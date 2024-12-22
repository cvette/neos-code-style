<?php

declare(strict_types=1);

namespace Vette\Neos\CodeStyle\Lexer;

use Closure;
use LogicException;

/**
 * Fusion Lexer
 *
 * @package Vette\FusionParser
 */
class Lexer
{
    /** lexical states */
    private const STATE_INITIAL = 0;
    private const STATE_PROTOTYPE_FOUND = 1;
    private const STATE_VALUE_EXPECTED = 2;
    private const STATE_OBJECT_IDENTIFIER_VALUE_FOUND = 3;
    private const STATE_EEL_EXPRESSION_FOUND = 4;
    private const STATE_DSL_FOUND = 5;
    private const STATE_INCLUDE_FOUND = 6;
    private const STATE_NAMESPACE_FOUND = 7;

    /** regular expressions */
    private const LINE_BREAK = '/\n/A';
    private const WHITESPACE = '/[ \t\f]+/A';

    private const SINGLE_LINE_COMMENT = '/(#|\/\/).*(\n|$)/A';
    private const MULTI_LINE_COMMENT = '/\/\*(.|\n)*\*\//uA';

    private const DOT = '/\./A';
    private const COLON = '/:/A';
    private const LPAREN = '/\(/A';
    private const RPAREN = '/\)/A';
    private const LBRACE = '/{/A';
    private const RBRACE = '/}/A';

    private const INCLUDE_KEYWORD = '/include/A';
    private const NAMESPACE_KEYWORD = '/namespace/A';
    private const INCLUDE_VALUE = '/.*/A';

    private const PROTOTYPE_KEYWORD = '/prototype/A';
    private const OBJECT_IDENTIFIER = '/[.a-zA-Z0-9_]+/A';
    private const OBJECT_PATH_PART = '/[a-zA-Z0-9_:-]+/A';
    private const META_PROPERTY_KEYWORD = '/@/A';

    private const COPY_OPERATOR = '/</A';
    private const UNSET_OPERATOR = '/>/A';
    private const ASSIGNMENT_OPERATOR = '/=/A';

    private const EEL_START = '/\$\{/A';

    private const AND_OPERATOR = "/\|\||or|OR/A";
    private const OR_OPERATOR = "/&&|and|AND/A";

    private const ADDITION_OPERATOR = '/\+/A';
    private const SUBTRACTION_OPERATOR = '/\-/A';
    private const MODULO_OPERATOR = "/%/A";
    private const DIVISION_OPERATOR = "/\//A";
    private const MULTIPLICATION_OPERATOR = "/\*/A";
    private const COMPARISON_OPERATOR = '/==|!=|<=|>=|<|>/A';
    private const NEGATION_OPERATOR = '/not|!/A';

    private const LBRACKET = '/\[/A';
    private const RBRACKET = '/\]/A';

    private const IF_KEYWORD = '/\?/A';
    private const IF_SEPARATOR = '/\:/A';

    private const EEL_IDENTIFIER = '/[a-zA-Z_][a-zA-Z0-9_\-]*/A';
    private const EEL_VALUE_SEPARATOR = '/,/A';
    private const EEL_DOUBLE_ARROW = '/=>/A';

    private const DSL_START = '/[a-zA-Z0-9\.]+[`]/A';
    private const DSL_CODE = '/[^`]+/A';
    private const DSL_END = '/`/A';

    private const NULL_VALUE = '/(NULL|null)/A';
    private const BOOLEAN_VALUE = '/(true|TRUE|false|FALSE)/A';
    private const NUMBER_VALUE = '/-?\d+/A';
    private const FLOAT_NUMBER_VALUE = '/-?\d+(\.\d+)?/A';
    private const STRING_VALUE = '/"([^"\\\\]*(?>\\\\.[^"\\\\]*)*)"|\'([^\'\\\\]*(?>\\\\.[^\'\\\\]*)*)\'/A';

    /** @var array<int> */
    protected array $states = [];

    protected int $state = self::STATE_INITIAL;

    protected int $cursor = 0;

    protected int $lineNumber = 1;

    /** @var array<Token> */
    protected array $tokens = [];

    protected int $end = 0;

    protected Source $source;

    protected string $code = '';

