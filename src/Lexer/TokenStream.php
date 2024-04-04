<?php

declare(strict_types=1);

namespace Vette\Neos\CodeStyle\Lexer;

use Countable;
use Iterator;

/**
 * Class TokenStream
 *
 * @package Vette\FusionParser
 */
class TokenStream implements Iterator, Countable
{
    /** @var Token[] */
    private array $tokens;

    private ?Source $source;

    private int $pointer;

    const WHITESPACE_TOKEN_TYPES = [Token::WHITESPACE_TYPE, Token::LINE_BREAK];


    /**
     * TokenStream constructor.
     *
     * @param array<Token> $tokens
     * @param Source|null $source
     */
    public function __construct(array $tokens, ?Source $source = null)
    {
        $this->pointer = 0;
        $this->tokens = $tokens;
        $this->source = $source;

        if ($source === null) {
            $this->source = new Source('', '');
        }
    }

    public function getTokenAt(int $index): ?Token
    {
        if (!isset($this->tokens[$index])) {
            return null;
        }

        return $this->tokens[$index];
    }

    /**
     * @param int $offset
     * @return Token|null
     */
    public function findNextNonWhitespaceToken(int $offset): ?Token
    {
        for ($i = $offset; $i < count($this->tokens); $i++) {
            if (!in_array($this->tokens[$i]->getType(), self::WHITESPACE_TOKEN_TYPES)) {
                return $this->tokens[$i];
            }
        }

        return null;
    }

    /**
     * @param int $offset
     * @param int $tokenType
     * @param int $untilTokenType
     * @return Token|null
     */
    public function findNextToken(int $offset, int $tokenType, int $untilTokenType): ?Token
    {
        for ($i = $offset; $i < count($this->tokens); $i++) {
            if ($this->tokens[$i]->getType() === $untilTokenType) {
                return null;
            }

            if ($this->tokens[$i]->getType() === $tokenType) {
                return $this->tokens[$i];
            }
        }

        return null;
    }

    public function getPointer(): int
    {
        return $this->pointer;
    }

    public function current(): Token
    {
        return $this->tokens[$this->pointer];
    }

    public function next(): void
    {
        $this->pointer++;
    }

    public function key(): int
    {
        return $this->pointer;
    }

    public function valid(): bool
    {
        return isset($this->tokens[$this->pointer]);
    }

    public function rewind(): void
    {
        $this->pointer = 0;
    }

    public function getSource(): ?Source
    {
        return $this->source;
    }

    public function count(): int
    {
        return count($this->tokens);
    }
}
