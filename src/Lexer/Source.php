<?php

declare(strict_types=1);

namespace Vette\Neos\CodeStyle\Lexer;

/**
 * Class Source
 *
 * @package Vette\FusionParser
 */
final class Source
{
    /** @var string */
    private $code;

    /** @var string */
    private $name;

    /** @var string */
    private $path;


    /**
     * @param string $code The template source code
     * @param string $name The template logical name
     * @param string $path The filesystem path of the template if any
     */
    public function __construct(string $code, string $name, string $path = '')
    {
        $this->code = $code;
        $this->name = $name;
        $this->path = $path;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
