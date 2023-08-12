<?php

include './vendor/autoload.php';

use Fayda\SDK\Api\Resident;
use Fayda\SDK\Auth;
use Fayda\SDK\Exceptions\BusinessException;
use Fayda\SDK\Exceptions\EncryptionException;
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

    print "============ Resident Authentication Yes/No Result ============\n";
    print $result . "\n\n";

    $result = $api->authenticateKyc($transactionId, $individualId, $otp);
    print "============ Resident Authentication KYC Result ============\n";
    print json_encode($result) . "\n\n";

} catch (HttpException|BusinessException|InvalidApiUriException|EncryptionException $e) {
    print $e->getMessage();
}
