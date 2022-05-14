<?php

namespace Vette\Neos\CodeStyle\Rules\Generic;

use PHPUnit\Framework\TestCase;
use Vette\Neos\CodeStyle\Files\File;
use Vette\Neos\CodeStyle\Lexer\Token;
use Vette\Neos\CodeStyle\Lexer\TokenStream;

class EelSpacingRuleTest extends TestCase
{
    /**
     * @dataProvider processProvider
     */
    public function testProcess(TokenStream $stream, int $offset, int $expectedErrors)
    {
        $rule = new EelSpacingRule();
        $rule->setOptions([]);

        $file = new File(__FILE__);
        $file->setTokenStream($stream);

        $rule->process($offset, $file, 0);
        $this->assertCount($expectedErrors, $file->getErrors());
    }

    public function processProvider(): array
    {
        return [
            'space after start type' => [
                new TokenStream([
                    new Token(Token::EEL_START_TYPE, '${', 1, 0, 0),
                    new Token(Token::WHITESPACE_TYPE, '  ', 1, 0, 0),
                ]), 0, 1
            ],
            'space before end type' => [
                new TokenStream([
                    new Token(Token::WHITESPACE_TYPE, '  ', 1, 0, 0),
                    new Token(Token::EEL_END_TYPE, '}', 1, 0, 0)
                ]), 1, 1
            ],
            'no space after start type' => [
                new TokenStream([
                    new Token(Token::EEL_START_TYPE, '${', 1, 0, 0),
                    new Token(Token::EEL_IDENTIFIER_TYPE, 'test', 1, 0, 0),
                ]), 0, 0
            ],
            'no space before end type' => [
                new TokenStream([
                    new Token(Token::EEL_IDENTIFIER_TYPE, 'test', 1, 0, 0),
                    new Token(Token::EEL_END_TYPE, '}', 1, 0, 0)
                ]), 1, 0
            ],
        ];
    }
}
