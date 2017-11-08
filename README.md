# Firewall [![Build Status](https://secure.travis-ci.org/M6Web/Firewall.png)](http://travis-ci.org/M6Web/Firewall)

This PHP 5.4+ library provides IP filtering features.  
A lot of [filters](#entries-formats) can be used.  
It is also possible to [customize](#custom-error-handling) the error handling.

## Installation

Add this line in your `composer.json` :

```json
{
    "require": {
        "m6web/firewall": "dev-master"
    }
}
```

Update your vendors :

```
$ composer update m6web/firewall
```

## Usage

#### Basic usage

```php
use M6Web\Component\Firewall\Firewall;

$whiteList = array(
    '127.0.0.1',
    '192.168.0.*',
);

$blackList = array(
    '192.168.0.50',
);

$firewall = new Firewall();

$connAllowed = $firewall
    ->setDefaultState(false)
    ->addList($whiteList, 'local', true)
    ->addList($blackList, 'localBad', false)
    ->setIpAddress('195.88.195.146')
    ->handle()
;

if (!$connAllowed) {
    http_response_code(403); // Forbidden
    exit();
}
```

In this example, only IPs starting with *192.168.0* (but not *192.168.0.50*) and *127.0.0.1* will be allowed by the firewall.  
In all other case `handle()` return false.


* `setDefaultState(false)` defines default firewall response (Optional - Default false),
* `addList($whiteList, 'local', true)` defines `$whiteList` list, called `local` as allowed (`true`),
* `addList($blackList, 'localBad', false);` defines `$blackList` list, called `localBad` as rejected (`false`).

#### Entries Formats

Type | Syntax | Details
--- | --- | ---
IPV6|`::1`|Short notation
IPV4|`192.168.0.1`|
Range|`192.168.0.0-192.168.1.60`|Includes all IPs from *192.168.0.0* to *192.168.0.255*<br />and from *192.168.1.0* to *198.168.1.60*
Wild card|`192.168.0.*`|IPs starting with *192.168.0*<br />Same as IP Range `192.168.0.0-192.168.0.255`
Subnet mask|`192.168.0.0/255.255.255.0`|IPs starting with *192.168.0*<br />Same as `192.168.0.0-192.168.0.255` and `192.168.0.*`
CIDR Mask|`192.168.0.0/24`|IPs starting with *192.168.0*<br />Same as `192.168.0.0-192.168.0.255` and `192.168.0.*`<br />and `192.168.0.0/255.255.255.0`

#### Custom error handling

```php
use M6Web\Component\Firewall\Firewall;

function handleFirewallReturn(Firewall $firewall, $response) {
    if (false === $response) {
        header($_SERVER["SERVER_PROTOCOL"]." 403 Forbiden");
        exit();
    }

    return $response;
}

$whiteList = array(
    '127.0.0.1',
    '198.168.0.*',
);

$blackList = array(
    '192.168.0.50',
);

$firewall = new Firewall();
$firewall
    ->setDefaultState(true)
    ->addList($whiteList, 'local', true)
    ->addList($blackList, 'localBad', false)
    ->setIpAddress('195.88.195.146')
    ->handle('handleFirewallReturn')
;
```

`handle('handleFirewallReturn')` calls `handleFirewallReturn` with Firewall object and response as arguments (true or false).

## Running the tests

```shell
$ php composer.phar install --dev
$ ./vendor/bin/atoum -d Tests
```

## Credits

Developped by the [Cytron Team](http://cytron.fr/) of [M6 Web](http://tech.m6web.fr/).  
Tested with [atoum](http://atoum.org).

## License

Firewall is licensed under the [MIT license](LICENSE).
