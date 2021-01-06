<?php

declare(strict_types=1);

namespace Vette\Neos\CodeStyle\Rules;

use Iterator;

/**
 * Class RuleCollection
 *
 * @package Vette\Neos\CodeStyle\Packages
 */
class RuleCollection implements Iterator
{

    /**
     * @var array[]
     */
    protected array $rulesByToken = [];


    /**
     * PackageCollection constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param Rule $rule
     *
     * @return void
     */
    public function addRule(Rule $rule): void
    {
        foreach ($rule->getTokenTypes() as $tokenType) {
            if (!isset($this->rulesByToken[$tokenType])) {
                $this->rulesByToken[$tokenType] = [];
            }

            $this->rulesByToken[$tokenType][] = $rule;
        }
    }

    /**
     * @param int $tokenType
     * @return Rule[]
     */
    public function getRulesByTokenType(int $tokenType): array
    {
        if (isset($this->rulesByToken[$tokenType])) {
            return $this->rulesByToken[$tokenType];
        }

        return [];
    }

    public function current()
    {
        $path = key($this->rulesByToken);
        return $this->rulesByToken[$path];
    }

    public function next()
    {
        next($this->rulesByToken);
    }

    public function valid()
    {
        if (current($this->rulesByToken) === false) {
            return false;
        }

        return true;
    }

    public function rewind()
    {
        reset($this->rulesByToken);
    }

    public function key()
    {
        return key($this->rulesByToken);
    }
}
