<?php

include './vendor/autoload.php';

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

    $transactionId = '1234554321';
    $individualId = '4257964106293892';
    $result = $api->requestNew($transactionId, $individualId);
    print "============ OTP Request Result ============\n";
    print json_encode($result) . "\n\n";

} catch (HttpException|BusinessException|InvalidApiUriException $e) {
    print $e->getMessage();
}
