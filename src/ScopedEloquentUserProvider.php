<?php

namespace Mpyw\ScopedAuth;

use Illuminate\Auth\EloquentUserProvider;

class ScopedEloquentUserProvider extends EloquentUserProvider
{
    /**
     * @param  null|\Illuminate\Database\Eloquent\Model $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function newModelQuery($model = null)
    {
        $query = parent::newModelQuery($model);

        $instance = $query->getModel();

        if (method_exists($instance, 'scopeForAuthentication')) {
            $query = $instance->scopeForAuthentication($query);
        }

        return $query;
    }
}
