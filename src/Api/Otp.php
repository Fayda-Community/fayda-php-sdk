<?php

namespace Fayda\SDK\Api;

use Fayda\SDK\Exceptions\BusinessException;
use Fayda\SDK\Exceptions\HttpException;
use Fayda\SDK\Exceptions\InvalidApiUriException;
use Fayda\SDK\Http\Request;

/**
 * Class Otp
 *
 * @package Fayda\SDK\PrivateApi
 *
 * @see https://nidp.atlassian.net/wiki/spaces/FAPIQ/pages/633733136/Fayda+Platform+API+Specification
 */
class Otp extends IdAuthentication
{
    const OTP_CHANNEL_PHONE = 'PHONE';
    const OTP_CHANNEL_EMAIL = 'EMAIL';

    private static $id = 'fayda.identity.otp';

    public static function setId(string $id): void
    {
        static::$id = $id;
    }

    /**
     * OTP Request
     *
     * @throws BusinessException
     * @throws HttpException
     * @throws InvalidApiUriException
     */
    public function requestNew(
        string $transactionID,
        string $individualId,
        string $individualIdType = self::INDIVIDUAL_TYPE_VID,
        string $otpChannel = self::OTP_CHANNEL_PHONE
    ): array {
        $params = array_merge([
            'id' => static::$id,
            'domainUri' => static::getDomainUri(),
            'otpChannel' => [$otpChannel],
        ], compact('transactionID', 'individualId', 'individualIdType'));

        $response = $this->callWithDefaults(Request::METHOD_POST, '/idauthentication/v1/otp', $params);

        return $response->getApiData();
    }

}
