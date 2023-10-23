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

    const INDIVIDUAL_TYPE_VID = 'VID';
    const INDIVIDUAL_TYPE_UIN = 'UIN';

    /**
     * @throws HttpException
     * @throws InvalidApiUriException
     */
    public function callWithDefaults(string $method, string $uri, array $params = []): ApiResponse
    {
        $defaultParams = [
            'env' => getenv('FAYDA_ENV') ?: self::FAYDA_ENV_DEV,
            'version' => getenv('FAYDA_VERSION') ?: '1.0',
            'requestTime' => date(self::DATE_FORMAT),
        ];

        $params = array_merge($defaultParams, $params);
        return $this->call($method, $this->buildUri($uri), $params);

    }


    public function buildUri(string $uri): string
    {
        $fispKey = getenv('FAYDA_FISP_KEY');
        $partnerId = getenv('FAYDA_PARTNER_ID');
        $apiKey = getenv('FAYDA_PARTNER_API_KEY');

        return sprintf('%s/%s/%s/%s', rtrim($uri, '/'), $fispKey, $partnerId, $apiKey);
    }

}