    /** @var array<array<Closure>> */
    protected array $stateDefinitions = [];

    protected bool $ignoreWhitespace = true;

    protected int $currentEelNestingLevel = 0;


    /**
     * Lexer constructor.
     *
     * @param bool $ignoreWhitespace
     */
    public function __construct(bool $ignoreWhitespace = true)
    {
        $this->ignoreWhitespace = $ignoreWhitespace;
        $this->initializeStateDefinitions();
    }

    /**
     * Initialize state definitions
     */
    protected function initializeStateDefinitions(): void
    {
        $this->stateDefinitions = [
            self::STATE_INITIAL => [
                self::SINGLE_LINE_COMMENT => function (string $text): void {
                    if (str_contains($text, '@neoscs-ignore-next-line')) {
                        $this->pushToken(Token::IGNORE_NEXT_LINE_TYPE, $text);
                    }
                },
                self::MULTI_LINE_COMMENT => function (string $text): void {
                    if (str_contains($text, '@neoscs-ignore-next-line')) {
                        $this->pushToken(Token::IGNORE_NEXT_LINE_TYPE, $text);
                    }
                },
                self::PROTOTYPE_KEYWORD => function (string $text): bool {
                    if ($this->lookahead(strlen($text), self::LPAREN, false)) {
                        $this->pushToken(Token::PROTOTYPE_KEYWORD_TYPE, $text);
                        $this->pushState(self::STATE_PROTOTYPE_FOUND);
                        return true;
                    }
                    return false;
                },
                self::INCLUDE_KEYWORD => function (string $text): bool {
                    if ($this->lookahead(strlen($text), self::COLON, true)) {
                        $this->pushToken(Token::INCLUDE_KEYWORD_TYPE, $text);
                        $this->pushState(self::STATE_INCLUDE_FOUND);
                        return true;
                    }
                    return false;
                },
                self::NAMESPACE_KEYWORD => function (string $text): bool {
                    if ($this->lookahead(strlen($text), self::COLON, true)) {
                        $this->pushToken(Token::NAMESPACE_KEYWORD_TYPE, $text);
                        $this->pushState(self::STATE_NAMESPACE_FOUND);
                        return true;
                    }
                    return false;
                },
                self::OBJECT_PATH_PART => function (string $text): void {
                    $this->pushToken(Token::OBJECT_PATH_PART_TYPE, $text);
                },
                self::LBRACE => function (string $text): void {
                    $this->pushToken(Token::LBRACE_TYPE, $text);
                },
                self::RBRACE => function (string $text): void {
                    $this->pushToken(Token::RBRACE_TYPE, $text);
                },
                self::COPY_OPERATOR => function (string $text): void {
                    $this->pushToken(Token::COPY_TYPE, $text);
                },
                self::UNSET_OPERATOR => function (string $text): void {
                    $this->pushToken(Token::UNSET_TYPE, $text);
                },
                self::ASSIGNMENT_OPERATOR => function (string $text): void {
                    $this->pushToken(Token::ASSIGNMENT_TYPE, $text);
                    $this->pushState(self::STATE_VALUE_EXPECTED);
                },
                self::META_PROPERTY_KEYWORD => function (string $text): void {
                    $this->pushToken(Token::META_PROPERTY_KEYWORD_TYPE, $text);
                },
                self::DOT => function (string $text): void {
                    $this->pushToken(Token::DOT_TYPE, $text);
                },
                self::STRING_VALUE => function (string $text): void {
                    $this->pushToken(Token::STRING_VALUE_TYPE, $text);
                },
            ],

            self::STATE_PROTOTYPE_FOUND => [
                self::LPAREN => function (string $text): void {
                    $this->pushToken(Token::LPAREN_TYPE, $text);
                },
                self::RPAREN => function (string $text): void {
                    $this->pushToken(Token::RPAREN_TYPE, $text);
                    $this->popState();
                },
                self::COLON => function (string $text): void {
                    $this->pushToken(Token::COLON_TYPE, $text);
                },
                self::OBJECT_IDENTIFIER => function (string $text): void {
                    $this->pushToken(Token::OBJECT_IDENTIFIER_TYPE, $text);
                },
            ],

            self::STATE_VALUE_EXPECTED => [
                self::BOOLEAN_VALUE => function (string $text): void {
                    $this->pushToken(Token::BOOLEAN_VALUE_TYPE, $text);
                    $this->popState();
                },
                self::NULL_VALUE => function (string $text): void {
                    $this->pushToken(Token::NULL_VALUE_TYPE, $text);
                    $this->popState();
                },
                self::NUMBER_VALUE => function (string $text): void {
                    $this->pushToken(Token::NUMBER_VALUE_TYPE, $text);
                    $this->popState();
                },
                self::FLOAT_NUMBER_VALUE => function (string $text): void {
                    $this->pushToken(Token::FLOAT_NUMBER_VALUE_TYPE, $text);
                    $this->popState();
                },
                self::STRING_VALUE => function (string $text): void {
                    $this->pushToken(Token::STRING_VALUE_TYPE, $text);
                    $this->popState();
                },
                self::DSL_START => function (string $text): void {
                    $this->pushToken(Token::DSL_START_TYPE, $text);
                    $this->pushState(self::STATE_DSL_FOUND);
                },
                self::OBJECT_IDENTIFIER => function (string $text): void {
                    $this->pushToken(Token::OBJECT_IDENTIFIER_TYPE, $text);
                    $this->pushState(self::STATE_OBJECT_IDENTIFIER_VALUE_FOUND);
                },
                self::EEL_START => function (string $text): void {
                    $this->pushToken(Token::EEL_START_TYPE, $text);
                    $this->pushState(self::STATE_EEL_EXPRESSION_FOUND);
                },
            ],
            self::STATE_OBJECT_IDENTIFIER_VALUE_FOUND => [
                self::OBJECT_IDENTIFIER => function (string $text): void {
                    $this->pushToken(Token::OBJECT_IDENTIFIER_TYPE, $text);
                },
                self::COLON => function (string $text): void {
                    $this->pushToken(Token::COLON_TYPE, $text);
                },
                self::LBRACE => function (string $text): void {
                    $this->pushToken(Token::LBRACE_TYPE, $text);
                    $this->popState();
                    $this->popState();
                },
                self::WHITESPACE => function (string $text): void {
                    $this->pushToken(Token::WHITESPACE_TYPE, $text);
                    $this->popState();
                    $this->popState();
                },
                self::LINE_BREAK => function (string $text): void {
                    $this->pushToken(Token::LINE_BREAK, $text);
                    $this->popState();
                    $this->popState();
                },
            ],
            self::STATE_EEL_EXPRESSION_FOUND => [
                self::RBRACE => function (string $text): void {

                    if ($this->currentEelNestingLevel === 0) {
                        $this->pushToken(Token::EEL_END_TYPE, $text);
                        $this->popState();
                        $this->popState();
                    } else {
                        $this->pushToken(Token::EEL_RBRACE_TYPE, $text);
                        $this->currentEelNestingLevel--;
                    }
                },
                self::LBRACE => function (string $text): void {
                    $this->pushToken(Token::EEL_LBRACE_TYPE, $text);
                    $this->currentEelNestingLevel++;
                },
                self::DOT => function (string $text): void {
                    $this->pushToken(Token::EEL_IDENTIFIER_SEPARATOR_TYPE, $text);
                },
                self::EEL_VALUE_SEPARATOR => function (string $text): void {
                    $this->pushToken(Token::EEL_VALUE_SEPARATOR_TYPE, $text);
                },
                self::IF_KEYWORD => function (string $text): void {
                    $this->pushToken(Token::EEL_IF_KEYWORD_TYPE, $text);
                },
                self::IF_SEPARATOR => function (string $text): void {
                    $this->pushToken(Token::EEL_IF_SEPARATOR_TYPE, $text);
                },
                self::NUMBER_VALUE => function (string $text): void {
                    $this->pushToken(Token::EEL_NUMBER_VALUE_TYPE, $text);
                },
                self::ADDITION_OPERATOR => function (string $text): void {
                    $this->pushToken(Token::EEL_ADDITION_OPERATOR_TYPE, $text);
                },
                self::SUBTRACTION_OPERATOR => function (string $text): void {
                    $this->pushToken(Token::EEL_SUBTRACTION_OPERATOR_TYPE, $text);
                },
                self::MODULO_OPERATOR => function (string $text): void {
                    $this->pushToken(Token::EEL_MODULO_OPERATOR_TYPE, $text);
                },
                self::DIVISION_OPERATOR => function (string $text): void {
                    $this->pushToken(Token::EEL_DIVISION_OPERATOR_TYPE, $text);
                },
                self::MULTIPLICATION_OPERATOR => function (string $text): void {
                    $this->pushToken(Token::EEL_MULTIPLICATION_OPERATOR_TYPE, $text);
                },
                self::EEL_DOUBLE_ARROW => function (string $text): void {
                    $this->pushToken(Token::EEL_DOUBLE_ARROW_TYPE, $text);
                },
                self::COMPARISON_OPERATOR => function (string $text): void {
                    $this->pushToken(Token::EEL_COMPARISON_OPERATOR_TYPE, $text);
                },
                self::NEGATION_OPERATOR => function (string $text): void {
                    if ($this->lookahead(strlen($text), self::WHITESPACE, true)) {
                        $this->pushToken(Token::EEL_NEGATION_OPERATOR_TYPE, $text);
                    }
                },
                self::AND_OPERATOR => function (string $text): void {
                    if ($this->lookahead(strlen($text), self::WHITESPACE, true)) {
                        $this->pushToken(Token::EEL_AND_OPERATOR_TYPE, $text);
                    }
                },
                self::OR_OPERATOR => function (string $text): void {
                    if ($this->lookahead(strlen($text), self::WHITESPACE, true)) {
                        $this->pushToken(Token::EEL_OR_OPERATOR_TYPE, $text);
                    }
                },
                self::LBRACKET => function (string $text): void {
                    $this->pushToken(Token::EEL_LBRACKET_TYPE, $text);
                },
                self::RBRACKET => function (string $text): void {
                    $this->pushToken(Token::EEL_RBRACKET_TYPE, $text);
                },
                self::LPAREN => function (string $text): void {
                    $this->pushToken(Token::EEL_LPAREN_TYPE, $text);
                },
                self::RPAREN => function (string $text): void {
                    $this->pushToken(Token::EEL_RPAREN_TYPE, $text);
                },
                self::EEL_IDENTIFIER => function (string $text): void {
                    $this->pushToken(Token::EEL_IDENTIFIER_TYPE, $text);
                },
                self::BOOLEAN_VALUE => function (string $text): void {
                    $this->pushToken(Token::EEL_BOOLEAN_VALUE_TYPE, $text);
                },
                self::NULL_VALUE => function (string $text): void {
                    $this->pushToken(Token::EEL_NULL_VALUE_TYPE, $text);
                },
                self::FLOAT_NUMBER_VALUE => function (string $text): void {
                    $this->pushToken(Token::EEL_FLOAT_NUMBER_VALUE_TYPE, $text);
                },
                self::STRING_VALUE => function (string $text): void {
                    $this->pushToken(Token::EEL_STRING_VALUE_TYPE, $text);
                }
            ],
            self::STATE_DSL_FOUND => [
                self::DSL_CODE => function (string $text): void {
                    $this->pushToken(Token::DSL_CODE_TYPE, $text);
                },
                self::DSL_END => function (string $text): void {
                    $this->pushToken(Token::DSL_END_TYPE, $text);
                    $this->popState();
                    $this->popState();
                },
            ],
            self::STATE_NAMESPACE_FOUND => [
                self::COLON => function (string $text): void {
                    $this->pushToken(Token::COLON_TYPE, $text);
                },
                self::ASSIGNMENT_OPERATOR => function (string $text): void {
                    $this->pushToken(Token::ASSIGNMENT_TYPE, $text);
                },
                self::OBJECT_IDENTIFIER => function (string $text): void {
                    $this->pushToken(Token::OBJECT_IDENTIFIER_TYPE, $text);
                },
                self::LINE_BREAK => function (string $text): void {
                    $this->pushToken(Token::LINE_BREAK, $text);
                    $this->popState();
                },
            ],
            self::STATE_INCLUDE_FOUND => [
                self::INCLUDE_VALUE => function (string $text): void {
                    $this->pushToken(Token::INCLUDE_VALUE_TYPE, $text);
                    $this->popState();
                },
            ],
        ];
    }

