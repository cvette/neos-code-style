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
class EmptyBlockRule extends Rule
{
    /**
     * @var int[]
     */
    protected array $tokenTypes = [
        Token::LBRACE_TYPE
    ];

    public function process(int $tokenStreamIndex, File $file, int $level): void
    {
        $nextNonWhitespaceToken = $file->getTokenStream()->findNextNonWhitespaceToken($tokenStreamIndex + 1);
        if ($nextNonWhitespaceToken instanceof Token && $nextNonWhitespaceToken->getType() === Token::RBRACE_TYPE) {
            $current = $file->getTokenStream()->getTokenAt($tokenStreamIndex);
            $file->addError('Empty block found', $current?->getLine() ?? 0,$current?->getColumn() ?? 0, $this->severity);
        }
    }
}
