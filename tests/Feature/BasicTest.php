<?php

namespace Mpyw\ScopedAuth\Tests\Feature;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Mpyw\ScopedAuth\Tests\TestCase;

class BasicTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'testbench',
            'database.connections.testbench' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ],
            'auth.providers.users.model' => User::class,
        ]);

        DB::statement('drop table if exists users');
        DB::statement('create table users(
            id int not null,
            active int not null default 1,
            email text not null,
            password text not null
        )');
    }

    public function testAttemptSuccess(): void
    {
        $user = User::create([
            'id' => 123,
            'active' => true,
            'email' => 'u123@example.com',
            'password' => Hash::make('p123'),
        ]);

        $this->assertTrue(Auth::attempt(['email' => 'u123@example.com', 'password' => 'p123']));
        $this->assertTrue(Auth::user()->is($user));
    }

    public function testAttemptFailure(): void
    {
        $user = User::create([
            'id' => 123,
            'active' => false,
            'email' => 'u123@example.com',
            'password' => Hash::make('p123'),
        ]);

        $this->assertFalse(Auth::attempt(['email' => 'u123@example.com', 'password' => 'p123']));
        $this->assertNull(Auth::user());
    }

    public function testManualScopeQuerySuccess(): void
    {
        $user = User::create([
            'id' => 123,
            'active' => true,
            'email' => 'u123@example.com',
            'password' => Hash::make('p123'),
        ]);

        $this->assertTrue(User::where('email', 'u123@example.com')->forAuthentication()->firstOrFail()->is($user));
    }

    public function testManualScopeQueryFailure(): void
    {
        $user = User::create([
            'id' => 123,
            'active' => false,
            'email' => 'u123@example.com',
            'password' => Hash::make('p123'),
        ]);

        $this->expectException(ModelNotFoundException::class);
        User::where('email', 'u123@example.com')->forAuthentication()->firstOrFail();
    }
}
