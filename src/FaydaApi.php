<?php

namespace Fayda\SDK;

use Fayda\SDK\Http\ApiResponse;

abstract class FaydaApi extends Api
{

    const DATE_FORMAT = 'Y-m-d\TH:i:s.vP';

    const FAYDA_ENV_STAGING = 'Staging';
    const FAYDA_ENV_DEV = 'Developer';
    const FAYDA_ENV_PROD = 'Production';
    const FAYDA_ENV_PRE_PROD = 'Pree-Production';


    /**
     * @var string
     */
    protected static $domainUri;

    /**
     * @return string
     */
    public static function getDomainUri()
    {
        return isset(static::$domainUri) ? static::$domainUri : static::$baseUri;
    }

    /**
     * @param string $domainUri
     */
    public static function setDomainUri($domainUri)
    {
        static::$domainUri = $domainUri;
    }


    /**
     * Call an API
     *
     * @param string $method
     * @param string $uri
     * @param array $params
     * @param array $headers
     * @param int $timeout
     *
     * @return ApiResponse
     * @throws Exceptions\HttpException
     * @throws Exceptions\InvalidApiUriException
     */
    public function call(string $method, string $uri, array $params = [], array $headers = [], int $timeout = 30)
    {
        $response = parent::call($method, $uri, $params, $headers, $timeout);

        return new ApiResponse($response);
    }
}
