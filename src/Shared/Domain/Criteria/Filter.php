<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Domain\Criteria;

final class Filter
{
    public function __construct(
        private readonly FilterField $field,
        private readonly FilterOperator $operator,
        private readonly FilterValue $value
    ) {}

    /**
     * @param  array<string, mixed>  $values
     */
    public static function fromValues(array $values): self
    {
        $field = $values['field'];
        $operator = $values['operator'];
        $value = $values['value'];

        if (! is_string($field) || ! is_string($operator) || ! is_string($value)) {
            throw new \InvalidArgumentException('Filter field, operator and value must be strings');
        }

        return new self(
            new FilterField($field),
            FilterOperator::from($operator),
            new FilterValue($value)
        );
    }

    public function field(): FilterField
    {
        return $this->field;
    }

    public function operator(): FilterOperator
    {
        return $this->operator;
    }

    public function value(): FilterValue
    {
        return $this->value;
    }

    public function serialize(): string
    {
        $field = $this->field->value();
        $operator = $this->operator->value();
        $value = $this->value->value();

        return sprintf(
            '%s.%s.%s',
            $field,
            $operator,
            is_scalar($value) ? (string) $value : ''
        );
    }
}
