<?php

namespace Fayda\SDK\Api;

use Fayda\SDK\Exceptions\HttpException;
use Fayda\SDK\Exceptions\InvalidApiUriException;
use Fayda\SDK\FaydaApi;
use Fayda\SDK\Http\ApiResponse;

/**
 * Class IdAuthentication
 *
 * @package Fayda\SDK\PrivateApi
 */
abstract class IdAuthentication extends FaydaApi
{

    const INDIVIDUAL_TYPE_FCN = 'FCN';
    const INDIVIDUAL_TYPE_FIN = 'FIN';

    /**
     * @throws HttpException
     * @throws InvalidApiUriException
     */
    public function callWithDefaults(string $method, string $uri, array $params = []): ApiResponse
    {
        $defaultParams = [
            'env' => getenv('FAYDA_ENV') ?: self::FAYDA_ENV_PROD,
            'version' => getenv('FAYDA_VERSION') ?: '1.0',
            'requestTime' => date(self::DATE_FORMAT),
        ];

        $params = array_merge($defaultParams, $params);
        return $this->call($method, rtrim($uri, '/'), $params);

    }




}
