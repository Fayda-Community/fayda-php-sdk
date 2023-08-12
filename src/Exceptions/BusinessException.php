<?php

namespace Fayda\SDK\Exceptions;

use Fayda\SDK\Http\ApiResponse;

class BusinessException extends \Exception
{
    /**
     * @var ApiResponse $response
     */
    protected $response;


    public function getResponse(): ApiResponse
    {
        return $this->response;
    }

    public function setResponse(ApiResponse $response)
    {
        $this->response = $response;
    }

}
