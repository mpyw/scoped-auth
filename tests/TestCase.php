<?php

namespace Mpyw\ScopedAuth\Tests;

use Mpyw\ScopedAuth\ScopedAuthServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /**
     * @param  \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app): array
    {
        return [
            ScopedAuthServiceProvider::class,
        ];
    }
}
