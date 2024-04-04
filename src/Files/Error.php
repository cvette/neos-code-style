<?php

declare(strict_types=1);

namespace Vette\Neos\CodeStyle\Files;

/**
 * Class Error
 *
 * @package Vette\Neos\CodeStyle\Files
 */
class Error
{
    public const SEVERITY_INFO = 'info';
    public const SEVERITY_WARNING = 'warning';
    public const SEVERITY_ERROR = 'error';

    protected string $message;

    protected int $lineNumber;

    protected int $column;

    protected string $severity;


    /**
     * Error constructor.
     *
     * @param string $message
     * @param int $lineNumber
     * @param int $column
     * @param string $severity
     */
    public function __construct(string $message, int $lineNumber, int $column, string $severity = Error::SEVERITY_INFO)
    {
        $this->message = $message;
        $this->lineNumber = $lineNumber;
        $this->column = $column;
        $this->severity = $severity;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return int
     */
    public function getLineNumber(): int
    {
        return $this->lineNumber;
    }

    /**
     * @return int
     */
    public function getColumn(): int
    {
        return $this->column;
    }

    /**
     * @return string
     */
    public function getSeverity(): string
    {
        return $this->severity;
    }
}
