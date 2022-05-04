<?php

declare(strict_types=1);

namespace Vette\Neos\CodeStyle\Rules\Neos;

use Vette\Neos\CodeStyle\Files\File;
use Vette\Neos\CodeStyle\Lexer\Token;
use Vette\Neos\CodeStyle\Rules\Rule;

/**
 * Class OperatorSpacingRule
 *
 * @package Vette\Neos\CodeStyle\Rules\Neos
 */
class OnlyIncludesInRootRule extends Rule
{
    /**
     * @var int[]
     */
    protected $tokenTypes = [
        Rule::FILE_START_TOKEN_TYPE
    ];

    protected const ALLOWED_TOKEN_TYPES = [
        Token::INCLUDE_KEYWORD_TYPE,
        Token::INCLUDE_VALUE_TYPE,
        Token::WHITESPACE_TYPE,
        Token::LINE_BREAK,
        Token::EOF_TYPE
    ];

    public function process(int $tokenStreamIndex, File $file, int $level): void
    {
        if (basename($file->getPath()) !== 'Root.fusion') {
            return;
        }

        for ($i = 0; $i < $file->getTokenStream()->count(); $i++) {
            $token = $file->getTokenStream()->getTokenAt($i);
            if ($token instanceof Token && !in_array($token->getType(), self::ALLOWED_TOKEN_TYPES)) {
                $file->addError('The Root.fusion file should only include other files', $token->getLine(), $token->getColumn(), $this->severity);
                return;
            }
        }
    }
}
