# Scoped Auth

[![Build Status](https://github.com/mpyw/scoped-auth/actions/workflows/ci.yml/badge.svg?branch=master)](https://github.com/mpyw/scoped-auth/actions)
[![Coverage Status](https://coveralls.io/repos/github/mpyw/scoped-auth/badge.svg?branch=master)](https://coveralls.io/github/mpyw/scoped-auth?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mpyw/scoped-auth/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mpyw/scoped-auth/?branch=master)

Apply specific scope for user authentication.

## Requirements

- PHP: `^8.0`
- Laravel: `^9.0 || ^10.0`

## Installing

Via [Composer](https://getcomposer.org/)

```bash
$ composer require mpyw/scoped-auth
```

### For [Fortify](https://github.com/laravel/fortify) users

> [!WARNING]
> Default Fortify's [`RedirectIfTwoFactorAuthenticatable`](https://github.com/laravel/fortify/blob/7da6504f5f8a5fe6854dedaffc81ac497194ba56/src/Actions/RedirectIfTwoFactorAuthenticatable.php#L89-L91) implementation directly uses internal `Model` under `UserProvider`, however, [the Laravel author won't be willing to fix it for whatever reason](https://github.com/laravel/fortify/pull/393). So we need to configure Fortify like this:

<details>
<summary>CustomFortifyAuthenticator.php</summary>

```php
<?php

namespace App\Auth;

use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Auth\UserProvider;
use Laravel\Fortify\Fortify;

class CustomFortifyAuthenticator
{
    private const PASSWORD_NAME = 'password';

    private readonly UserProvider $provider;

    public function __construct(StatefulGuard $guard)
    {
        // Assert `StatefulGuard` has `getProvider()` which is not declared in the contract
        assert(method_exists($guard, 'getProvider'));
        $provider = $guard->getProvider();

        assert($provider instanceof UserProvider);
        $this->provider = $provider;
    }

    public function __invoke(Request $request): ?Authenticatable
    {
        $user = $this->provider->retrieveByCredentials([
            Fortify::username() => $request->input(Fortify::username()),
        ]);

        return $user && $this->provider->validateCredentials($user, [
            self::PASSWORD_NAME => $request->input(self::PASSWORD_NAME),
        ]) ? $user : null;
    }
}
```
</details>

<details>
<summary>AuthServiceProvider.php</summary>

```php
<?php

namespace App\Providers;

use App\Auth\CustomFortifyAuthenticator;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(CustomFortifyAuthenticator $authenticator): void
    {
        Fortify::authenticateUsing($authenticator);
    }
}
```
</details>

## Testing

Via [PHPUnit](https://phpunit.de/)

```bash
$ composer test
```

## Usage

Implement **AuthScopable** contract on your Authenticatable Eloquent Model.

```php
<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Mpyw\ScopedAuth\AuthScopable;

class User extends Model implements UserContract, AuthScopable
{
    use Authenticatable;

    public function scopeForAuthentication(Builder $query): Builder
    {
        return $query->where('active', 1);
    }
}
```

```php
<?php

use Illuminate\Support\Facades\Auth;

$user = Auth::user(); // Only include users where "active" is 1
```

Note that you can reuse another existing scope.

```php
public function scopeActive(Builder $query): Builder
{
    return $query->where('active', 1);
}

public function scopeForAuthentication(Builder $query): Builder
{
    return $this->scopeActive($query);
}
```

As a by-product, you can also run scope queries based on the standard Eloquent way.

```php
$user = User::where('email', 'xxx@example.com')->forAuthentication()->firstOrFail();
```

```php
$user = User::where('email', 'xxx@example.com')->scopes(['forAuthentication'])->firstOrFail();
```

## Standards

- [PSR-1: Basic Coding Standard](https://www.php-fig.org/psr/psr-1/)
- [PSR-2: Coding Style Guide](https://www.php-fig.org/psr/psr-2/)
- [PSR-4: Autoloader](https://www.php-fig.org/psr/psr-4/)
- [Semantic Versioning 2.0.0](https://semver.org/)

## Credits

- [mpyw](https://github.com/mpyw)
- [All Contributors](../../contributors)

## License

Licensed under the MIT License. See [License File](LICENSE.md) for more information.
