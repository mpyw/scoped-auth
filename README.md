# Scoped Auth

[![Build Status](https://travis-ci.com/mpyw/scoped-auth.svg?branch=master)](https://travis-ci.com/mpyw/scoped-auth)
[![Coverage Status](https://coveralls.io/repos/github/mpyw/scoped-auth/badge.svg?branch=master)](https://coveralls.io/github/mpyw/scoped-auth?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mpyw/scoped-auth/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mpyw/scoped-auth/?branch=master)

Apply specific scope for user authentication.

## Requirements

- PHP: ^7.3 || ^8.0
- Laravel: ^6.0 || ^7.0 || ^8.0

## Installing

Via [Composer](https://getcomposer.org/)

```bash
$ composer require mpyw/scoped-auth
```

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
