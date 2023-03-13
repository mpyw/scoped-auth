<?php

namespace Mpyw\ScopedAuth;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Database\Eloquent\Builder;

class ScopedEloquentUserProvider extends EloquentUserProvider
{
    /**
     * @param null|\Illuminate\Database\Eloquent\Model $model
     */
    public function newModelQuery($model = null): Builder
    {
        $query = parent::newModelQuery($model);

        $instance = $query->getModel();

        if ($instance instanceof AuthScopable) {
            $query = $instance->scopeForAuthentication($query);
        }

        return $query;
    }
}
