<?php

namespace Vette\Neos\CodeStyle\Rules\Generic;

use PHPUnit\Framework\TestCase;
use Vette\Neos\CodeStyle\Files\File;
use Vette\Neos\CodeStyle\Lexer\Token;
use Vette\Neos\CodeStyle\Lexer\TokenStream;

class BlockSpacingRuleTest extends TestCase
{
    /**
     * @dataProvider processProvider
     */
    public function testProcess(TokenStream $stream, int $offset, int $expectedErrors)
    {
        $rule = new BlockSpacingRule();
        $rule->setOptions([]);

        $file = new File(__FILE__);
        $file->setTokenStream($stream);

        $rule->process($offset, $file, 0);
        $this->assertCount($expectedErrors, $file->getErrors());
    }

    public function processProvider(): array
    {
        return [
            'single space before brace' => [
                new TokenStream([
                    new Token(Token::WHITESPACE_TYPE, ' ', 1, 0, 0),
                    new Token(Token::LBRACE_TYPE, '{', 1, 0, 0),
                ]), 0, 0
            ],
            'no space before brace' => [
                new TokenStream([
                    new Token(Token::OBJECT_IDENTIFIER_TYPE, 'test', 1, 0, 0),
                    new Token(Token::LBRACE_TYPE, '{', 1, 0, 0)
                ]), 1, 1
            ],
            'multiple spaces before brace' => [
                new TokenStream([
                    new Token(Token::WHITESPACE_TYPE, '   ', 1, 0, 0),
                    new Token(Token::LBRACE_TYPE, '{', 1, 0, 0)
                ]), 0, 0
            ],
        ];
    }
}
