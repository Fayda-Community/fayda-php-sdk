<?php

include './vendor/autoload.php';

use Fayda\SDK\Api\PartnerAuthentication;
use Fayda\SDK\Exceptions\HttpException;
use Fayda\SDK\Exceptions\InvalidApiUriException;
use Fayda\SDK\FaydaApi;

// Set the base uri for production environment.
//FaydaApi::setBaseUri('https://prod.fayda.et');

FaydaApi::setBaseUri(getenv('FAYDA_BASE_URL'));
FaydaApi::setSkipVerifyTls(boolval(getenv('FAYDA_SKIP_VERIFY_TLS')));
FaydaApi::setDebugMode(boolval(getenv('FAYDA_DEBUG_MODE')));
FaydaApi::setLogPath(getenv('LOG_DIR'));

$partnerAuthenticator = new PartnerAuthentication();

try {

    $appId = getenv('FAYDA_APP_ID');
    $clientId = getenv('FAYDA_CLIENT_ID');
    $secretKey = getenv('FAYDA_SECRET_KEY');

    $authKey = $partnerAuthenticator->authenticate($clientId, $secretKey, $appId);

    print "============ Partner Auth Key ============\n";
    print $authKey . "\n\n";

} catch (HttpException|InvalidApiUriException $e) {
    print $e->getMessage();
}
