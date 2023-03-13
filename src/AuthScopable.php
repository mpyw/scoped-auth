<?php

namespace Mpyw\ScopedAuth;

use Illuminate\Database\Eloquent\Builder;

interface AuthScopable
{
    /**
     * Add a scope for authentication.
     */
    public function scopeForAuthentication(Builder $query): Builder;
}
