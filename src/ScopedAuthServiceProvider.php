<?php

namespace Mpyw\ScopedAuth;

use Closure;
use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;

class ScopedAuthServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register()
    {
        $this->app->afterResolving(AuthManager::class, Closure::fromCallable([$this, 'overrideEloquentUserProvider']));
    }

    /**
     * @param \Illuminate\Auth\AuthManager $auth
     */
    protected function overrideEloquentUserProvider(AuthManager $auth): void
    {
        $auth->provider('eloquent', function (Container $app, array $config) {
            return $app->make(ScopedEloquentUserProvider::class, [
                'model' => $config['model'],
            ]);
        });
    }
}
