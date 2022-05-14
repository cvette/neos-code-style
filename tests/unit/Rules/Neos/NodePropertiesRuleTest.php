<?php

namespace Vette\Neos\CodeStyle\Rules\Neos;

use PHPUnit\Framework\TestCase;
use Vette\Neos\CodeStyle\Files\File;
use Vette\Neos\CodeStyle\Lexer\Token;
use Vette\Neos\CodeStyle\Lexer\TokenStream;

class NodePropertiesRuleTest extends TestCase
{

    /**
     * @dataProvider processProvider
     */
    public function testProcess(TokenStream $stream, bool $shouldHaveError)
    {
        $rule = new NodePropertiesRule();
        $rule->setOptions([]);

        $file = new File(__FILE__);
        $file->setTokenStream($stream);

        $rule->process(0, $file, 0);

        if ($shouldHaveError) {
            $this->assertNotEmpty($file->getErrors());
        } else {
            $this->assertEmpty($file->getErrors());
        }
    }

    public function processProvider(): array
    {
        return [
            'node' => [
                new TokenStream([
                    new Token(Token::EEL_IDENTIFIER_TYPE, 'node', 1, 0, 0),
                    new Token(Token::EEL_IDENTIFIER_SEPARATOR_TYPE, '.', 1, 0, 0),
                    new Token(Token::EEL_IDENTIFIER_TYPE, 'properties', 1, 0, 0),

                ]), true
            ],
            'documentNode' => [
                new TokenStream([
                    new Token(Token::EEL_IDENTIFIER_TYPE, 'documentNode', 1, 0, 0),
                    new Token(Token::EEL_IDENTIFIER_SEPARATOR_TYPE, '.', 1, 0, 0),
                    new Token(Token::EEL_IDENTIFIER_TYPE, 'properties', 1, 0, 0),

                ]), true
            ],
            'site' => [
                new TokenStream([
                    new Token(Token::EEL_IDENTIFIER_TYPE, 'site', 1, 0, 0),
                    new Token(Token::EEL_IDENTIFIER_SEPARATOR_TYPE, '.', 1, 0, 0),
                    new Token(Token::EEL_IDENTIFIER_TYPE, 'properties', 1, 0, 0),

                ]), true
            ],
            'other' => [
                new TokenStream([
                    new Token(Token::EEL_IDENTIFIER_TYPE, 'other', 1, 0, 0),
                    new Token(Token::EEL_IDENTIFIER_SEPARATOR_TYPE, '.', 1, 0, 0),
                    new Token(Token::EEL_IDENTIFIER_TYPE, 'properties', 1, 0, 0),

                ]), false
            ],
            'not properties' => [
                new TokenStream([
                    new Token(Token::EEL_IDENTIFIER_TYPE, 'node', 1, 0, 0),
                    new Token(Token::EEL_IDENTIFIER_SEPARATOR_TYPE, '.', 1, 0, 0),
                    new Token(Token::EEL_IDENTIFIER_TYPE, 'other', 1, 0, 0),

                ]), false
            ],
        ];
    }
}
