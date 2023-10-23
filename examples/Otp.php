<?php

include './vendor/autoload.php';

use Fayda\SDK\Api\Otp;
use Fayda\SDK\Auth;
use Fayda\SDK\Exceptions\BusinessException;
use Fayda\SDK\Exceptions\HttpException;
use Fayda\SDK\Exceptions\InvalidApiUriException;
use Fayda\SDK\FaydaApi;

// Set the base uri for production environment.
FaydaApi::setBaseUri(getenv('FAYDA_BASE_URL'));
FaydaApi::setSkipVerifyTls(boolval(getenv('FAYDA_SKIP_VERIFY_TLS')));
FaydaApi::setDebugMode(boolval(getenv('FAYDA_DEBUG_MODE')));
FaydaApi::setLogPath(getenv('LOG_DIR'));

try {

    $auth = Auth::init();
    $api = new Otp($auth);
    $api::setId('mosip.identity.otp'); //override the default id

    $transactionId = '1697582000'; //time();
    $individualId = getenv('FAYDA_TEST_VID');

//    $individualId = getenv('FAYDA_TEST_UIN'); // uncomment this line to test with UIN

    $result = $api->requestNew($transactionId, $individualId);
    print "============ OTP Request Result ============\n";
    print json_encode($result) . "\n\n";

} catch (HttpException|BusinessException|InvalidApiUriException $e) {
    print $e->getMessage();
}
