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
    protected string $path;

    protected string $realPath;

    protected ?Package $package;

    protected TokenStream $tokenStream;

    /**
     * @var Error[]
     */
    protected array $errors = [];


    /**
     * File constructor.
     *
     * @param string $path
     * @param Package|null $package
     */
    public function __construct(string $path, ?Package $package = null)
    {
        $realPath = realpath($path);

        $this->path = $path;
        $this->realPath = $realPath === false ? '' : $realPath;
        $this->package = $package;
    }

    public function getPath(): string
    {
        return $this->path;
    }

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

    public function getTokenStream(): TokenStream
    {
        return $this->tokenStream;
    }

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
