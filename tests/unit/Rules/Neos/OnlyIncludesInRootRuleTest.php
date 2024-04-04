<?php

namespace Vette\Neos\CodeStyle\Rules\Generic;

use PHPUnit\Framework\TestCase;
use Vette\Neos\CodeStyle\Files\File;
use Vette\Neos\CodeStyle\Lexer\Token;
use Vette\Neos\CodeStyle\Lexer\TokenStream;
use Vette\Neos\CodeStyle\Rules\Neos\OnlyIncludesInRootRule;

class OnlyIncludesInRootRuleTest extends TestCase
{

    /**
     * @dataProvider processProvider
     */
    public function testProcess(TokenStream $stream, int $expectedErrors)
    {
        $rule = new OnlyIncludesInRootRule();
        $file = new File('Root.fusion');

        $file->setTokenStream($stream);
        $rule->process(0, $file, 0);

        $this->assertCount($expectedErrors, $file->getErrors());
    }

    public function processProvider(): array
    {
        return [
            'not only includes' => [
                new TokenStream([
                    new Token(Token::FILE_START_TYPE, '', 0, 0, 0),
                    new Token(Token::PROTOTYPE_KEYWORD_TYPE, 'prototype', 0, 0, 0),
                    new Token(Token::LPAREN_TYPE, '(', 0, 0, 0),
                    new Token(Token::OBJECT_IDENTIFIER_TYPE, 'Test', 0, 0, 0),
                    new Token(Token::RPAREN_TYPE, ')', 0, 0, 0)
                ]), 1
            ],
            'only includes' => [
                new TokenStream([
                    new Token(Token::FILE_START_TYPE, '', 0, 0, 0),
                    new Token(Token::INCLUDE_KEYWORD_TYPE, 'include', 0, 0, 0)
                ]), 0
            ]
        ];
    }
}
