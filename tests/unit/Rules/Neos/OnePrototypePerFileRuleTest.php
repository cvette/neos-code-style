<?php

namespace Vette\Neos\CodeStyle\Rules\Neos;

use PHPUnit\Framework\TestCase;
use Vette\Neos\CodeStyle\Files\File;
use Vette\Neos\CodeStyle\Lexer\Token;
use Vette\Neos\CodeStyle\Lexer\TokenStream;

class OnePrototypePerFileRuleTest extends TestCase
{

    /**
     * @dataProvider processProvider
     */
    public function testProcess(TokenStream $stream, array $keywordPositions, bool $shouldHaveError)
    {
        $rule = new OnePrototypePerFileRule();
        $rule->setOptions([]);

        $file = new File(__FILE__);
        $file->setTokenStream($stream);

        foreach ($keywordPositions as $position) {
            $rule->process($position, $file, 0);
        }

        if ($shouldHaveError) {
            $this->assertNotEmpty($file->getErrors());
        } else {
            $this->assertEmpty($file->getErrors());
        }
    }

    public function processProvider(): array
    {
        return [
            'multiple prototype definitions' => [
                new TokenStream([
                    new Token(Token::FILE_START_TYPE, '', 1, 0, 0),
                    new Token(Token::PROTOTYPE_KEYWORD_TYPE, 'prototype', 1, 0, 0),
                    new Token(Token::LPAREN_TYPE, '(', 1, 0, 0),
                    new Token(Token::OBJECT_IDENTIFIER_TYPE, 'Test', 1, 0, 0),
                    new Token(Token::RPAREN_TYPE, ')', 1, 0, 0),
                    new Token(Token::LBRACE_TYPE, '{', 1, 0, 0),
                    new Token(Token::RBRACE_TYPE, '}', 1, 0, 0),
                    new Token(Token::LINE_BREAK, PHP_EOL, 1, 0, 0),
                    new Token(Token::PROTOTYPE_KEYWORD_TYPE, 'prototype', 2, 0, 0),
                    new Token(Token::LPAREN_TYPE, '(', 2, 0, 0),
                    new Token(Token::OBJECT_IDENTIFIER_TYPE, 'Test', 2, 0, 0),
                    new Token(Token::RPAREN_TYPE, ')', 2, 0, 0),
                    new Token(Token::LBRACE_TYPE, '{', 2, 0, 0),
                    new Token(Token::RBRACE_TYPE, '}', 2, 0, 0)
                ]), [1, 8], true
            ],
            'single prototype definition' => [
                new TokenStream([
                    new Token(Token::FILE_START_TYPE, '', 1, 0, 0),
                    new Token(Token::PROTOTYPE_KEYWORD_TYPE, 'prototype', 1, 0, 0),
                    new Token(Token::LPAREN_TYPE, '(', 1, 0, 0),
                    new Token(Token::OBJECT_IDENTIFIER_TYPE, 'Test', 1, 0, 0),
                    new Token(Token::RPAREN_TYPE, ')', 1, 0, 0),
                    new Token(Token::LBRACE_TYPE, '{', 1, 0, 0),
                    new Token(Token::RBRACE_TYPE, '}', 1, 0, 0),
                    new Token(Token::LINE_BREAK, PHP_EOL, 1, 0, 0)
                ]), [1], false
            ],
            'prototype path assignment' => [
                new TokenStream([
                    new Token(Token::FILE_START_TYPE, '', 1, 0, 0),
                    new Token(Token::PROTOTYPE_KEYWORD_TYPE, 'prototype', 1, 0, 0),
                    new Token(Token::LPAREN_TYPE, '(', 1, 0, 0),
                    new Token(Token::OBJECT_IDENTIFIER_TYPE, 'Test', 1, 0, 0),
                    new Token(Token::RPAREN_TYPE, ')', 1, 0, 0),
                    new Token(Token::DOT_TYPE, '.', 1, 0, 0),
                    new Token(Token::OBJECT_PATH_PART_TYPE, 'test', 1, 0, 0)
                ]), [1], false
            ],
            'prototype unset' => [
                new TokenStream([
                    new Token(Token::FILE_START_TYPE, '', 1, 0, 0),
                    new Token(Token::PROTOTYPE_KEYWORD_TYPE, 'prototype', 1, 0, 0),
                    new Token(Token::LPAREN_TYPE, '(', 1, 0, 0),
                    new Token(Token::OBJECT_IDENTIFIER_TYPE, 'Test', 1, 0, 0),
                    new Token(Token::RPAREN_TYPE, ')', 1, 0, 0),
                    new Token(Token::UNSET_TYPE, '>', 1, 0, 0)
                ]), [1], false
            ]
        ];
    }
}