    /**
     * Tokenize source
     *
     * @param Source $source
     *
     * @return TokenStream
     *
     * @throws LogicException
     */
    public function tokenize(Source $source): TokenStream
    {
        $this->cursor = 0;
        $this->lineNumber = 1;
        $this->states = [];
        $this->tokens = [];
        $this->source = $source;
        $this->state = self::STATE_INITIAL;

        $this->code = str_replace(["\r\n", "\r"], "\n", $source->getCode());
        $this->end = strlen($this->code);

        $this->tokens[] = new Token(Token::FILE_START_TYPE, '', 0, 0, 0);

        while ($this->cursor < $this->end) {
            if ($this->lexState() || $this->lexWhitespace()) {
                continue;
            }

            $this->throwException();
        }

        $this->pushToken(Token::EOF_TYPE);
        return new TokenStream($this->tokens, $this->source);
    }

    /**
     * Throws lexing exception
     */
    private function throwException(): void
    {
        throw new LexerException(
            $this->lineNumber,
            $this->getColumn(),
            $this->code[$this->cursor],
            'Unexpected character "' . $this->code[$this->cursor] . '"'
        );
    }

    /**
     * Gets the current cursor position relative to the beginning of the line.
     *
     * @return int
     */
    private function getColumn(): int
    {
        $lines = explode("\n", substr($this->code, 0, $this->cursor));
        $lastLine = end($lines);

        return mb_strlen($lastLine);
    }

