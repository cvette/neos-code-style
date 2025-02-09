<?php

declare(strict_types=1);

namespace Vette\Neos\CodeStyle\Rules\Neos;

use Vette\Neos\CodeStyle\Files\File;
use Vette\Neos\CodeStyle\Lexer\Token;
use Vette\Neos\CodeStyle\Rules\FusionRule;

/**
 * Class PrototypeNamePrefixRule
 *
 * @package Vette\Neos\CodeStyle\Rules\Neos
 */
class NodePropertiesRule extends FusionRule
{
    protected const EEL_IDENTIFIER_VALUES = ['node', 'documentNode', 'site'];

    /**
     * @var int[]
     */
    protected array $tokenTypes = [
        Token::EEL_IDENTIFIER_TYPE
    ];


    public function process(int $tokenStreamIndex, File $file, int $level): void
    {
        $identifierToken = $file->getTokenStream()->getTokenAt($tokenStreamIndex);
        if ($identifierToken === null) {
            return;
        }

        if (in_array($identifierToken->getValue(),self::EEL_IDENTIFIER_VALUES)) {
            $nextToken = $file->getTokenStream()->findNextNonWhitespaceToken($tokenStreamIndex + 1);
            if ($nextToken?->getType() !== Token::EEL_IDENTIFIER_SEPARATOR_TYPE) {
                return;
            }

            $nextToken = $file->getTokenStream()->findNextNonWhitespaceToken($tokenStreamIndex + 2);
            if ($nextToken?->getType() === Token::EEL_IDENTIFIER_TYPE && $nextToken?->getValue() === 'properties') {
                $file->addError('Node properties should only be accessed individually via "q(node).property()"', $identifierToken->getLine(), $identifierToken->getColumn(), $this->severity);
            }
        }
    }
}
