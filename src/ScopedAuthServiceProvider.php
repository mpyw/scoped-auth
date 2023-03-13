<?php

namespace Mpyw\ScopedAuth;

use Closure;
use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;

class ScopedAuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        /* @noinspection PhpUnhandledExceptionInspection */
        $this->app->resolved(AuthManager::class)
            ? static::overrideEloquentUserProvider($this->app->make(AuthManager::class)) // @codeCoverageIgnore
            : $this->app->afterResolving(AuthManager::class, Closure::fromCallable([$this, 'overrideEloquentUserProvider']));
    }

    protected function overrideEloquentUserProvider(AuthManager $auth): void
    {
        $auth->provider('eloquent', function (Container $app, array $config) {
            return $app->make(ScopedEloquentUserProvider::class, [
                'model' => $config['model'],
            ]);
        });
    }
}
