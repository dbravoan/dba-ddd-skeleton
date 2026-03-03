<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Infrastructure\Criteria;

use Dba\DddSkeleton\Shared\Domain\Criteria\Criteria;
use Dba\DddSkeleton\Shared\Domain\Criteria\Filters;
use Dba\DddSkeleton\Shared\Domain\Criteria\Order;
use Dba\DddSkeleton\Shared\Domain\Criteria\OrderBy;
use Dba\DddSkeleton\Shared\Domain\Criteria\OrderType;
use Dba\DddSkeleton\Shared\Domain\Security\SqlInjectionProtector;
use Illuminate\Http\Request;
use InvalidArgumentException;

final class RequestCriteriaBuilder
{
    private array $allowedFields = [];

    public function __construct(
        array $allowedFields = []
    ) {
        $this->allowedFields = $allowedFields;
    }

    public function buildFromRequest(Request $request): Criteria
    {
        $filters = $request->get('filters', []);
        $orderBy = $request->get('order_by');
        $orderType = $request->get('order_type');
        $limit = $request->get('limit') ? (int) $request->get('limit') : null;
        $offset = $request->get('offset') ? (int) $request->get('offset') : null;
        $glue = $request->get('glue', 'AND');

        // Sanitizar filters antes de procesarlos
        $filters = $this->sanitizeFilters(is_array($filters) ? $filters : []);
        
        // Validar order_by
        if ($orderBy) {
            $this->validateField($orderBy);
        }

        $criteriaFilters = Filters::fromValues($filters);

        if ($orderBy) {
            $criteriaOrder = new Order(
                new OrderBy($orderBy),
                new OrderType($orderType ?? 'asc')
            );
        } else {
            $criteriaOrder = Order::none();
        }

        return new Criteria($criteriaFilters, $criteriaOrder, $offset, $limit, $glue);
    }

    public function withAllowedFields(array $allowedFields): self
    {
        $this->allowedFields = $allowedFields;
        return $this;
    }

    private function sanitizeFilters(array $filters): array
    {
        // Si tiene 'groups', es formato avanzado
        if (isset($filters['groups'])) {
            return [
                'groups' => array_map(
                    fn (array $group) => $this->sanitizeGroup($group),
                    $filters['groups']
                )
            ];
        }

        // Formato simple: validar cada filtro
        return array_map(
            fn (array $filter) => $this->sanitizeFilter($filter),
            $filters
        );
    }

    private function sanitizeGroup(array $group): array
    {
        if (!isset($group['conditions']) || !is_array($group['conditions'])) {
            throw new InvalidArgumentException('Group must have "conditions" array');
        }

        return [
            'glue' => strtolower($group['glue'] ?? 'and'),
            'conditions' => array_map(
                fn (array $condition) => $this->sanitizeFilter($condition),
                $group['conditions']
            )
        ];
    }

    private function sanitizeFilter(array $filter): array
    {
        if (!isset($filter['field'])) {
            throw new InvalidArgumentException('Filter must have "field"');
        }

        if (!isset($filter['operator'])) {
            throw new InvalidArgumentException('Filter must have "operator"');
        }

        $field = trim((string) $filter['field']);
        $operator = strtoupper(trim((string) $filter['operator']));
        $value = $filter['value'] ?? '';

        $this->validateField($field);

        $this->validateOperator($operator);

        $value = $this->sanitizeValue($operator, $value);

        return [
            'field' => $field,
            'operator' => $operator,
            'value' => $value
        ];
    }

    private function validateField(string $field): void
    {
        SqlInjectionProtector::validateFieldName($field);

        if (!empty($this->allowedFields)) {
            if (!in_array($field, $this->allowedFields, true)) {
                throw new InvalidArgumentException(
                    "Field '{$field}' is not allowed for filtering. Allowed fields: " .
                    implode(', ', $this->allowedFields)
                );
            }
            return;
        }

        $this->validateFieldSyntax($field);
    }

    private function validateFieldSyntax(string $field): void
    {
        // Rechazar patrones peligrosos
        if (preg_match('/[;\'"`()\\\\]/', $field)) {
            throw new InvalidArgumentException(
                "Field '{$field}' contains invalid characters"
            );
        }

        // Solo permitir: letras, números, underscore, dot (para joins)
        if (!preg_match('/^[a-zA-Z0-9_.]+$/', $field)) {
            throw new InvalidArgumentException(
                "Field '{$field}' contains invalid characters. Only alphanumeric, underscore and dot allowed"
            );
        }

        // Evitar inyecciones de comentarios SQL
        if (preg_match('/--|#|\/\*/', $field)) {
            throw new InvalidArgumentException(
                "Field '{$field}' contains SQL comment characters"
            );
        }

        // Evitar keywords SQL en nombres de campos
        if (preg_match('/\b(UNION|SELECT|INSERT|UPDATE|DELETE|DROP|CREATE|ALTER)\b/i', $field)) {
            throw new InvalidArgumentException(
                "Field '{$field}' contains SQL keywords"
            );
        }
    }

    private function validateOperator(string $operator): void
    {
        SqlInjectionProtector::validateOperator($operator);
    }

    private function sanitizeValue(string $operator, mixed $value): string
    {
        $value = SqlInjectionProtector::validateFilterValue((string) $value, $operator);

        // BETWEEN requiere 2 valores
        if ($operator === 'BETWEEN') {
            $parts = array_map('trim', explode(',', $value));
            if (count($parts) !== 2) {
                throw new InvalidArgumentException(
                    "BETWEEN operator requires exactly 2 values separated by comma"
                );
            }
            return implode(',', $parts);
        }

        // IN requiere al menos 1 valor
        if ($operator === 'IN') {
            $parts = array_filter(
                array_map('trim', explode(',', $value)),
                fn ($v) => !empty($v)
            );
            if (empty($parts)) {
                throw new InvalidArgumentException(
                    "IN operator requires at least 1 value"
                );
            }
            return implode(',', $parts);
        }

        return trim($value);
    }
}