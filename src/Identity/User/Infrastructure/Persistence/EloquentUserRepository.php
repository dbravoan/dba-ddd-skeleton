<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Identity\User\Infrastructure\Persistence;

use Dba\DddSkeleton\Identity\User\Domain\User;
use Dba\DddSkeleton\Identity\User\Domain\UserId;
use Dba\DddSkeleton\Identity\User\Domain\UserRepository;
use Dba\DddSkeleton\Shared\Domain\Criteria\Criteria;
use Dba\DddSkeleton\Shared\Infrastructure\Persistence\Eloquent\EloquentCriteria;
use Dba\DddSkeleton\Shared\Infrastructure\Persistence\Eloquent\EloquentCriteriaConverter;
use Dba\DddSkeleton\Shared\Infrastructure\Persistence\Eloquent\EloquentRepository;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends EloquentRepository<UserModel>
 */
final class EloquentUserRepository extends EloquentRepository implements UserRepository
{
    /** @var array<string, string> */
    protected array $toEloquentFields = [
        'id'    => 'id',
        'email' => 'email',
        'name'  => 'name',
    ];

    public function save(User $user): void
    {
        $this->newQuery()->updateOrCreate(
            ['id' => $user->id()->value()],
            $user->toPrimitives()
        );

        $this->publishEvents($user);
    }

    public function search(UserId $id): ?User
    {
        /** @var UserModel|null $model */
        $model = $this->newQuery()->find($id->value());

        return $model ? $this->toDomain($model->toArray()) : null;
    }

    /** @return array<int, User> */
    public function searchByCriteria(Criteria $criteria): array
    {
        /** @var EloquentCriteria<UserModel> $eloquentCriteria */
        $eloquentCriteria = EloquentCriteriaConverter::convert($criteria, $this->toEloquentFields);

        $models = $this->matching($eloquentCriteria)->get();

        return array_map(
            fn (Model $model) => $this->toDomain($model->toArray()),
            $models->all()
        );
    }

    public function delete(UserId $id): bool
    {
        return (bool) $this->newQuery()->where('id', $id->value())->delete();
    }

    /** @param array<string, mixed> $primitives */
    private function toDomain(array $primitives): User
    {
        return User::fromPrimitives($primitives);
    }
}

/**
 * Stub/Proxy for User model since this is a package.
 * In a real app, this would be a real Eloquent Model.
 */
class UserModel extends Model
{
    protected $table = 'users';
    protected $guarded = [];
}
