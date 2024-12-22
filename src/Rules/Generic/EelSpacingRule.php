<?php

declare(strict_types=1);

namespace Vette\Neos\CodeStyle\Rules\Generic;

use Vette\Neos\CodeStyle\Files\File;
use Vette\Neos\CodeStyle\Lexer\Token;
use Vette\Neos\CodeStyle\Rules\Rule;

/**
 * Class EelSpacingRule
 *
 * @package Vette\Neos\CodeStyle\Rules\Generic
 */
class EelSpacingRule extends Rule
{
    /**
     * @var int[]
     */
    protected array $tokenTypes = [
        Token::EEL_START_TYPE,
        Token::EEL_END_TYPE
    ];

    function process(int $tokenStreamIndex, File $file, int $level): void
    {
        $current = $file->getTokenStream()->getTokenAt($tokenStreamIndex);
        if ($current === null) {
            return;
        }

        if ($current->getType() == Token::EEL_START_TYPE) {
            $next = $file->getTokenStream()->getTokenAt($tokenStreamIndex + 1);
            if ($next !== null && ($next->getType() == Token::WHITESPACE_TYPE)) {
                $error = 'Expecting no space after EEL start';
                $file->addError($error, $current->getLine(), $current->getColumn(), $this->severity);
            }
        }

        if ($current->getType() == Token::EEL_END_TYPE) {
            $previous = $file->getTokenStream()->getTokenAt($tokenStreamIndex - 1);
            if ($previous !== null && ($previous->getType() == Token::WHITESPACE_TYPE)) {
                $error = 'Expecting no space before EEL end';
                $file->addError($error, $current->getLine(), $current->getColumn(), $this->severity);
            }
        }
    }
}
