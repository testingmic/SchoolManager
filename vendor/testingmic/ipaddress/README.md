# PHP IpAddress

An IP address helps you identify visitors geographical location.

A PHP IP Address Geolocation library to help you identify visitors geographical location.

## Requirements

- pdo_sqlite (runtime deps)
- php_curl (for update only)

## Installation And Initialisation :

These instructions will get you a copy of the project up and running on your local machine.
there are two options:

- [x] Using Composer installer(Recommended) by typing the following command:

```php

composer require testingmic/ipaddress

```

## Usage: (using Composer autoloader)

```php

require __DIR__ . '/vendor/autoload.php';

try
{
    $IpAddressGeo = new testingmic\IpAddress();

} catch (\Throwable $th) {
    trigger_error($th->getMessage(), E_USER_ERROR);
}

```

#### Getting Country code from given IP address:

```php

    $ipAddress_1='37.140.250.97';

    echo '<pre>';
    print_r($IpAddressGeo->resolve($ipAddress_1));

    Array
    (
        [start] => 629996032
        [end] => 629996288
        [country_code] => UA
        [country_name] => United States
        [continent_code] => NA
        [city] => Alexandria
        [latitude] => 38.8031
        [longitude] => -77.0388
    )
```

#### Getting current visitor Country code (auto detect his IP address):

```php

    echo '<pre>';
    print_r($IpAddressGeo->resolve());  /** resolve() method called without any argument */
```