    /**
     * Lex the current state
     *
     * @return bool
     */
    protected function lexState(): bool
    {
        foreach ($this->stateDefinitions[$this->state] as $pattern => $function) {
            if (preg_match($pattern, $this->code, $match, 0, $this->cursor)) {
                $move = $function($match[0]);
                if ($move === null || $move === true) {
                    $this->moveCursor($match[0]);
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Lex whitespace
     *
     * @return bool
     */
    protected function lexWhitespace(): bool
    {
        if (preg_match(self::WHITESPACE, $this->code, $match, 0, $this->cursor)) {
            $this->pushToken(Token::WHITESPACE_TYPE);
            $this->moveCursor($match[0]);
            return true;
        }

        if (preg_match(self::LINE_BREAK, $this->code, $match, 0, $this->cursor)) {
            $this->pushToken(Token::LINE_BREAK);
            $this->moveCursor($match[0]);
            return true;
        }

        return false;
    }

    /**
     * Pushes a token
     *
     * @param int $type
     * @param string $value
     */
    protected function pushToken(int $type, string $value = ''): void
    {
        if (($type === Token::LINE_BREAK || $type === Token::WHITESPACE_TYPE) && $this->ignoreWhitespace) {
            return;
        }

        $this->tokens[] = new Token($type, $value, $this->lineNumber, $this->getColumn(), $this->cursor);
    }

    /**
     * Moves the cursor
     *
     * @param string $text
     */
    protected function moveCursor(string $text): void
    {
        $this->cursor += strlen($text);
        $this->lineNumber += mb_substr_count($text, "\n");
    }

    /**
     * Pushes a state
     *
     * @param int $state
     */
    protected function pushState(int $state): void
    {
        $this->states[] = $this->state;
        $this->state = $state;
    }

    /**
     * Pops a state
     */
    protected function popState(): void
    {
        if (count($this->states) === 0) {
            throw new LogicException('Cannot pop state without a previous state.');
        }

        $this->state = array_pop($this->states);
    }

    /**
     * Lookahead
     *
     * @param int $offset
     * @param string $token
     * @param bool $acceptWhitespace
     * @return bool
     */
    protected function lookahead(int $offset, string $token, bool $acceptWhitespace): bool
    {
        if ($acceptWhitespace && preg_match('/[ \t\f]?/A', $this->code, $match, 0, $this->cursor + $offset)) {
            $offset += strlen($match[0]);
        }

        if ($token === '') {
            return false;
        }

        if (preg_match($token, $this->code, $match, 0, $this->cursor + $offset)) {
            return true;
        }

        return false;
    }
}
