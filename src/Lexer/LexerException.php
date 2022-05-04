<?php

declare(strict_types=1);

namespace Vette\Neos\CodeStyle\Lexer;

use LogicException;

/**
 * Class LexerException
 *
 * @package Vette\FusionParser
 */
class LexerException extends LogicException
{
    /** @var string */
    protected $character;

    /** @var int */
    protected $position;


    /**
     * LexerException constructor.
     *
     * @param int $lineNumber
     * @param int $position
     * @param string $character
     * @param string $message
     */
    public function __construct(int $lineNumber, int $position, string $character, string $message = "")
    {
        $this->line = $lineNumber;
        $this->position = $position;
        $this->character = $character;

        parent::__construct($message);
    }

    /**
     * @return string
     */
    public function getCharacter(): string
    {
        return $this->character;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }
}
