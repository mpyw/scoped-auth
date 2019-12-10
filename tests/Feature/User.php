<?php

namespace Mpyw\ScopedAuth\Tests\Feature;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Mpyw\ScopedAuth\AuthScopable;

/**
 * Class User
 *
 * @property int $id
 * @property-read bool $active
 * @method static static create(array $attributes)
 */
class User extends Model implements AuthScopable, UserContract
{
    use Authenticatable;

    public $timestamps = false;
    public $incrementing = false;

    protected $casts = [
        'id' => 'int',
        'active' => 'bool',
    ];
    protected $attributes = ['active' => false];
    protected $guarded = [];

    /**
     * Add a scope for authentication.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForAuthentication(Builder $query): Builder
    {
        return $query->where('active', 1);
    }
}
