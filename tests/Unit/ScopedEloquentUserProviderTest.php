<?php

namespace Mpyw\ScopedAuth\Tests\Unit;

use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Mockery;
use Mpyw\ScopedAuth\AuthScopable;
use Mpyw\ScopedAuth\ScopedEloquentUserProvider;
use Mpyw\ScopedAuth\Tests\TestCase;

class ScopedEloquentUserProviderTest extends TestCase
{
    public function testNewModelQueryWithAuthScopable(): void
    {
        $hasher = Mockery::mock(Hasher::class);
        $model = new TestAuthScopableUser();

        $provider = new ScopedEloquentUserProvider($hasher, TestAuthScopableUser::class);

        // Call newModelQuery and verify scopeForAuthentication was applied
        $query = $provider->newModelQuery();

        // The scope should have been applied (TestAuthScopableUser sets a flag)
        $this->assertTrue(TestAuthScopableUser::$scopeApplied);
    }

    public function testNewModelQueryWithoutAuthScopable(): void
    {
        $hasher = Mockery::mock(Hasher::class);

        $provider = new ScopedEloquentUserProvider($hasher, TestNonScopableUser::class);

        // Call newModelQuery - should not fail even without AuthScopable
        $query = $provider->newModelQuery();

        // Just verify we got a query back
        $this->assertInstanceOf(Builder::class, $query);
    }

    protected function setUp(): void
    {
        parent::setUp();
        TestAuthScopableUser::$scopeApplied = false;
    }
}

/**
 * Test model that implements AuthScopable
 */
class TestAuthScopableUser extends Model implements UserContract, AuthScopable
{
    public static bool $scopeApplied = false;

    protected $table = 'users';

    public function scopeForAuthentication(Builder $query): Builder
    {
        static::$scopeApplied = true;
        return $query;
    }

    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    public function getAuthIdentifier(): mixed
    {
        return $this->getKey();
    }

    public function getAuthPasswordName(): string
    {
        return 'password';
    }

    public function getAuthPassword(): ?string
    {
        return $this->password;
    }

    public function getRememberToken(): ?string
    {
        return $this->remember_token;
    }

    public function setRememberToken($value): void
    {
        $this->remember_token = $value;
    }

    public function getRememberTokenName(): string
    {
        return 'remember_token';
    }
}

/**
 * Test model that does NOT implement AuthScopable
 */
class TestNonScopableUser extends Model implements UserContract
{
    protected $table = 'users';

    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    public function getAuthIdentifier(): mixed
    {
        return $this->getKey();
    }

    public function getAuthPasswordName(): string
    {
        return 'password';
    }

    public function getAuthPassword(): ?string
    {
        return $this->password;
    }

    public function getRememberToken(): ?string
    {
        return $this->remember_token;
    }

    public function setRememberToken($value): void
    {
        $this->remember_token = $value;
    }

    public function getRememberTokenName(): string
    {
        return 'remember_token';
    }
}
