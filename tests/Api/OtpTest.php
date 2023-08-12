<?php

namespace Fayda\SDK\Tests\Api;

use Fayda\SDK\Api\Otp;
use Fayda\SDK\Exceptions\BusinessException;
use Fayda\SDK\Exceptions\HttpException;
use Fayda\SDK\Exceptions\InvalidApiUriException;
use Fayda\SDK\Tests\TestCase;

class OtpTest extends TestCase
{
    protected $apiClass = Otp::class;

    /**
     * @dataProvider apiProvider
     *
     * @param Otp $api
     *
     * @throws BusinessException
     * @throws HttpException
     * @throws InvalidApiUriException
     */
    public function testRequestOtp(Otp $api)
    {
        $result = $api->requestNew('1234', '4257964106293892');
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('transactionID', $result);
        $this->assertArrayHasKey('response', $result);
        $this->assertArrayHasKey('maskedMobile', $result['response']);
        $this->assertArrayHasKey('maskedEmail', $result['response']);

    }
}
