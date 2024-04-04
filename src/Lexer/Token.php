<?php

declare(strict_types=1);

namespace Vette\Neos\CodeStyle\Lexer;

use LogicException;

/**
 * Represents a Token.
 */
final class Token
{
    private string $value;

    private int $type;

    private int $lineno;

    private int $column;

    private int $offset;

    public const EOF_TYPE = -1;
    public const WHITESPACE_TYPE = 0;
    public const LINE_BREAK = 1;

    public const DOT_TYPE = 2;
    public const COLON_TYPE = 3;
    public const LPAREN_TYPE = 4;
    public const RPAREN_TYPE = 5;
    public const LBRACE_TYPE = 6;
    public const RBRACE_TYPE = 7;

    public const INCLUDE_KEYWORD_TYPE = 8;
    public const INCLUDE_VALUE_TYPE = 9;
    public const NAMESPACE_KEYWORD_TYPE = 10;
    public const PROTOTYPE_KEYWORD_TYPE = 11;
    public const OBJECT_IDENTIFIER_TYPE = 12;
    public const OBJECT_PATH_PART_TYPE = 13;
    public const META_PROPERTY_KEYWORD_TYPE = 14;

    public const COPY_TYPE = 15;
    public const UNSET_TYPE = 16;
    public const ASSIGNMENT_TYPE = 17;

    public const DSL_START_TYPE = 18;
    public const DSL_CODE_TYPE = 19;
    public const DSL_END_TYPE = 20;

    public const EEL_START_TYPE = 21;
    public const EEL_END_TYPE = 22;

    public const EEL_IF_KEYWORD_TYPE = 23;
    public const EEL_IF_SEPARATOR_TYPE = 24;
    public const EEL_AND_OPERATOR_TYPE = 25;
    public const EEL_OR_OPERATOR_TYPE = 26;

    public const EEL_LPAREN_TYPE = 27;
    public const EEL_RPAREN_TYPE = 28;

    public const EEL_LBRACKET_TYPE = 29;
    public const EEL_RBRACKET_TYPE = 30;

    public const EEL_LBRACE_TYPE = 31;
    public const EEL_RBRACE_TYPE = 32;

    public const EEL_ADDITION_OPERATOR_TYPE = 33;
    public const EEL_SUBTRACTION_OPERATOR_TYPE = 34;
    public const EEL_MULTIPLICATION_OPERATOR_TYPE = 35;
    public const EEL_DIVISION_OPERATOR_TYPE = 36;
    public const EEL_MODULO_OPERATOR_TYPE = 37;
    public const EEL_COMPARISON_OPERATOR_TYPE = 38;
    public const EEL_NEGATION_OPERATOR_TYPE = 39;

    public const EEL_IDENTIFIER_TYPE = 40;
    public const EEL_IDENTIFIER_SEPARATOR_TYPE = 41;
    public const EEL_VALUE_SEPARATOR_TYPE = 42;
    public const EEL_DOUBLE_ARROW_TYPE = 43;

    public const EEL_NULL_VALUE_TYPE = 44;
    public const EEL_BOOLEAN_VALUE_TYPE = 45;
    public const EEL_NUMBER_VALUE_TYPE = 46;
    public const EEL_FLOAT_NUMBER_VALUE_TYPE = 47;
    public const EEL_STRING_VALUE_TYPE = 48;

    public const NULL_VALUE_TYPE = 49;
    public const BOOLEAN_VALUE_TYPE = 50;
    public const NUMBER_VALUE_TYPE = 51;
    public const FLOAT_NUMBER_VALUE_TYPE = 52;
    public const STRING_VALUE_TYPE = 53;

    public const FILE_START_TYPE = 99;
    public const IGNORE_NEXT_LINE_TYPE = 100;


    /**
     * @param int $type The type of the token
     * @param string $value The token value
     * @param int $lineno The line position in the source
     * @param int $column The column on the line
     * @param int $offset
     */
    public function __construct(int $type, string $value, int $lineno, int $column, int $offset)
    {
        $this->type = $type;
        $this->value = $value;
        $this->lineno = $lineno;
        $this->column = $column;
        $this->offset = $offset;
    }

    public function __toString(): string
    {
        return sprintf(
            '%s(%s)',
            self::typeToString($this->type, true),
            $this->value
        );
    }

    public function getLine(): int
    {
        return $this->lineno;
    }

