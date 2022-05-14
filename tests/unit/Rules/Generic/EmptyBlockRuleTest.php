<?php

namespace Vette\Neos\CodeStyle\Rules\Generic;

use PHPUnit\Framework\TestCase;
use Vette\Neos\CodeStyle\Files\File;
use Vette\Neos\CodeStyle\Lexer\Token;
use Vette\Neos\CodeStyle\Lexer\TokenStream;

class EmptyBlockRuleTest extends TestCase
{

    /**
     * @dataProvider processProvider
     */
    public function testProcess(TokenStream $stream, int $expectedErrors)
    {
        $rule = new EmptyBlockRule();
        $rule->setOptions([]);

        $file = new File(__FILE__);
        $file->setTokenStream($stream);

        $rule->process(0, $file, 0);
        $this->assertCount($expectedErrors, $file->getErrors());
    }

    public function processProvider(): array
    {
        return [
            'empty block' => [
                new TokenStream([
                    new Token(Token::LBRACE_TYPE, '{', 1, 0, 0),
                    new Token(Token::RBRACE_TYPE, '}', 1, 0, 0),
                ]), 1
            ],
            'empty block with whitespace' => [
                new TokenStream([
                    new Token(Token::LBRACE_TYPE, '{', 1, 0, 0),
                    new Token(Token::LINE_BREAK, PHP_EOL, 1, 0, 0),
                    new Token(Token::WHITESPACE_TYPE, '  ', 1, 0, 0),
                    new Token(Token::RBRACE_TYPE, '}', 1, 0, 0),
                ]), 1
            ],
            'not empty block' => [
                new TokenStream([
                    new Token(Token::LBRACE_TYPE, '{', 1, 0, 0),
                    new Token(Token::LINE_BREAK, PHP_EOL, 1, 0, 0),
                    new Token(Token::OBJECT_PATH_PART_TYPE, 'test', 1, 0, 0),
                    new Token(Token::WHITESPACE_TYPE, '  ', 1, 0, 0),
                    new Token(Token::RBRACE_TYPE, '}', 1, 0, 0),
                ]), 0
            ],
        ];
    }
}
