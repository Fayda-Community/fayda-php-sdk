<?php

namespace Fayda\SDK\Api;

use Fayda\SDK\Exceptions\BusinessException;
use Fayda\SDK\Exceptions\EncryptionException;
use Fayda\SDK\Exceptions\HttpException;
use Fayda\SDK\Exceptions\InvalidApiUriException;
use Fayda\SDK\Http\Request;

/**
 * Class Resident
 *
 * @package Fayda\SDK\PrivateApi
 *
 * @see https://nidp.atlassian.net/wiki/spaces/FAPIQ/pages/633733136/Fayda+Platform+API+Specification
 */
class Resident extends IdAuthentication
{

    const LANG_CODE_EN = 'eng';
    const LANG_CODE_AM = 'amh';

    private static $idYesNo = 'fayda.identity.auth';

    private static $idKyc = 'fayda.identity.kyc';

    /**
     * Resident Authentication Yes/No given OTP
     *
     * @throws BusinessException
     * @throws HttpException
     * @throws InvalidApiUriException
     * @throws EncryptionException
     */
    public function authenticateYesNo(
        string $transactionID,
        string $individualId,
        string $otp,
        string $individualType = self::INDIVIDUAL_TYPE_VID,
        bool $requestOtp = true,
        bool $requestDemo = false,
        bool $requestBio = false,
        bool $consentObtained = true
    ) {

        $params = $this->buildRequest(
            $transactionID,
            $individualId,
            $otp,
            $individualType,
            $requestOtp,
            $requestDemo,
            $requestBio,
            $consentObtained
        );


        $params = array_merge($params, [
            'id' => static::$idYesNo,
        ]);

        $response = $this->callWithDefaults(Request::METHOD_POST, '/idauthentication/v1/otp', $params);

        return $response->getApiData();
    }


    /**
     * Resident Authentication eKYC given OTP
     *
     * @throws BusinessException
     * @throws HttpException
     * @throws InvalidApiUriException
     * @throws EncryptionException
     */
    public function authenticateKyc(
        string $transactionID,
        string $individualId,
        string $otp,
        string $individualType = self::INDIVIDUAL_TYPE_VID,
        bool $requestOtp = true,
        bool $requestDemo = false,
        bool $requestBio = false,
        bool $consentObtained = true
    ): array {

        $params = $this->buildRequest(
            $transactionID,
            $individualId,
            $otp,
            $individualType,
            $requestOtp,
            $requestDemo,
            $requestBio,
            $consentObtained
        );

        $params = array_merge($params, [
            'id' => static::$idKyc,
        ]);


        $response = $this->callWithDefaults(Request::METHOD_POST, '/idauthentication/v1/kyc', $params);

        return $response->getApiData();
    }


    /**
     *
     * @throws EncryptionException
     */
    private function buildRequest(
        string $transactionID,
        string $individualId,
        string $otp,
        string $individualType,
        bool $requestOtp,
        bool $requestDemo,
        bool $requestBio,
        bool $consentObtained
    ): array {
        $requestTime = date(self::DATE_FORMAT);

        $requestSessionKey = $this->auth->requestSessionKey();

        $request = json_encode([
            'otp' => $otp,
            'timestamp' => $requestTime,
            'demographics' => null, // TODO(Anteneh): what is the use case for this?
            'biometrics' => null, // TODO(Anteneh): implement this
        ]);

        return array_merge([
            'secondaryLangCode' => self::LANG_CODE_EN,
            'requestTime' => $requestTime,
            'requestedAuth' => [
                'otp' => $requestOtp,
                'demo' => $requestDemo,
                'bio' => $requestBio,
            ],
            'thumbprint' => $this->auth->thumbprint(),
            'requestSessionKey' => $requestSessionKey,
            'requestHMAC' => $this->auth->requestHMAC($request, $requestSessionKey),
            'request' => $this->auth->encodedRequest($request, $requestSessionKey),
        ], compact('transactionID', 'individualId', 'individualType', 'consentObtained'));
    }

}
