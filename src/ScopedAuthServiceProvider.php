<?php

namespace Mpyw\ScopedAuth;

use Closure;
use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;

class ScopedAuthServiceProvider extends ServiceProvider
{
    /** @noinspection PhpDocMissingThrowsInspection */

    /**
     * @return void
     */
    public function register()
    {
        /* @noinspection PhpUnhandledExceptionInspection */
        $this->app->resolved(AuthManager::class)
            ? static::overrideEloquentUserProvider($this->app->make(AuthManager::class)) // @codeCoverageIgnore
            : $this->app->afterResolving(AuthManager::class, Closure::fromCallable([$this, 'overrideEloquentUserProvider']));
    }

    /**
     * @param  \Illuminate\Auth\AuthManager $auth
     * @return void
     */
    protected function overrideEloquentUserProvider(AuthManager $auth)
    {
        $auth->provider('eloquent', function (Container $app, array $config) {
            return $app->make(ScopedEloquentUserProvider::class, [
                'model' => $config['model'],
            ]);
        });
    }
}
