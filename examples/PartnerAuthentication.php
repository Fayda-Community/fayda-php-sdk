<?php

include '../vendor/autoload.php';

use Fayda\SDK\Api\PartnerAuthentication;
use Fayda\SDK\Exceptions\BusinessException;
use Fayda\SDK\Exceptions\HttpException;
use Fayda\SDK\Exceptions\InvalidApiUriException;

// Set the base uri for production environment.
//FaydaApi::setBaseUri('https://prod.fayda.et');

$partnerAuthenticator = new PartnerAuthentication();

try {

    $appId = getenv('FAYDA_APP_ID') ?: 'ida';
    $clientId = getenv('FAYDA_CLIENT_ID') ?: 'fayda-ida-client';
    $secretKey = getenv('FAYDA_SECRET_KEY') ?: 'f475c70c-40b6-47ce-ad59-2074210f5cec';

    $authKey = $partnerAuthenticator->authenticate($clientId, $secretKey, $appId);

    var_dump($authKey);

} catch (HttpException $e) {
    var_dump($e->getMessage());
} catch (BusinessException $e) {
    var_dump($e->getMessage());
} catch (InvalidApiUriException $e) {
    var_dump($e->getMessage());
}
