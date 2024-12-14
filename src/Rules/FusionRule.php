<?php

declare(strict_types=1);

namespace Vette\Neos\CodeStyle\Rules;

use Vette\Neos\CodeStyle\Files\File;
use Vette\Neos\CodeStyle\Lexer\Token;

/**
 * Class Rule
 *
 * @package Vette\Neos\CodeStyle\Rules
 */
abstract class FusionRule extends Rule
{
    /**
     * Check if token is first on line
     * Ignores whitespace and newlines
     *
     * @param int $tokenStreamIndex
     * @param File $file
     * @return bool
     */
    protected function isFirstOnLine(int $tokenStreamIndex, File $file): bool
    {
        $stream = $file->getTokenStream();
        $prev = $stream->getTokenAt($tokenStreamIndex - 1);
        $prevPrev = $stream->getTokenAt($tokenStreamIndex - 2);

        if ($prev === null || $prevPrev === null) {
            return false;
        }

        // check if prototype is first token on line
        return ($prev->getType() === Token::FILE_START_TYPE
            || $prev->getType() === Token::LINE_BREAK
            || $prev->getType() === Token::WHITESPACE_TYPE
            && ($prevPrev->getType() === Token::FILE_START_TYPE || $prevPrev->getType() === Token::LINE_BREAK));
    }

    /**
     * Check if token is part of a prototype definition
     *
     * @param int $tokenStreamIndex
     * @param File $file
     * @return bool
     */
    protected function isPrototypeDefinition(int $tokenStreamIndex, File $file):bool
    {
        $stream = $file->getTokenStream();
        // Check if this prototype is being unset or is part of an assignment
        return $this->isFirstOnLine($tokenStreamIndex, $file)
            && $stream->findNextToken($tokenStreamIndex, Token::UNSET_TYPE, Token::LINE_BREAK) === null
            && $stream->findNextToken($tokenStreamIndex, Token::ASSIGNMENT_TYPE, Token::LINE_BREAK) === null
            && $stream->findNextToken($tokenStreamIndex, Token::OBJECT_PATH_PART_TYPE, Token::LINE_BREAK) === null;
    }
}
