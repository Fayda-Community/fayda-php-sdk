<?php

include './vendor/autoload.php';

use Fayda\SDK\Api\PartnerAuthentication;
use Fayda\SDK\Exceptions\HttpException;
use Fayda\SDK\Exceptions\InvalidApiUriException;

// Set the base uri for production environment.
//FaydaApi::setBaseUri('https://prod.fayda.et');

$partnerAuthenticator = new PartnerAuthentication();

try {

    $appId = getenv('FAYDA_APP_ID');
    $clientId = getenv('FAYDA_CLIENT_ID');
    $secretKey = getenv('FAYDA_SECRET_KEY');

    $authKey = $partnerAuthenticator->authenticate($clientId, $secretKey, $appId);

    print "============ Partner Auth Key ============\n";
    print $authKey . "\n\n";

} catch (HttpException|InvalidApiUriException $e) {
    var_dump($e->getMessage());
}
