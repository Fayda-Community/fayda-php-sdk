# PHP SDK - Fayda Platform

❗This repository is work in progress. It is not ready yet and may change a lot.

> Read the official specification
>
document [Fayda Platform API Specification](https://nidp.atlassian.net/wiki/spaces/FAPIQ/pages/633733136/Fayda+Platform+API+Specification).
> In order to receive the latest change notifications, please `Watch` this repository.

[![Latest Version](https://img.shields.io/github/release/Fayda-Community/fayda-php-sdk.svg)](https://github.com/Fayda-Community/fayda-php-sdk/releases)
[![PHP Version](https://img.shields.io/packagist/php-v/fayda/fayda-php-sdk.svg?color=green)](https://secure.php.net)
[![Build Status](https://travis-ci.org/fayda/fayda-php-sdk.svg?branch=master)](https://travis-ci.org/fayda/fayda-php-sdk)
[![Total Downloads](https://poser.pugx.org/fayda/fayda-php-sdk/downloads)](https://packagist.org/packages/fayda/fayda-php-sdk)
[![License](https://poser.pugx.org/fayda/fayda-php-sdk/license)](LICENSE)
<!-- [![Total Lines](https://tokei.rs/b1/github/Fayda-Community/fayda-php-sdk)](https://github.com/Fayda-Community/fayda-php-sdk) -->
<!-- [![Packagist](https://img.shields.io/packagist/dt/fayda/fayda-php-sdk.svg)](https://packagist.org/packages/fayda/fayda-php-sdk) -->
<!-- [![License](https://img.shields.io/packagist/l/fayda/fayda-php-sdk.svg)](LICENSE) -->

## Requirements

| Dependency                                              | Requirement                     |
|---------------------------------------------------------|---------------------------------|
| [PHP](https://secure.php.net/manual/en/install.php)     | `>=7.1 <8.2` `Recommend PHP8+`  |
| [guzzlehttp/guzzle](https://github.com/guzzle/guzzle)   | <code>^6.0 &#124; ^7.0</code>   | 
| [firebase/php-jwt](https://github.com/firebase/php-jwt) | `^6.8`                          |

## Install

> Install package via [Composer](https://getcomposer.org/).

```shell
composer require "fayda/fayda-php-sdk"
```

## Usage

### Choose environment

| Environment   | BaseUri                         |
|---------------|---------------------------------|
| *Production*  | `https://prod.fayda.et`         |
| *Development* | `https://dev.fayda.et`(DEFAULT) |

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

// Set the base uri for your environment. Default is https://dev.fayda.et
//FaydaApi::setBaseUri('https://prod.fayda.et');

try {

    $auth = Auth::init();
    $api = new Otp($auth);

    $transactionId = '1234554321';
    $individualId = '4257964106293892';
    $result = $api->requestNew($transactionId, $individualId);
    print "============ OTP Request Result ============\n";
    print json_encode($result) . "\n\n";
    
} catch (HttpException $e) {
    print $e->getMessage();
} catch (BusinessException $e) {
    print $e->getMessage();
} catch (InvalidApiUriException $e) {
    print $e->getMessage();
}

```

### API list

<details>
<summary>Fayda\SDK\Api\PartnerAuthentication</summary>

| API                                                 | Description                                                                                                                     |
|-----------------------------------------------------|---------------------------------------------------------------------------------------------------------------------------------|
| Fayda\SDK\Api\PartnerAuthentication::authenticate() | https://nidp.atlassian.net/wiki/spaces/FAPIQ/pages/633733136/Fayda+Platform+API+Specification#1.-Client-Authentication--Service |

</details>

<details>
<summary>Fayda\SDK\Api\Otp</summary>

| API                             | Description                                                                                                           |
|---------------------------------|-----------------------------------------------------------------------------------------------------------------------|
| Fayda\SDK\Api\Otp::requestNew() | https://nidp.atlassian.net/wiki/spaces/FAPIQ/pages/633733136/Fayda+Platform+API+Specification#2.--OTP-Request-Service |

</details>

<details>
<summary>Fayda\SDK\Api\Resident</summary>

| API                                         | Description                                                                                                                       |
|---------------------------------------------|-----------------------------------------------------------------------------------------------------------------------------------|
| Fayda\SDK\Api\Resident::authenticateYesNo() | https://nidp.atlassian.net/wiki/spaces/FAPIQ/pages/633733136/Fayda+Platform+API+Specification#3.-Resident-Authentication--Service |
| Fayda\SDK\Api\Resident::authenticateKyc()   | https://nidp.atlassian.net/wiki/spaces/FAPIQ/pages/633733136/Fayda+Platform+API+Specification#4.-Resident-e-KYC-Service           |

</details>

## Using Docker

Set up docker environment.

You can put the `.cert` and `.p12` files in the `examples/creds` folder.

1. `cp .env.example .env`
2. edit `.env` file with your credentials.
3. `docker-compose up -d`

Run the examples inside docker. See the output on console to verify the results.

#### Signing and encryption examples

`docker-compose exec fayda php ./examples/Cert.php`

#### Otp request example

`docker-compose exec fayda php ./examples/Otp.php`

#### Resident authentication examples

`docker-compose exec fayda php ./examples/Resident.php`

## Run tests

> Modify your API key in `phpunit.xml` first.

```shell
# Add your API configuration items into the environmental variable first     
        
export FAYDA_BASE_URL=https://dev.fayda.et

export FAYDA_AUTH_KEY=auth-key
export FAYDA_APP_ID=app-id
export FAYDA_CLIENT_ID=client-id
export FAYDA_SECRET_KEY=secret-key

export FAYDA_FISP_KEY=fisp
export FAYDA_PARTNER_ID=partner-id
export FAYDA_API_KEY=api-key

export FAYDA_CERT=cert

export FAYDA_KEYPAIR=p12
export FAYDA_P12_PASSWORD=passphrase

export FAYDA_VERSION=1.0
export FAYDA_ENV=Developer

export FAYDA_SKIP_VERIFY_TLS=0
export FAYDA_DEBUG_MODE=1

composer test
```

## License

[MIT](LICENSE)

![Ethiopian National ID](nid_logo.png "Fayda")
