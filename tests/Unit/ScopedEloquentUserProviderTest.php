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

        // Create anonymous class that implements AuthScopable
        $modelClass = get_class(new class extends Model implements UserContract, AuthScopable {
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
        });

        // Reset static state
        $modelClass::$scopeApplied = false;

        $provider = new ScopedEloquentUserProvider($hasher, $modelClass);

        // Call newModelQuery and verify scopeForAuthentication was applied
        $query = $provider->newModelQuery();

        // The scope should have been applied
        $this->assertTrue($modelClass::$scopeApplied);
    }

    public function testNewModelQueryWithoutAuthScopable(): void
    {
        $hasher = Mockery::mock(Hasher::class);

        // Create anonymous class that does NOT implement AuthScopable
        $modelClass = get_class(new class extends Model implements UserContract {
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
        });

        $provider = new ScopedEloquentUserProvider($hasher, $modelClass);

        // Call newModelQuery - should not fail even without AuthScopable
        $query = $provider->newModelQuery();

        // Just verify we got a query back
        $this->assertInstanceOf(Builder::class, $query);
    }
}
