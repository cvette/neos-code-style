<?php

declare(strict_types=1);

namespace Vette\Neos\CodeStyle\Rules\Generic;

use Vette\Neos\CodeStyle\Files\File;
use Vette\Neos\CodeStyle\Lexer\Token;
use Vette\Neos\CodeStyle\Rules\Rule;

/**
 * Class BlockSpacingRule
 *
 * @package Vette\Neos\CodeStyle\Rules\Generic
 */
class BlockSpacingRule extends Rule
{
    /**
     * @var int[]
     */
    protected array $tokenTypes = [
        Token::LBRACE_TYPE
    ];

    function process(int $tokenStreamIndex, File $file, int $level): void
    {
        $previous = $file->getTokenStream()->getTokenAt($tokenStreamIndex - 1);
        $current = $file->getTokenStream()->getTokenAt($tokenStreamIndex);

        if ($previous !== null && ($previous->getType() !== Token::WHITESPACE_TYPE || strlen($previous->getValue()) > 1)) {
            $error = 'Expecting exactly 1 space before opening brace';
            $file->addError($error, $current->getLine(), $current->getColumn(), $this->severity);
        }
    }
}
