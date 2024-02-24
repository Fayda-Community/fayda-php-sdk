# PHP SDK - Fayda Platform

❗This repository is work in progress. It is not ready yet and may change a lot.

> Read the official specification
>
document [Fayda Platform API Specification](https://nidp.atlassian.net/wiki/spaces/FAPIQ/pages/633733136/Fayda+Platform+API+Specification).
> In order to receive the latest change notifications, please `Watch` this repository.

[![Latest Version](https://img.shields.io/github/release/Fayda-Community/fayda-php-sdk.svg)](https://github.com/Fayda-Community/fayda-php-sdk/releases)
[![PHP Version](https://img.shields.io/packagist/php-v/fayda/fayda-php-sdk.svg?color=green)](https://secure.php.net)
[![Build Status](https://travis-ci.org/Fayda-Community/fayda-php-sdk.svg?branch=main)](https://travis-ci.org/Fayda-Community/fayda-php-sdk)
[![Total Downloads](https://poser.pugx.org/fayda/fayda-php-sdk/downloads)](https://packagist.org/packages/fayda/fayda-php-sdk)
[![License](https://poser.pugx.org/fayda/fayda-php-sdk/license)](LICENSE)
<!-- [![Total Lines](https://tokei.rs/b1/github/Fayda-Community/fayda-php-sdk)](https://github.com/Fayda-Community/fayda-php-sdk) -->
<!-- [![Packagist](https://img.shields.io/packagist/dt/fayda/fayda-php-sdk.svg)](https://packagist.org/packages/fayda/fayda-php-sdk) -->
<!-- [![License](https://img.shields.io/packagist/l/fayda/fayda-php-sdk.svg)](LICENSE) -->

## Requirements

| Dependency                                              | Requirement                    |
|---------------------------------------------------------|--------------------------------|
| [PHP](https://secure.php.net/manual/en/install.php)     | `>=7.1 <8.2` `Recommend PHP8+` |
| [guzzlehttp/guzzle](https://github.com/guzzle/guzzle)   | <code>^6.0 &#124; ^7.0</code>  | 
| [firebase/php-jwt](https://github.com/firebase/php-jwt) | `^5.5`                         |

## Install

> Install package via [Composer](https://getcomposer.org/).

```shell
composer require "fayda/fayda-php-sdk"
```

## Usage

### Choose environment

| Environment   | BaseUri                              |
|---------------|--------------------------------------|
| *Production*  | `https://prod.fayda.et`              |
| *Development* | `https://auth-api.fayda.et`(DEFAULT) |

```php
// Switch to the prod environment
FaydaApi::setBaseUri('https://prod.fayda.et');
```

### Debug mode & logging

```php
// Debug mode will record the logs of API to files in the directory "FaydaApi::getLogPath()" according to the minimum log level "FaydaApi::getLogLevel()".
FaydaApi::setDebugMode(true);

// Logging in your code
// FaydaApi::setLogPath('/tmp');
// FaydaApi::setLogLevel(Monolog\Logger::DEBUG);
FaydaApi::getLogger()->debug("I'm a debug message");
```

### Examples

> See the [examples](examples) folder for more.

#### Example API - OTP Request Service

```php

use Fayda\SDK\Api\Otp;
use Fayda\SDK\Auth;
use Fayda\SDK\Exceptions\BusinessException;
use Fayda\SDK\Exceptions\HttpException;
use Fayda\SDK\Exceptions\InvalidApiUriException;

// Set the base uri for your environment. Default is https://auth-api.fayda.et
//FaydaApi::setBaseUri('https://prod.fayda.et');

try {

    $api = new Otp();

    $transactionId = time(); // unique transaction id
    $individualId = ''; // your Fayda FIN/FCN
    
    $result = $api->requestNew($transactionId, $individualId);
    
    print "============ OTP Request Result ============\n";
    print json_encode($result) . "\n\n";
    
    $otp = readline("Enter OTP: ");
    
    print "============  eKyc ============\n";
    $dataKyc = new DataKyc();
    $authentication = $dataKyc->authenticate(
        $result['transactionID'], // transactionID from the previous request
        $individualId,
        $otp,
        [
            'otp' => false,
            'demo' => true,
            'bio' => false,
        ]
    );
    print json_encode($authentication) . "\n\n";
    
} catch (HttpException $e) {
    print $e->getMessage();
} catch (BusinessException $e) {
    print $e->getMessage();
} catch (InvalidApiUriException $e) {
    print $e->getMessage();
}

```

## Using Docker

Set up docker environment.

1. `cp .env.example .env`
2. edit `.env` file with your credentials.
3. `docker-compose up -d`

Run the examples inside docker. See the output on console to verify the results.

#### Request OTP and do kyc

`docker-compose exec fayda php ./examples/Example.php`

## Run tests

> Modify your API key in `phpunit.xml` first.

```shell
# Add your API configuration items into the environmental variable first     
        
export FAYDA_BASE_URL=https://dev.fayda.et

export FAYDA_VERSION=1.0
export FAYDA_ENV=prod

export FAYDA_SKIP_VERIFY_TLS=0
export FAYDA_DEBUG_MODE=1

composer test
```

## License

[MIT](LICENSE)

![Ethiopian National ID](nid_logo.png "Fayda")
