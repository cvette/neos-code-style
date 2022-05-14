<?php

namespace Vette\Neos\CodeStyle\Rules\Generic;

use PHPUnit\Framework\TestCase;
use Vette\Neos\CodeStyle\Files\File;
use Vette\Neos\CodeStyle\Lexer\Token;
use Vette\Neos\CodeStyle\Lexer\TokenStream;

class OperatorSpacingRuleTest extends TestCase
{

    /**
     * @dataProvider processProvider
     */
    public function testProcess(TokenStream $stream, int $expectedErrors)
    {
        $rule = new OperatorSpacingRule();
        $rule->setOptions([]);

        $file = new File(__FILE__);
        $file->setTokenStream($stream);

        $rule->process(1, $file, 0);
        $this->assertCount($expectedErrors, $file->getErrors());
    }

    public function processProvider(): array
    {
        return [
            'assignment' => [
                new TokenStream([
                    new Token(Token::WHITESPACE_TYPE, '   ', 1, 0, 0),
                    new Token(Token::ASSIGNMENT_TYPE, '=', 1, 0, 0),
                    new Token(Token::WHITESPACE_TYPE, '  ', 1, 0, 0),
                ]), 2
            ],
            'modulo' => [
                new TokenStream([
                    new Token(Token::EEL_IDENTIFIER_TYPE, 'test', 1, 0, 0),
                    new Token(Token::EEL_MODULO_OPERATOR_TYPE, '%', 1, 0, 0),
                    new Token(Token::WHITESPACE_TYPE, ' ', 1, 0, 0),
                ]), 1
            ]
        ];
    }
}
