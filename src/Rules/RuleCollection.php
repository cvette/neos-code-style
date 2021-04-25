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
    protected $rulesByToken = [];


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
        return $this->rulesByToken[$tokenType] ?? [];
    }

    public function current(): array
    {
        $path = key($this->rulesByToken);
        return $this->rulesByToken[$path];
    }

    public function next(): void
    {
        next($this->rulesByToken);
    }

    public function valid(): bool
    {
        return !(current($this->rulesByToken) === false);
    }

    public function rewind(): void
    {
        reset($this->rulesByToken);
    }

    public function key()
    {
        return key($this->rulesByToken);
    }
}
