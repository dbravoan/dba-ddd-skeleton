<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Domain\Criteria;

use Dba\DddSkeleton\Shared\Domain\Collection;
use InvalidArgumentException;

use function Lambdish\Phunctional\map;
use function Lambdish\Phunctional\reduce;

/**
 * @extends Collection<Filter|FilterGroup>
 */
final class Filters extends Collection
{
    /**
     * @param array<string, mixed> $values
     */
    public static function fromValues(array $values): self
    {
        if (isset($values['groups'])) {
            /** @var iterable<mixed> $groups */
            $groups = $values['groups'];

            /** @var array<int, FilterGroup> $mappedGroups */
            $mappedGroups = map(self::groupBuilder(), $groups);

            return new self($mappedGroups);
        }

        /** @var array<int, Filter> $mappedFilters */
        $mappedFilters = array_map(self::filterBuilder(), $values);

        return new self($mappedFilters);
    }

    private static function filterBuilder(): callable
    {
        return fn (array $values) => Filter::fromValues($values);
    }

    private static function groupBuilder(): callable
    {
        return function (array $group) {
            $glue = $group['glue'] ?? 'and';
            $conditions = array_map(self::filterBuilder(), $group['conditions']);

            return FilterGroup::fromValues($conditions, $glue);
        };
    }

    /**
     * @param array<int, Filter|FilterGroup> $items
     */
    public function __construct(array $items = [])
    {
        parent::__construct($items);
    }

    /**
     * @return array<int, Filter|FilterGroup>
     */
    public function filters(): array
    {
        return $this->items();
    }

    public function serialize(): string
    {
        /** @var string $result */
        $result = reduce(
            static fn (string $accumulate, FilterGroup|Filter $group) => sprintf('%s^%s', $accumulate, $group->serialize()),
            $this->items(),
            ''
        );

        return $result;
    }

    /**
     * @return array<int, string>
     */
    protected function type(): array
    {
        return [Filter::class, FilterGroup::class];
    }
}
