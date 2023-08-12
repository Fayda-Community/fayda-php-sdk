<?php

namespace Fayda\SDK\Api;

use Fayda\SDK\Exceptions\BusinessException;
use Fayda\SDK\Exceptions\HttpException;
use Fayda\SDK\Exceptions\InvalidApiUriException;
use Fayda\SDK\Http\Request;
use Fayda\SDK\FaydaApi;

/**
 * Class PartnerAuthentication
 *
 * @package Fayda\SDK\PrivateApi
 *
 * @see https://nidp.atlassian.net/wiki/spaces/FAPIQ/pages/633733136/Fayda+Platform+API+Specification
 */
class PartnerAuthentication extends FaydaApi
{

    private static $id = 'partner-auth';

    /**
     * Authenticate a partner
     *
     * @throws HttpException
     * @throws InvalidApiUriException
     */
    public function authenticate(string $clientId, string $secretKey, string $appId): string
    {
        $params = [
            'id' => static::$id,
            'version' => getenv('FAYDA_VERSION') ?: '1.0',
            'requestTime' => date(self::DATE_FORMAT),
            'request' => compact('clientId', 'secretKey', 'appId')
        ];


        $response = $this->call(Request::METHOD_POST, 'v1/authmanager/authenticate/clientidsecretkey', $params);

        /**
         * Fayda puts the authorization string in the cookie
         */
        $headers = $response->getHeaders();

        return $headers['Authorization'][0];
    }
}
