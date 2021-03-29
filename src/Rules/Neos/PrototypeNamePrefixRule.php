<?php

declare(strict_types=1);

namespace Vette\Neos\CodeStyle\Rules\Neos;

use Vette\Neos\CodeStyle\Files\File;
use Vette\FusionParser\Token;
use Vette\Neos\CodeStyle\Rules\FusionRule;

/**
 * Class PrototypeNamePrefixRule
 *
 * @package Vette\Neos\CodeStyle\Rules\Neos
 */
class PrototypeNamePrefixRule extends FusionRule
{

    /**
     * @var int[]
     */
    protected array $tokenTypes = [
        Token::PROTOTYPE_KEYWORD_TYPE
    ];


    function process(int $tokenStreamIndex, File $file, int $level): void
    {
        if ($level !== 0 || !$this->isPrototypeDefinition($tokenStreamIndex, $file)) {
            return;
        }

        $stream = $file->getTokenStream();
        $firstIdentifier = $stream->findNextToken($tokenStreamIndex + 1, Token::OBJECT_IDENTIFIER_TYPE, Token::RPAREN_TYPE);
        $secondIdentifier = $stream->findNextToken($tokenStreamIndex + 3, Token::OBJECT_IDENTIFIER_TYPE, Token::RPAREN_TYPE);

        $prototypeName = $secondIdentifier ? $secondIdentifier : $firstIdentifier;
        if ($prototypeName === null) {
            return;
        }

        $prototypeNameParts = explode('.', $prototypeName->getValue());
        if (!in_array(reset($prototypeNameParts), $this->getOption('validPrefixes'))) {
            $file->addError('Prototype name should start with: ' . join(', ', $this->getOption('validPrefixes')), $firstIdentifier->getLine(), $firstIdentifier->getColumn(), $this->severity);
        }
    }
}
