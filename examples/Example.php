<?php

include './vendor/autoload.php';

use Fayda\SDK\Api\Otp;
use Fayda\SDK\Api\DataKyc;
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

    print "============ OTP Request ============\n";
    $api = new Otp();
    $transactionId = time();
    $individualId = getenv('FAYDA_TEST_FIN');
    $result = $api->requestNew($transactionId, $individualId);
    print json_encode($result) . "\n\n";

    $otp = readline("Enter OTP: ");

    $dataKyc = new DataKyc();
    print "============ Partner eKyc ============\n";
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

} catch (HttpException|BusinessException|InvalidApiUriException $e) {
    print $e->getMessage();
}
