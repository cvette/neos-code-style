<?php

namespace Vette\Neos\CodeStyle\Rules\Neos;

use PHPUnit\Framework\TestCase;
use Vette\Neos\CodeStyle\Files\File;
use Vette\Neos\CodeStyle\Lexer\Token;
use Vette\Neos\CodeStyle\Lexer\TokenStream;

class PrototypeNamePrefixRuleTest extends TestCase
{

    /**
     * @dataProvider processWithErrorsProvider
     */
    public function testProcessWithErrors(TokenStream $stream, array $ruleOptions)
    {
        $rule = new PrototypeNamePrefixRule();
        $rule->setOptions($ruleOptions);

        $file = new File('test.fusion');
        $file->setTokenStream($stream);
        $rule->process(1, $file, 0);

        $this->assertNotEmpty($file->getErrors());
    }

    /**
     * @dataProvider processWithoutErrorsProvider
     */
    public function testProcessWithoutErrors(TokenStream $stream, array $ruleOptions, int $nestingLevel)
    {
        $rule = new PrototypeNamePrefixRule();
        $rule->setOptions($ruleOptions);

        $file = new File('test.fusion');
        $file->setTokenStream($stream);
        $rule->process(1, $file, $nestingLevel);

        $this->assertEmpty($file->getErrors());
    }

    public function processWithErrorsProvider(): array
    {
        return [
            'without namespace' => [
                new TokenStream([
                    new Token(Token::FILE_START_TYPE, '', 0, 0, 0),
                    new Token(Token::PROTOTYPE_KEYWORD_TYPE, 'prototype', 0, 0, 0),
                    new Token(Token::LPAREN_TYPE, '(', 0, 0, 0),
                    new Token(Token::OBJECT_IDENTIFIER_TYPE, 'Test', 0, 0, 0),
                    new Token(Token::RPAREN_TYPE, ')', 0, 0, 0)
                ]),
                ['ignorePackages' => [], 'validPrefixes' => ['Content']]
            ],
            'with invalid namespace' => [
                new TokenStream([
                    new Token(Token::FILE_START_TYPE, '', 0, 0, 0),
                    new Token(Token::PROTOTYPE_KEYWORD_TYPE, 'prototype', 0, 0, 0),
                    new Token(Token::LPAREN_TYPE, '(', 0, 0, 0),
                    new Token(Token::OBJECT_IDENTIFIER_TYPE, 'Package', 0, 0, 0),
                    new Token(Token::COLON_TYPE, '.', 0, 0, 0),
                    new Token(Token::OBJECT_IDENTIFIER_TYPE, 'Test', 0, 0, 0),
                    new Token(Token::RPAREN_TYPE, ')', 0, 0, 0)
                ]),
                ['ignorePackages' => [], 'validPrefixes' => ['Content']]
            ]
        ];
    }

    public function processWithoutErrorsProvider(): array
    {
        return [
            'ignored package' => [
                new TokenStream([
                    new Token(Token::FILE_START_TYPE, '', 0, 0, 0),
                    new Token(Token::PROTOTYPE_KEYWORD_TYPE, 'prototype', 0, 0, 0),
                    new Token(Token::LPAREN_TYPE, '(', 0, 0, 0),
                    new Token(Token::OBJECT_IDENTIFIER_TYPE, 'Package', 0, 0, 0),
                    new Token(Token::COLON_TYPE, '.', 0, 0, 0),
                    new Token(Token::OBJECT_IDENTIFIER_TYPE, 'Test', 0, 0, 0),
                    new Token(Token::RPAREN_TYPE, ')', 0, 0, 0)
                ]),
                ['ignorePackages' => ['Package'], 'validPrefixes' => ['Content']],
                0
            ],
            'valid prefix' => [
                new TokenStream([
                    new Token(Token::FILE_START_TYPE, '', 0, 0, 0),
                    new Token(Token::PROTOTYPE_KEYWORD_TYPE, 'prototype', 0, 0, 0),
                    new Token(Token::LPAREN_TYPE, '(', 0, 0, 0),
                    new Token(Token::OBJECT_IDENTIFIER_TYPE, 'Package', 0, 0, 0),
                    new Token(Token::COLON_TYPE, '.', 0, 0, 0),
                    new Token(Token::OBJECT_IDENTIFIER_TYPE, 'Test', 0, 0, 0),
                    new Token(Token::RPAREN_TYPE, ')', 0, 0, 0)
                ]),
                ['ignorePackages' => [], 'validPrefixes' => ['Content']],
                1
            ]
        ];
    }
}
