<?php

declare(strict_types=1);

namespace Vette\Neos\CodeStyle\Rules\Neos;

use Vette\Neos\CodeStyle\Files\File;
use Vette\Neos\CodeStyle\Lexer\Token;
use Vette\Neos\CodeStyle\Rules\Rule;

/**
 * Checks whether `@context` was used on the first level of a prototype
 */
class ContextInFirstLevelRule extends Rule
{

    protected $tokenTypes = [
        Token::META_PROPERTY_KEYWORD_TYPE
    ];

    function process(int $tokenStreamIndex, File $file, int $level): void
    {
        if ($level !== 1) {
            return;
        }

        $prevToken = $file->getTokenStream()->getTokenAt($tokenStreamIndex - 1);

        if ($prevToken && $prevToken->getType() === Token::DOT_TYPE) {
            return;
        }

        $keyword = $file->getTokenStream()->getTokenAt($tokenStreamIndex + 1);

        if ($keyword instanceof Token && $keyword->getValue() === 'context') {
            $file->addError('No `@context` should be used in the first level of a prototype', $keyword->getLine(),
                $keyword->getColumn(), $this->severity);
        }
    }
}
