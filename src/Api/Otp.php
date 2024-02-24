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
    const OTP_CHANNEL_PHONE = 'phone';
    const OTP_CHANNEL_EMAIL = 'email';


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
        string $otpChannel = self::OTP_CHANNEL_PHONE
    ): array {
        $params = array_merge([
            'domainUri' => 'fayda.et',
            'otpChannel' => [$otpChannel],
            'individualIdType' => strlen($individualId) === 16 ? self::INDIVIDUAL_TYPE_FCN : self::INDIVIDUAL_TYPE_FIN,
        ], compact('transactionID', 'individualId'));

        $response = $this->callWithDefaults(Request::METHOD_POST, '/fayda/requestData', $params);

        return [
            'transactionID' => $response->getTransactionID(),
            'data' => $response->getApiData(),
        ];
    }

}
