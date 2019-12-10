<?php

namespace Mpyw\ScopedAuth\Tests\Unit;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Mockery;
use Mpyw\ScopedAuth\AuthScopable;
use Mpyw\ScopedAuth\ScopedEloquentUserProvider;
use Mpyw\ScopedAuth\Tests\TestCase;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class ScopedEloquentUserProviderTest extends TestCase
{
    /**
     * @var \Illuminate\Contracts\Auth\UserProvider|\Mpyw\ScopedAuth\ScopedEloquentUserProvider
     */
    protected $provider;

    /**
     * @var \Illuminate\Contracts\Hashing\Hasher
     */
    protected $hasher;

    /**
     * @var \Illuminate\Contracts\Auth\Authenticatable|\Illuminate\Database\Eloquent\Model|\Mpyw\ScopedAuth\AuthScopable
     */
    protected $model;

    /**
     * @var \Illuminate\Database\Eloquent\Builder
     */
    protected $query;

    public function testNewModelQuery(): void
    {
        /** @var EloquentUserProvider|\Mockery\LegacyMockInterface|\Mockery\MockInterface $parent */
        $parent = Mockery::mock('overload:' . EloquentUserProvider::class);
        $this->hasher = Mockery::mock(Hasher::class);
        $this->model = Mockery::mock(Model::class . ',' . AuthScopable::class . ',' . UserContract::class);
        $this->query = Mockery::mock(Builder::class);

        $parent->shouldReceive('newModelQuery')->with(null)->andReturn($this->query);
        $this->query->shouldReceive('getModel')->andReturn($this->model);
        $this->model->shouldReceive('scopeForAuthentication')->with($this->query)->andReturn($this->query);

        $this->provider = $this->app->make(ScopedEloquentUserProvider::class, [$this->hasher, $this->model]);
        $this->assertSame($this->query, $this->provider->newModelQuery());
    }

    public function testNewModelQueryWithArgument(): void
    {
        /** @var EloquentUserProvider|\Mockery\LegacyMockInterface|\Mockery\MockInterface $parent */
        $parent = Mockery::mock('overload:' . EloquentUserProvider::class);
        $this->hasher = Mockery::mock(Hasher::class);
        $this->model = Mockery::mock(Model::class . ',' . AuthScopable::class . ',' . UserContract::class);
        $this->query = Mockery::mock(Builder::class);

        $parent->shouldReceive('newModelQuery')->with($this->model)->andReturn($this->query);
        $this->query->shouldReceive('getModel')->andReturn($this->model);
        $this->model->shouldReceive('scopeForAuthentication')->with($this->query)->andReturn($this->query);

        $this->provider = $this->app->make(ScopedEloquentUserProvider::class, [$this->hasher, $this->model]);
        $this->assertSame($this->query, $this->provider->newModelQuery($this->model));
    }

    public function testNewModelQueryWithoutAuthScopable(): void
    {
        /** @var EloquentUserProvider|\Mockery\LegacyMockInterface|\Mockery\MockInterface $parent */
        $parent = Mockery::mock('overload:' . EloquentUserProvider::class);
        $this->hasher = Mockery::mock(Hasher::class);
        $this->model = Mockery::mock(Model::class . ',' . UserContract::class);
        $this->query = Mockery::mock(Builder::class);

        $parent->shouldReceive('newModelQuery')->with(null)->andReturn($this->query);
        $this->query->shouldReceive('getModel')->andReturn($this->model);
        $this->model->shouldNotReceive('scopeForAuthentication');

        $this->provider = $this->app->make(ScopedEloquentUserProvider::class, [$this->hasher, $this->model]);
        $this->assertSame($this->query, $this->provider->newModelQuery());
    }
}
