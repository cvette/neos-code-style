<?php

namespace Vette\Neos\CodeStyle\Rules\Neos;

use PHPUnit\Framework\TestCase;
use Vette\Neos\CodeStyle\Files\File;
use Vette\Neos\CodeStyle\Lexer\Token;
use Vette\Neos\CodeStyle\Lexer\TokenStream;

class FluidTemplateRuleTest extends TestCase
{

    /**
     * @dataProvider processProvider
     */
    public function testProcess(TokenStream $stream, bool $shouldHaveError)
    {
        $rule = new FluidTemplateRule();
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
            'template' => [
                new TokenStream([
                    new Token(Token::OBJECT_IDENTIFIER_TYPE, 'Neos.Fusion', 1, 0, 0),
                    new Token(Token::COLON_TYPE, ':', 1, 0, 0),
                    new Token(Token::OBJECT_IDENTIFIER_TYPE, 'Template', 1, 0, 0),

                ]), true
            ],
            'no template' => [
                new TokenStream([
                    new Token(Token::OBJECT_IDENTIFIER_TYPE, 'Neos.Fusion', 1, 0, 0),
                    new Token(Token::COLON_TYPE, ':', 1, 0, 0),
                    new Token(Token::OBJECT_IDENTIFIER_TYPE, 'Something', 1, 0, 0),

                ]), false
            ]
        ];
    }
}
