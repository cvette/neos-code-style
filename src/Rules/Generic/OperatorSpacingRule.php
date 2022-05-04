<?php

declare(strict_types=1);

namespace Vette\Neos\CodeStyle\Rules\Generic;

use Vette\Neos\CodeStyle\Files\File;
use Vette\Neos\CodeStyle\Lexer\Token;
use Vette\Neos\CodeStyle\Rules\Rule;

/**
 * Class OperatorSpacingRule
 *
 * @package Vette\Neos\CodeStyle\Rules\Generic
 */
class OperatorSpacingRule extends Rule
{
    /**
     * @var int[]
     */
    protected $tokenTypes = [
        Token::ASSIGNMENT_TYPE,
        Token::COPY_TYPE,
        Token::EEL_AND_OPERATOR_TYPE,
        Token::EEL_OR_OPERATOR_TYPE,
        Token::EEL_ADDITION_OPERATOR_TYPE,
        Token::EEL_SUBTRACTION_OPERATOR_TYPE,
        Token::EEL_MULTIPLICATION_OPERATOR_TYPE,
        Token::EEL_DIVISION_OPERATOR_TYPE,
        Token::EEL_MODULO_OPERATOR_TYPE
    ];

    public function process(int $tokenStreamIndex, File $file, int $level): void
    {
        $previous = $file->getTokenStream()->getTokenAt($tokenStreamIndex - 1);
        $next = $file->getTokenStream()->getTokenAt($tokenStreamIndex + 1);
        $current = $file->getTokenStream()->getTokenAt($tokenStreamIndex);

        if ($previous !== null && ($previous->getType() !== Token::WHITESPACE_TYPE || strlen($previous->getValue()) > 1)) {
            $error = 'Expecting exactly 1 space before operator';
            $file->addError($error, $current->getLine(), $current->getColumn(), $this->severity);
        }

        if ($next !== null && ($next->getType() !== Token::WHITESPACE_TYPE || strlen($next->getValue()) > 1)) {
            $error = 'Expecting exactly 1 space after operator';
            $file->addError($error, $current->getLine(), $current->getColumn(), $this->severity);
        }
    }
}
