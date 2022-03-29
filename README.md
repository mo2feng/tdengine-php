<h1 align="center"> tdgine </h1>

<p align="center"> rest client for db tdengine.</p>

[![Tests](https://github.com/mofengme/tdengine-php/actions/workflows/test.yml/badge.svg)](https://github.com/mofengme/tdengine-php/actions/workflows/test.yml)

## Installing

```shell
$ composer require mofengme/tdengine -vvv
```

## Usage

```php
use Mofengme\Tdengine\TdEngine;

$database = "test";
$host = "127.0.0.1"; //default
$port = "6041"; //default
$user = "root"; //default
$password = "taosdata"; //default

$client = new TdEngine($database,$host,$port,$user,$password);
$client->query('select * from test');
//or
$client->execute("show stables");

```

## Contributing

You can contribute in one of three ways:

1. File bug reports using the [issue tracker](https://github.com/mofeng/tdgine/issues).
2. Answer questions or fix bugs on the [issue tracker](https://github.com/mofeng/tdgine/issues).
3. Contribute new features or update the wiki.

_The code contribution process is not very formal. You just need to make sure that you follow the PSR-0, PSR-1, and
PSR-2 coding guidelines. Any new code contributions must be accompanied by unit tests where applicable._

## License

MIT