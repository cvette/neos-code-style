<?php

declare(strict_types=1);

namespace Vette\Neos\CodeStyle\Files;

use Vette\Neos\CodeStyle\Lexer\TokenStream;
use Vette\Neos\CodeStyle\Packages\Package;

/**
 * Class File
 *
 * @package Vette\Neos\CodeStyle\Files
 */
class File
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $realPath;

    /**
     * @var Package|null
     */
    protected $package;

    /**
     * @var TokenStream
     */
    protected $tokenStream;

    /**
     * @var Error[]
     */
    protected $errors = [];


    /**
     * File constructor.
     *
     * @param string $path
     * @param Package|null $package
     */
    public function __construct(string $path, ?Package $package = null)
    {
        $this->path = $path;
        $this->realPath = realpath($path);
        $this->package = $package;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getRealPath(): string
    {
        return $this->realPath;
    }

    /**
     * @return Package|null
     */
    public function getPackage(): ?Package
    {
        return $this->package;
    }

    /**
     * @return TokenStream
     */
    public function getTokenStream(): TokenStream
    {
        return $this->tokenStream;
    }

    /**
     * @param TokenStream $tokenStream
     */
    public function setTokenStream(TokenStream $tokenStream): void
    {
        $this->tokenStream = $tokenStream;
    }

    public function addError(string $error, int $lineNumber, int $column, string $severity): void
    {
        $this->errors[] = new Error($error, $lineNumber, $column, $severity);
    }

    /**
     * @return Error[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
