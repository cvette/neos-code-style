<?php

declare(strict_types=1);

namespace Vette\Neos\CodeStyle\Rules\Neos;

use Vette\Neos\CodeStyle\Files\File;
use Vette\Neos\CodeStyle\Lexer\Token;
use Vette\Neos\CodeStyle\Rules\FusionRule;

/**
 * Class OperatorSpacingRule
 *
 * @package Vette\Neos\CodeStyle\Rules\Neos
 */
class OnePrototypePerFileRule extends FusionRule
{
    /**
     * @var int[]
     */
    protected array $tokenTypes = [
        Token::PROTOTYPE_KEYWORD_TYPE
    ];

    /**
     * @var bool[]
     */
    protected array $prototypesPerFile = [];


    public function process(int $tokenStreamIndex, File $file, int $level): void
    {
        if ($level !== 0 || !$this->isPrototypeDefinition($tokenStreamIndex, $file)) {
            return;
        }

        $stream = $file->getTokenStream();
        $token = $stream->getTokenAt($tokenStreamIndex);

        if (isset($this->prototypesPerFile[$file->getRealPath()])
            && $this->prototypesPerFile[$file->getRealPath()] === true) {
            $file->addError('Expecting only 1 prototype definition per file', $token->getLine(), $token->getColumn(), $this->severity);
        } else {
            $this->prototypesPerFile[$file->getRealPath()] = true;
        }
    }
}
