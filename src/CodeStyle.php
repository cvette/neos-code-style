<?php

declare(strict_types=1);

namespace Vette\Neos\CodeStyle;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Yaml;
use Vette\Neos\CodeStyle\Files\Error;
use Vette\Neos\CodeStyle\Files\File;
use Vette\Neos\CodeStyle\Files\FileCollection;
use Vette\Neos\CodeStyle\Packages\PackageCollection;
use Vette\Neos\CodeStyle\Reports\Report;
use Vette\Neos\CodeStyle\Rules\Rule;
use Vette\Neos\CodeStyle\Rules\RuleCollection;
use Vette\FusionParser\Lexer;
use Vette\FusionParser\LexerException;
use Vette\FusionParser\Source;
use Vette\FusionParser\Token;
use Vette\FusionParser\TokenStream;
use Exception;

/**
 * Class CodeStyle
 *
 * @package Vette\Neos\CodeStyle
 */
class CodeStyle
{
    protected FileCollection $fileCollection;
    protected PackageCollection $packageCollection;
    protected RuleCollection $ruleCollection;
    protected Report $report;
    protected array $config;


    /**
     * CodeStyle constructor.
     *
     * @param Parameters $parameters
     */
    public function __construct(Parameters $parameters)
    {
        $configFiles = [YAML::parseFile(__DIR__ . '/config.yaml')];

        // Override default config with config file specified via command option
        $userConfigFile = $parameters->getConfigFile();
        if (is_string($userConfigFile)) {
            $configFiles[] = YAML::parseFile($userConfigFile);
        }

        $processor = new Processor();
        $this->config = $processor->processConfiguration(new CodeStyleConfiguration(), $configFiles);

        if ($parameters->getRuleset()) {
            $this->config['defaultRuleSet'] = $parameters->getRuleset();
        }

        if ($parameters->getReport()) {
            $this->config['defaultReport'] = $parameters->getReport();
        }

        if (!empty($parameters->getFiles())) {
            $this->config['files'] = $parameters->getFiles();
        }

        if ($parameters->getNeosRoot()) {
            $this->config['neosRoot'] = $parameters->getNeosRoot();
        }
    }

    /**
     * Run linter
     *
     * @throws Exception
     *
     * @return void
     */
    public function run(): void
    {
        $this->packageCollection = new PackageCollection($this->config['neosRoot']);
        $this->fileCollection = new FileCollection($this->config['files'], $this->packageCollection);

        $this->initRules();
        $this->initReport();

        foreach ($this->fileCollection as $file) {
            $this->processFile($file);
            $this->report->reportFile($file);
        }

        echo $this->report->generate();
    }

    /**
     * @throws Exception
     */
    protected function initReport(): void
    {
        $defaultReport = $this->config['defaultReport'];
        $this->config['reports'][$defaultReport];

        if (!isset($this->config['reports'][$defaultReport])) {
            throw new Exception('report is not defined: ' . $defaultReport);
        }

        $className = $this->config['reports'][$defaultReport]['class'];
        if (!class_exists($className)) {
            throw new Exception('class does not exist: ' . $className);
        }

        $report = new $className();
        if (!$report instanceof Report) {
            throw new Exception('class does extend Report: ' . $className);
        }

        $this->report = $report;
    }

    /**
     * Init Rules
     *
     * @throws Exception
     *
     * @return void
     */
    protected function initRules(): void
    {
        $this->ruleCollection = new RuleCollection();

        $ruleSet = $this->config['defaultRuleSet'];
        if (!isset($this->config['ruleSets'][$ruleSet])) {
            throw new Exception('unknown ruleset: ' . $ruleSet);
        }

        $rules = array_unique($this->visitRuleSet($ruleSet));
        foreach ($rules as $ruleName) {
            if (!isset($this->config['rules'][$ruleName])) {
                throw new Exception('rule is not defined: ' . $ruleName);
            }

            $className = $this->config['rules'][$ruleName]['class'];
            if (!class_exists($className)) {
                throw new Exception('class does not exist: ' . $className);
            }

            $rule = new $className();
            if (!$rule instanceof Rule) {
                throw new Exception('class does extend Rule: ' . $className);
            }

            $options = $this->config['rules'][$ruleName]['options'];
            if (is_array($options)) {
                $rule->setOptions($options);
            }

            $severity = $this->config['rules'][$ruleName]['severity'];
            $rule->setSeverity($severity);

            $this->ruleCollection->addRule($rule);
        }
    }

    /**
     * Visit rule set
     *
     * @param string $ruleSet
     * @param array $rules
     * @param array $alreadyVisited
     *
     * @return array
     * @throws Exception
     */
    protected function visitRuleSet(string $ruleSet, array &$rules = [], array &$alreadyVisited = []): array
    {
        $rules = array_merge($this->config['ruleSets'][$ruleSet]['rules'], $rules);
        $includes = $this->config['ruleSets'][$ruleSet]['include'];
        foreach ($includes as $include) {
            if (!isset($this->config['ruleSets'][$include])) {
                throw new Exception('unknown ruleset: ' . $include);
            }

            if (in_array($include, $alreadyVisited)) {
                throw new Exception('recursive ruleset include: ' . $include . ' in ' . $ruleSet);
            }

            $alreadyVisited[] = $include;
            $this->visitRuleSet($include, $rules, $alreadyVisited);
        }

        return $rules;
    }

    /**
     * Process file
     *
     * @param File $file
     *
     * @return void
     */
    protected function processFile(File $file): void
    {
        $lexer = new Lexer(false);

        try {
            $source = new Source(file_get_contents($file->getRealPath()), basename($file->getRealPath()), $file->getRealPath());
            $tokenStream = $lexer->tokenize($source);
            $file->setTokenStream($tokenStream);

            $this->applyRules($this->ruleCollection, $tokenStream, Rule::FILE_START_TOKEN_TYPE, $file, 0);

            $level = 0;
            foreach ($tokenStream as $token) {
                $this->applyRules($this->ruleCollection, $tokenStream, $token->getType(), $file, $level);

                // calculate current nesting level
                if ($token->getType() === Token::LBRACE_TYPE) {
                    $level++;
                } elseif ($token->getType() === Token::RBRACE_TYPE) {
                    $level--;
                }
            }
        } catch (LexerException $exception) {
            $file->addError($exception->getMessage(), $exception->getLine(), $exception->getPosition(), Error::SEVERITY_ERROR);
        }
    }

    /**
     * Apply rules
     *
     * @param RuleCollection $ruleCollection
     * @param TokenStream $tokenStream
     * @param int $tokenType
     * @param File $file
     * @param int $level
     *
     * @return void
     */
    protected function applyRules(RuleCollection $ruleCollection, TokenStream $tokenStream, int $tokenType, File $file, int $level): void
    {
        $rules = $ruleCollection->getRulesByTokenType($tokenType);
        foreach ($rules as $rule) {
            $rule->process($tokenStream->getPointer(), $file, $level);
        }
    }
}
