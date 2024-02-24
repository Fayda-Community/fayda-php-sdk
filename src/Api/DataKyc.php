<?php

namespace Fayda\SDK\Api;

use Fayda\SDK\Exceptions\BusinessException;
use Fayda\SDK\Exceptions\HttpException;
use Fayda\SDK\Exceptions\InvalidApiUriException;
use Fayda\SDK\Http\Request;
use Fayda\SDK\FaydaApi;

/**
 * Class PartnerAuthentication
 *
 * @package Fayda\SDK\PrivateApi
 *
 */
class DataKyc extends FaydaApi
{
    /**
     * Authenticate a partner
     *
     * @throws HttpException
     * @throws InvalidApiUriException
     * @throws BusinessException
     */
    public function authenticate(
        string $transactionID,
        string $individualId,
        string $otpCode,
        array $requestedAuth
    ): array {
        $params = array_merge([
            'env' => getenv('FAYDA_ENV') ?: self::FAYDA_ENV_PROD,
            'domainUri' => 'fayda.et',
            'version' => getenv('FAYDA_VERSION') ?: '1.0',
            'requestTime' => date(self::DATE_FORMAT),
            'consentObtained' => true,
            'thumbprint' => '',
            'requestSessionKey' => '',
            'requestHMAC' => '',
            'requestedAuth' => $requestedAuth,
            'request' => [
                "timestamp" => date(self::DATE_FORMAT),
                "otp" => $otpCode,
            ],
            'individualIdType' => strlen($individualId) === 16 ? IdAuthentication::INDIVIDUAL_TYPE_FCN : IdAuthentication::INDIVIDUAL_TYPE_FIN,
        ], compact('transactionID', 'individualId'));


        $response = $this->call(Request::METHOD_POST, '/fayda/getDataKyc', $params);

        return [
            'transactionID' => $response->getTransactionID(),
            'data' => $response->getApiData(),
        ];
    }
}
