<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Infrastructure\Criteria;

use Dba\DddSkeleton\Shared\Domain\Criteria\Criteria;
use Dba\DddSkeleton\Shared\Domain\Criteria\Filters;
use Dba\DddSkeleton\Shared\Domain\Criteria\Order;
use Dba\DddSkeleton\Shared\Domain\Criteria\OrderBy;
use Dba\DddSkeleton\Shared\Domain\Criteria\OrderType;
use Illuminate\Http\Request;
use InvalidArgumentException;

final class RequestCriteriaBuilder
{
    /** @var array<int, string> */
    private array $allowedFields = [];

    /** @param array<int, string> $allowedFields */
    public function __construct(array $allowedFields = [])
    {
        $this->allowedFields = $allowedFields;
    }

    public function build(Request $request): Criteria
    {
        /** @var mixed $filtersRaw */
        $filtersRaw = $request->get('filters', []);
        $filtersList = is_array($filtersRaw) ? $filtersRaw : [];

        $filters = Filters::fromValues(['groups' => $this->sanitizeFilters($filtersList)]);

        $limit = $request->get('limit');
        $offset = $request->get('offset');

        $orderByRaw = $request->get('order_by');
        $orderTypeRaw = $request->get('order');
        $glueRaw = $request->get('glue', 'and');

        $orderBy = is_string($orderByRaw) ? $orderByRaw : '';
        $orderType = is_string($orderTypeRaw) ? $orderTypeRaw : OrderType::NONE;
        $glue = is_string($glueRaw) ? $glueRaw : 'and';

        return new Criteria(
            $filters,
            new Order(
                new OrderBy($orderBy),
                new OrderType($orderType)
            ),
            is_numeric($offset) ? (int) $offset : null,
            is_numeric($limit) ? (int) $limit : null,
            $glue
        );
    }

    /** @param array<int, string> $allowedFields */
    public function withAllowedFields(array $allowedFields): self
    {
        $this->allowedFields = $allowedFields;

        return $this;
    }

    /**
     * @param  array<mixed>  $filters
     * @return array<int, array<string, mixed>>
     */
    private function sanitizeFilters(array $filters): array
    {
        return array_map(function ($filter) {
            if (! is_array($filter)) {
                throw new InvalidArgumentException('Filter must be an array');
            }

            if (isset($filter['filters']) && is_array($filter['filters'])) {
                /** @var array<string, mixed> $filter */
                return $this->sanitizeGroup($filter);
            }

            /** @var array<string, mixed> $filter */
            return $this->sanitizeFilter($filter);
        }, $filters);
    }

    /**
     * @param  array<string, mixed>  $group
     * @return array<string, mixed>
     */
    private function sanitizeGroup(array $group): array
    {
        /** @var mixed $filtersRaw */
        $filtersRaw = $group['filters'] ?? [];
        $filtersList = is_array($filtersRaw) ? $filtersRaw : [];

        return [
            'conditions' => array_map(function (mixed $condition) {
                if (! is_array($condition)) {
                    throw new InvalidArgumentException('Filter condition must be an array');
                }

                /** @var array<string, mixed> $condition */
                return $this->sanitizeFilter($condition);
            }, $filtersList),
            'glue' => is_string($group['glue'] ?? null) ? (string) $group['glue'] : 'and',
        ];
    }

    /**
     * @param  array<string, mixed>  $filter
     * @return array<string, mixed>
     */
    private function sanitizeFilter(array $filter): array
    {
        $field = $filter['field'] ?? '';
        $this->validateField(is_string($field) ? $field : '');

        return $filter;
    }

    private function validateField(string $field): void
    {
        if (! empty($this->allowedFields) && ! in_array($field, $this->allowedFields)) {
            throw new InvalidArgumentException(sprintf('The field <%s> is not allowed for filtering', $field));
        }
    }
}
