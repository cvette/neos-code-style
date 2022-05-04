<?php

declare(strict_types=1);

namespace Vette\Neos\CodeStyle\Rules\Neos;

use Vette\Neos\CodeStyle\Files\File;
use Vette\Neos\CodeStyle\Lexer\Token;
use Vette\Neos\CodeStyle\Rules\Rule;

/**
 * Class FluidTemplateRule
 *
 * @package Vette\Neos\CodeStyle\Rules\Standard
 */
class FluidTemplateRule extends Rule
{
    /**
     * @var int[]
     */
    protected $tokenTypes = [
        Token::OBJECT_IDENTIFIER_TYPE
    ];

    public function process(int $tokenStreamIndex, File $file, int $level): void
    {
        $namespace = $file->getTokenStream()->getTokenAt($tokenStreamIndex);
        $colon = $file->getTokenStream()->getTokenAt($tokenStreamIndex + 1);
        $identifier = $file->getTokenStream()->getTokenAt($tokenStreamIndex + 2);

        if ($colon instanceof Token
            && $colon->getType() === Token::COLON_TYPE
            && $identifier instanceof Token
            && $identifier->getType() === Token::OBJECT_IDENTIFIER_TYPE
            && $identifier->getValue() === 'Template'
            && $namespace instanceof Token
            && $namespace->getValue() === 'Neos.Fusion') {

            $file->addError('AFX should be used instead of Fluid templates', $namespace->getLine(), $namespace->getColumn(), $this->severity);
        }
    }
}
