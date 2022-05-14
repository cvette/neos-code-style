<?php

namespace Vette\Neos\CodeStyle\Rules\Neos;

use PHPUnit\Framework\TestCase;
use Vette\Neos\CodeStyle\Files\File;
use Vette\Neos\CodeStyle\Lexer\Token;
use Vette\Neos\CodeStyle\Lexer\TokenStream;

class TernaryOperatorRuleTest extends TestCase
{

    /**
     * @dataProvider processProvider
     */
    public function testProcess(TokenStream $stream, bool $shouldHaveErrors)
    {
        $rule = new TernaryOperatorRule();
        $rule->setOptions([]);

        $file = new File('test.fusion');
        $file->setTokenStream($stream);
        $rule->process(1, $file, 0);

        if ($shouldHaveErrors) {
            $this->assertNotEmpty($file->getErrors());
        } else {
            $this->assertEmpty($file->getErrors());
        }
    }

    public function processProvider(): array
    {
        return [
            'single ternary operator' => [
                new TokenStream([
                    new Token(Token::FILE_START_TYPE, '', 0, 0, 0),
                    new Token(Token::EEL_IF_KEYWORD_TYPE, '?', 0, 0, 0),
                    new Token(Token::EEL_BOOLEAN_VALUE_TYPE, 'true', 0, 0, 0),
                    new Token(Token::EEL_IF_SEPARATOR_TYPE, ':', 0, 0, 0),
                    new Token(Token::EEL_BOOLEAN_VALUE_TYPE, 'false', 0, 0, 0)
                ]), false
            ],
            'second ternary operator on same line' => [
                new TokenStream([
                    new Token(Token::FILE_START_TYPE, '', 0, 0, 0),
                    new Token(Token::EEL_IF_KEYWORD_TYPE, '?', 0, 0, 0),
                    new Token(Token::EEL_BOOLEAN_VALUE_TYPE, 'true', 0, 0, 0),
                    new Token(Token::EEL_IF_KEYWORD_TYPE, '?', 0, 0, 0)
                ]), true
            ],
            'second ternary operator on new line' => [
                new TokenStream([
                    new Token(Token::FILE_START_TYPE, '', 0, 0, 0),
                    new Token(Token::EEL_IF_KEYWORD_TYPE, '?', 0, 0, 0),
                    new Token(Token::EEL_BOOLEAN_VALUE_TYPE, 'true', 0, 0, 0),
                    new Token(Token::LINE_BREAK, PHP_EOL, 0, 0, 0),
                    new Token(Token::EEL_IF_KEYWORD_TYPE, '?', 1, 0, 0)
                ]), false
            ]
        ];
    }
}
