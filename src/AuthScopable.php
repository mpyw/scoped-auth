<?php

namespace Mpyw\ScopedAuth;

use Illuminate\Database\Eloquent\Builder;

interface AuthScopable
{
    /**
     * Add a scope for authentication.
     *
     * @param  \Illuminate\Database\Eloquent\Builder      $query
     * @return null|\Illuminate\Database\Eloquent\Builder
     */
    public function scopeForAuthentication(Builder $query): ?Builder;
}
