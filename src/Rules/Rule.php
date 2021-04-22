<?php

declare(strict_types=1);

namespace Vette\Neos\CodeStyle\Rules;

use Vette\Neos\CodeStyle\Files\Error;
use Vette\Neos\CodeStyle\Files\File;

/**
 * Class Rule
 *
 * @package Vette\Neos\CodeStyle\Rules
 */
abstract class Rule
{
    public const FILE_START_TOKEN_TYPE = 99;

    /**
     * @var int[]
     */
    protected $tokenTypes;

    /**
     * @var array
     */
    protected $options;

    protected $severity = Error::SEVERITY_INFO;


    /**
     * Gets the token types this rule should be applied to
     *
     * @return int[]
     */
    public function getTokenTypes(): array
    {
        return $this->tokenTypes;
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function getOption(string $key)
    {
        return $this->options[$key] ?? null;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    /**
     * @return string
     */
    public function getSeverity(): string
    {
        return $this->severity;
    }

    /**
     * @param string $severity
     */
    public function setSeverity(string $severity): void
    {
        $this->severity = $severity;
    }

    /**
     * Process
     *
     * @param int $tokenStreamIndex
     * @param File $file
     * @param int $level
     */
    abstract public function process(int $tokenStreamIndex, File $file, int $level): void;
}
