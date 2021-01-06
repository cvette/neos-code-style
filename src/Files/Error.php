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
    public const SEVERITY_INFO = 'INFO';
    public const SEVERITY_WARNING = 'warning';
    public const SEVERITY_ERROR = 'error';

    /**
     * @var string
     */
    protected string $message;

    /**
     * @var int
     */
    protected int $lineNumber;

    /**
     * @var string
     */
    protected string $severity;


    /**
     * Error constructor.
     *
     * @param string $message
     * @param int $lineNumber
     * @param string $severity
     */
    public function __construct(string $message, int $lineNumber, string $severity = Error::SEVERITY_INFO)
    {
        $this->message = $message;
        $this->lineNumber = $lineNumber;
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
     * @return string
     */
    public function getSeverity(): string
    {
        return $this->severity;
    }
}
