<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Identity\User\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Model;

/**
 * Stub/Proxy Eloquent model for the User bounded context.
 * In a real application, replace this with your actual Eloquent model
 * that includes casts, relationships, scopes, etc.
 */
class UserModel extends Model
{
    protected $table = 'users';

    /** @var list<string> */
    protected $guarded = [];
}
