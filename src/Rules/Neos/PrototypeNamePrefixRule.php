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
class PrototypeNamePrefixRule extends FusionRule
{

    /**
     * @var int[]
     */
    protected array $tokenTypes = [
        Token::PROTOTYPE_KEYWORD_TYPE
    ];


    public function process(int $tokenStreamIndex, File $file, int $level): void
    {
        if ($level !== 0 || !$this->isPrototypeDefinition($tokenStreamIndex, $file)) {
            return;
        }

        $stream = $file->getTokenStream();
        $firstIdentifier = $stream->findNextToken($tokenStreamIndex + 1, Token::OBJECT_IDENTIFIER_TYPE, Token::RPAREN_TYPE);
        $secondIdentifier = $stream->findNextToken($tokenStreamIndex + 3, Token::OBJECT_IDENTIFIER_TYPE, Token::RPAREN_TYPE);

        $prototypeName = $secondIdentifier ?: $firstIdentifier;
        if ($prototypeName === null) {
            return;
        }

        if (in_array($firstIdentifier->getValue(), $this->getOption('ignorePackages'), true)) {
            return;
        }

        $prototypeNameParts = explode('.', $prototypeName->getValue());
        if (!in_array(reset($prototypeNameParts), $this->getOption('validPrefixes'))) {
            $file->addError('Prototype name should start with: ' . implode(', ', $this->getOption('validPrefixes')), $firstIdentifier->getLine(), $firstIdentifier->getColumn(), $this->severity);
        }
    }
}
