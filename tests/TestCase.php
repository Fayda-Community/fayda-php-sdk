<?php

namespace Fayda\SDK\Tests;

use Fayda\SDK\Auth;
use Fayda\SDK\Http\GuzzleHttp;
use Fayda\SDK\FaydaApi;
use Fayda\SDK\Utils\Crypto;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected $apiClass = 'Must be declared in the subclass';
    protected $apiWithAuth = false;

    public function apiProvider()
    {
        $authKey = getenv('FAYDA_AUTH_KEY');
        $cert = getenv('FAYDA_CERT');
        $p12Key = getenv('FAYDA_KEYPAIR');
        $p12Password = getenv('FAYDA_P12_PASSWORD');

        $apiBaseUri = getenv('FAYDA_BASE_URL');
        $apiSkipVerifyTls = (bool)getenv('FAYDA_SKIP_VERIFY_TLS');
        $apiDebugMode = (bool)getenv('FAYDA_DEBUG_MODE');

        FaydaApi::setSkipVerifyTls($apiSkipVerifyTls);
        FaydaApi::setDebugMode($apiDebugMode);
        if ($apiBaseUri) {
            FaydaApi::setBaseUri($apiBaseUri);
        }

        $crypto = new Crypto($cert, $p12Key, $p12Password);
        $auth = new Auth($authKey, $crypto);
        return [
            [new $this->apiClass($this->apiWithAuth ? $auth : null)],
            [
                new $this->apiClass($this->apiWithAuth ? $auth : null,
                    new GuzzleHttp(['skipVerifyTls' => $apiSkipVerifyTls]))
            ],
        ];
    }
}
