<?php

include '../vendor/autoload.php';

use Fayda\SDK\Api\Resident;
use Fayda\SDK\Auth;
use Fayda\SDK\Exceptions\BusinessException;
use Fayda\SDK\Exceptions\HttpException;
use Fayda\SDK\Exceptions\InvalidApiUriException;

// Set the base uri for production environment.
//FaydaApi::setBaseUri('https://prod.fayda.et');


try {

    $auth = Auth::init();
    $api = new Resident($auth);

    $transactionId = '5814390537';
    $individualId = '4257964106293892';
    $otp = '111111'; // get this from Otp::requestNew() call for each resident authentication

    $result = $api->authenticateYesNo($transactionId, $individualId, $otp);
    var_dump($result);

    $result = $api->authenticateKyc($transactionId, $individualId, $otp);
    var_dump($result);

} catch (HttpException $e) {
    var_dump($e->getMessage());
} catch (BusinessException $e) {
    var_dump($e->getMessage());
} catch (InvalidApiUriException $e) {
    var_dump($e->getMessage());
}