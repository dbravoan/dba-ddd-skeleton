<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Domain\Criteria;

use InvalidArgumentException;
use Dba\DddSkeleton\Shared\Domain\Collection;
use function Lambdish\Phunctional\map;
use function Lambdish\Phunctional\reduce;

final class Filters extends Collection
{
    public static function fromValues(array $values): self
    {
        if (isset($values['groups'])) {
            return new self(map(self::groupBuilder(), $values['groups']));
        }
        
        return new self(array_map(self::filterBuilder(), $values));
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

    public function __construct(array $items = [])
    {
        foreach ($items as $item) {
            if (!$item instanceof Filter && !$item instanceof FilterGroup) {
                throw new InvalidArgumentException('All items must be instances of Filter or FilterGroup');
            }
        }

        parent::__construct($items);
    }

    public function filters(): array
    {
        return $this->items();
    }

    public function serialize(): string
    {
        return reduce(
            static fn (string $accumulate, FilterGroup $group) => sprintf('%s^%s', $accumulate, $group->serialize()),
            $this->items(),
            ''
        );
    }

    protected function type(): array
    {
        return [Filter::class, FilterGroup::class];
    }
    
}
