<?php

namespace Fayda\SDK;

use Fayda\SDK\Http\ApiResponse;

abstract class FaydaApi extends Api
{

    const DATE_FORMAT = 'Y-m-d\TH:i:s.vP';

    const FAYDA_ENV_PROD = 'prod';

    /**
     * @var string
     */
    protected static $domainUri;

    public static function getDomainUri(): string
    {
        return static::$domainUri ?? static::$baseUri;
    }

    public static function setDomainUri(string $domainUri)
    {
        static::$domainUri = $domainUri;
    }


    /**
     * Call an API
     *
     * @throws Exceptions\HttpException
     * @throws Exceptions\InvalidApiUriException
     */
    public function call(
        string $method,
        string $uri,
        array $params = [],
        array $headers = [],
        int $timeout = 30
    ): ApiResponse {
        $response = parent::call($method, $uri, $params, $headers, $timeout);

        return new ApiResponse($response);
    }
}
