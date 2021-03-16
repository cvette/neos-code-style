<?php

declare(strict_types=1);

namespace Vette\Neos\CodeStyle\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Vette\Neos\CodeStyle\Parameters;
use Vette\Neos\CodeStyle\CodeStyle;
use Exception;

/**
 * Class RunCommand
 *
 * @package Vette\Neos\CodeStyle\Console\Command
 */
class RunCommand extends Command
{

    protected static $defaultName = 'run';


    /**
     * Configure
     *
     * @return void
     */
    protected function configure()
    {
        $this->addOption('neosRoot', 'nr', InputOption::VALUE_OPTIONAL,'The root directory of a Neos distribution');
        $this->addOption('config','c',InputOption::VALUE_OPTIONAL);
        $this->addOption('ruleSet','s',InputOption::VALUE_OPTIONAL);
        $this->addOption('report','r',InputOption::VALUE_OPTIONAL);

        $this->addArgument('files', InputArgument::IS_ARRAY,'The directories to check');
    }

    /**
     * Execute
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $params = new Parameters();

        $files = $input->getArgument('files');
        $params->setFiles($files);

        $params->setNeosRoot($input->getOption('neosRoot'));
        $params->setConfigFile($input->getOption('config'));
        $params->setRuleSet($input->getOption('ruleSet'));

        $codeStyle = new CodeStyle($params);
        $codeStyle->run();

        return Command::SUCCESS;
    }
}