<?php

declare(strict_types=1);

namespace Vette\Neos\CodeStyle\Rules\Neos;

use Vette\Neos\CodeStyle\Files\File;
use Vette\Neos\CodeStyle\Lexer\Token;
use Vette\Neos\CodeStyle\Rules\Rule;

/**
 * Checks for multiple ternary operators in one eel expression
 */
class TernaryOperatorRule extends Rule
{
    /**
     * @var int[]
     */
    protected $tokenTypes = [
        Token::EEL_IF_KEYWORD_TYPE
    ];

    function process(int $tokenStreamIndex, File $file, int $level): void
    {
        $firstOperator = $file->getTokenStream()->getTokenAt($tokenStreamIndex);

        if (!$firstOperator) {
            return;
        }

        $stream = $file->getTokenStream();
        $secondOperator = $stream->findNextToken($tokenStreamIndex + 1, Token::EEL_IF_KEYWORD_TYPE, Token::RBRACE_TYPE);

        if ($secondOperator instanceof Token && $secondOperator->getLine() === $firstOperator->getLine()) {
            $file->addError('Only one ternary operator should be used in an Eel expression', $firstOperator->getLine(),
                $firstOperator->getColumn(), $this->severity);
        }
    }
}