    public function getColumn(): int
    {
        return $this->column;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * Returns the constant representation (internal) of a given type.
     *
     * @param int $type The type as an integer
     * @param bool $short Whether to return a short representation or not
     *
     * @return string The string representation
     */
    public static function typeToString(int $type, bool $short = false): string
    {
        $name = match ($type) {
            self::EOF_TYPE => 'EOF_TYPE',
            self::OBJECT_IDENTIFIER_TYPE => 'OBJECT_IDENTIFIER_TYPE',
            self::WHITESPACE_TYPE => 'WHITESPACE_TYPE',
            self::NUMBER_VALUE_TYPE => 'NUMBER_VALUE_TYPE',
            self::FLOAT_NUMBER_VALUE_TYPE => 'FLOAT_NUMBER_VALUE_TYPE',
            self::UNSET_TYPE => 'UNSET_TYPE',
            self::LINE_BREAK => 'LINE_BREAK_TYPE',
            self::COPY_TYPE => 'COPY_TYPE',
            self::DOT_TYPE => 'DOT_TYPE',
            self::COLON_TYPE => 'COLON_TYPE',
            self::ASSIGNMENT_TYPE => 'ASSIGNMENT_TYPE',
            self::NULL_VALUE_TYPE => 'NULL_VALUE_TYPE',
            self::BOOLEAN_VALUE_TYPE => 'BOOLEAN_VALUE_TYPE',
            self::RBRACE_TYPE => 'RBRACE_TYPE',
            self::LBRACE_TYPE => 'LBRACE_TYPE',
            self::RPAREN_TYPE => 'RPAREN_TYPE',
            self::LPAREN_TYPE => 'LPAREN_TYPE',
            self::INCLUDE_KEYWORD_TYPE => 'INCLUDE_KEYWORD_TYPE',
            self::NAMESPACE_KEYWORD_TYPE => 'NAMESPACE_KEYWORD_TYPE',
            self::PROTOTYPE_KEYWORD_TYPE => 'PROTOTYPE_KEYWORD_TYPE',
            self::STRING_VALUE_TYPE => 'STRING_VALUE_TYPE',
            self::OBJECT_PATH_PART_TYPE => 'OBJECT_PATH_PART_TYPE',
            self::META_PROPERTY_KEYWORD_TYPE => 'META_PROPERTY_KEYWORD_TYPE',
            self::EEL_START_TYPE => 'EEL_START_TYPE',
            self::EEL_END_TYPE => 'EEL_END_TYPE',
            self::EEL_IDENTIFIER_TYPE => 'EEL_IDENTIFIER_TYPE',
            self::EEL_IF_KEYWORD_TYPE => 'EEL_IF_KEYWORD_TYPE',
            self::EEL_ADDITION_OPERATOR_TYPE => 'EEL_ADDITION_OPERATOR_TYPE',
            self::EEL_AND_OPERATOR_TYPE => 'EEL_AND_OPERATOR_TYPE',
            self::EEL_BOOLEAN_VALUE_TYPE => 'EEL_BOOLEAN_VALUE_TYPE',
            self::EEL_COMPARISON_OPERATOR_TYPE => 'EEL_COMPARISON_OPERATOR_TYPE',
            self::EEL_DIVISION_OPERATOR_TYPE => 'EEL_DIVISION_OPERATOR_TYPE',
            self::EEL_DOUBLE_ARROW_TYPE => 'EEL_DOUBLE_ARROW_TYPE',
            self::EEL_FLOAT_NUMBER_VALUE_TYPE => 'EEL_FLOAT_NUMBER_VALUE_TYPE',
            self::EEL_IDENTIFIER_SEPARATOR_TYPE => 'EEL_IDENTIFIER_SEPARATOR_TYPE',
            self::EEL_LPAREN_TYPE => 'EEL_LPAREN_TYPE',
            self::EEL_RPAREN_TYPE => 'EEL_RPAREN_TYPE',
            self::EEL_LBRACE_TYPE => 'EEL_LBRACE_TYPE',
            self::EEL_RBRACE_TYPE => 'EEL_RBRACE_TYPE',
            self::EEL_MODULO_OPERATOR_TYPE => 'EEL_MODULO_OPERATOR_TYPE',
            self::EEL_MULTIPLICATION_OPERATOR_TYPE => 'EEL_MULTIPLICATION_OPERATOR_TYPE',
            self::EEL_NEGATION_OPERATOR_TYPE => 'EEL_NEGATION_OPERATOR_TYPE',
            self::EEL_NULL_VALUE_TYPE => 'EEL_NULL_VALUE_TYPE',
            self::EEL_NUMBER_VALUE_TYPE => 'EEL_NUMBER_VALUE_TYPE',
            self::EEL_OR_OPERATOR_TYPE => 'EEL_OR_OPERATOR_TYPE',
            self::EEL_RBRACKET_TYPE => 'EEL_RBRACKET_TYPE',
            self::EEL_STRING_VALUE_TYPE => 'EEL_STRING_VALUE_TYPE',
            self::EEL_SUBTRACTION_OPERATOR_TYPE => 'EEL_SUBTRACTION_OPERATOR_TYPE',
            self::EEL_VALUE_SEPARATOR_TYPE => 'EEL_VALUE_SEPARATOR_TYPE',
            self::EEL_IF_SEPARATOR_TYPE => 'EEL_IF_SEPARATOR_TYPE',
            self::DSL_END_TYPE => 'DSL_END_TYPE',
            self::DSL_START_TYPE => 'DSL_START_TYPE',
            self::DSL_CODE_TYPE => 'DSL_CODE_TYPE',
            default => throw new LogicException(sprintf('Token of type "%s" does not exist.', $type)),
        };

        return $short ? $name : 'Fusion\Token::' . $name;
    }
}
