<?php

namespace Fayda\SDK;

interface IAuth
{
    public function getHeaders(string $method, string $requestUri, string $body);
}
