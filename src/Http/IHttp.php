<?php

namespace Fayda\SDK\Http;

use Fayda\SDK\Exceptions\HttpException;
use Fayda\SDK\Exceptions\InvalidApiUriException;

interface IHttp
{
    /**
     * @param Request $request
     * @param float|int $timeout in seconds
     * @return Response
     * @throws HttpException
     * @throws InvalidApiUriException
     */
    public function request(Request $request, $timeout = 30): Response;
}
