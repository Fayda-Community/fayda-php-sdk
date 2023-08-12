<?php

include '../vendor/autoload.php';

use Fayda\SDK\Api\Otp;
use Fayda\SDK\Auth;
use Fayda\SDK\Exceptions\BusinessException;
use Fayda\SDK\Exceptions\HttpException;
use Fayda\SDK\Exceptions\InvalidApiUriException;

// Set the base uri for production environment.
//FaydaApi::setBaseUri('https://prod.fayda.et');


try {

    $auth = Auth::init();
    $api = new Otp($auth);

    $result = $api->requestNew('1234554321', '4257964106293892');

    var_dump($result);
} catch (HttpException|BusinessException|InvalidApiUriException $e) {
    var_dump($e->getMessage());
}
