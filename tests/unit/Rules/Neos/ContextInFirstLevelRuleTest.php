<?php

namespace Vette\Neos\CodeStyle\Rules\Neos;

use PHPUnit\Framework\TestCase;
use Vette\Neos\CodeStyle\Files\File;
use Vette\Neos\CodeStyle\Lexer\Token;
use Vette\Neos\CodeStyle\Lexer\TokenStream;

class ContextInFirstLevelRuleTest extends TestCase
{

    /**
     * @dataProvider processProvider
     */
    public function testProcess(TokenStream $stream, int $level, bool $shouldHaveError)
    {
        $rule = new ContextInFirstLevelRule();
        $rule->setOptions([]);

        $file = new File(__FILE__);
        $file->setTokenStream($stream);

        $rule->process(1, $file, $level);

        if ($shouldHaveError) {
            $this->assertNotEmpty($file->getErrors());
        } else {
            $this->assertEmpty($file->getErrors());
        }
    }

    public function processProvider(): array
    {
        return [
            'first level' => [
                new TokenStream([
                    new Token(Token::LINE_BREAK, PHP_EOL, 1, 0, 0),
                    new Token(Token::META_PROPERTY_KEYWORD_TYPE, '@', 1, 0, 0),
                    new Token(Token::OBJECT_PATH_PART_TYPE, 'context', 1, 0, 0),
                ]), 1, true
            ],
            'second level' => [
                new TokenStream([
                    new Token(Token::LINE_BREAK, PHP_EOL, 1, 0, 0),
                    new Token(Token::META_PROPERTY_KEYWORD_TYPE, '@', 1, 0, 0),
                    new Token(Token::OBJECT_PATH_PART_TYPE, 'context', 1, 0, 0),
                ]), 2, false
            ],
            'nested path' => [
                new TokenStream([
                    new Token(Token::DOT_TYPE, '.', 1, 0, 0),
                    new Token(Token::META_PROPERTY_KEYWORD_TYPE, '@', 1, 0, 0),
                    new Token(Token::OBJECT_PATH_PART_TYPE, 'context', 1, 0, 0),
                ]), 1, false
            ]
        ];
    }
}
