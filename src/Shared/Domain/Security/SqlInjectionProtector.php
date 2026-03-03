<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Domain\Security;

use InvalidArgumentException;

final class SqlInjectionProtector
{
    // Patrones de SQL injection comunes
    private const DANGEROUS_PATTERNS = [
        // Comentarios SQL
        '/(--|\#|\/\*|\*\/)/',
        
        // UNION-based injection
        '/\bUNION\b/i',
        
        // Stacked queries
        '/;/',
        
        // Boolean-based blind injection
        '/\b(AND|OR)\b\s*1\s*=\s*1/i',
        
        // Time-based blind injection
        '/\b(SLEEP|BENCHMARK|WAITFOR)\b/i',
        
        // Error-based injection
        '/\b(EXTRACTVALUE|UPDATEXML|JSON_EXTRACT)\b/i',
    ];

    private const DANGEROUS_KEYWORDS = [
        'UNION', 'SELECT', 'INSERT', 'UPDATE', 'DELETE', 'DROP',
        'CREATE', 'ALTER', 'EXEC', 'EXECUTE', 'SCRIPT', 'JAVASCRIPT',
        'ONLOAD', 'ONERROR', 'SLEEP', 'BENCHMARK', 'WAITFOR'
    ];

    /**
     * Valida un nombre de campo contra patrones de SQL injection
     */
    public static function validateFieldName(string $field): void
    {
        $field = trim($field);

        // No permitir caracteres especiales
        if (!preg_match('/^[a-zA-Z0-9_.]+$/', $field)) {
            throw new InvalidArgumentException(
                "Field name '{$field}' contains invalid characters"
            );
        }

        // No permitir patrones de SQL injection
        foreach (self::DANGEROUS_PATTERNS as $pattern) {
            if (preg_match($pattern, $field)) {
                throw new InvalidArgumentException(
                    "Field name '{$field}' contains potential SQL injection patterns"
                );
            }
        }

        // No permitir SQL keywords
        foreach (self::DANGEROUS_KEYWORDS as $keyword) {
            if (stripos($field, $keyword) !== false) {
                throw new InvalidArgumentException(
                    "Field name cannot contain SQL keyword: {$keyword}"
                );
            }
        }
    }

    /**
     * Valida un valor de filtro contra patrones de SQL injection
     */
    public static function validateFilterValue(string $value, string $operator): string
    {
        $value = trim($value);

        // Algunos operadores tienen restricciones especiales
        if ($operator === 'IN') {
            $values = array_map('trim', explode(',', $value));
            foreach ($values as $v) {
                self::validateSingleValue($v);
            }
            return implode(',', $values);
        }

        if ($operator === 'BETWEEN') {
            $values = array_map('trim', explode(',', $value));
            if (count($values) !== 2) {
                throw new InvalidArgumentException(
                    "BETWEEN requires exactly 2 comma-separated values"
                );
            }
            foreach ($values as $v) {
                self::validateSingleValue($v);
            }
            return implode(',', $values);
        }

        return self::validateSingleValue($value);
    }

    /**
     * Valida un valor individual
     */
    private static function validateSingleValue(string $value): string
    {
        $value = trim($value);

        // Detectar patrones peligrosos
        foreach (self::DANGEROUS_PATTERNS as $pattern) {
            if (preg_match($pattern, $value)) {
                throw new InvalidArgumentException(
                    "Filter value contains potential SQL injection pattern"
                );
            }
        }

        return $value;
    }

    /**
     * Valida un operador
     */
    public static function validateOperator(string $operator): void
    {
        $validOperators = [
            '=', '!=', '>', '<', '>=', '<=',
            'CONTAINS', 'NOT_CONTAINS', 'STARTS_WITH', 'ENDS_WITH',
            'IN', 'BETWEEN', 'LIKE'
        ];

        if (!in_array(strtoupper($operator), $validOperators, true)) {
            throw new InvalidArgumentException(
                "Invalid operator: {$operator}"
            );
        }
    }
}