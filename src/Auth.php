<?php

namespace Fayda\SDK;

use Fayda\SDK\Api\PartnerAuthentication;
use Fayda\SDK\Exceptions\CertificateException;
use Fayda\SDK\Exceptions\EncryptionException;
use Fayda\SDK\Exceptions\HttpException;
use Fayda\SDK\Exceptions\InvalidApiUriException;
use Fayda\SDK\Utils\Crypto;

class Auth implements IAuth
{
    /**
     * @var string
     */
    private $authKey;

    /**
     * @var Crypto
     */
    private $crypto;

    public function __construct($authKey, $crypto)
    {
        $this->authKey = $authKey;
        $this->crypto = $crypto;
    }

    /**
     * @throws HttpException
     * @throws InvalidApiUriException
     */
    public static function init(): Auth
    {
        // signing/crypto credentials
        $faydaKey = file_get_contents(getenv('FAYDA_KEYPAIR_PATH'));
        $faydaPassphrase = getenv('FAYDA_P12_PASSWORD');

        $faydaCert = file_get_contents(getenv('FAYDA_CERT_PATH'));

        // partner authentication credentials
        $appId = getenv('FAYDA_APP_ID');
        $clientId = getenv('FAYDA_CLIENT_ID');
        $secretKey = getenv('FAYDA_SECRET_KEY');

        // Do partner authentication first
        $partnerAuthenticator = new PartnerAuthentication();
        $authKey = $partnerAuthenticator->authenticate($clientId, $secretKey, $appId);

        // TODO(Anteneh): Do check the lifetime of the auth key and introduce some caching for subsequent requests

        $crypto = new Crypto($faydaCert, $faydaKey, $faydaPassphrase);

        return new self($authKey, $crypto);
    }

    /**
     * @throws CertificateException
     */
    public function getHeaders(string $method, string $requestUri, string $body): array
    {
        return [
            'Authorization' => $this->authKey,
            'Signature' => $this->crypto->sign($body),
        ];
    }

    /**
     *
     * @throws EncryptionException
     */
    public function requestHMAC(string $request, string $requestSessionKey): string
    {
        return $this->crypto->generateHmac($request, $requestSessionKey);
    }

    /**
     * @throws EncryptionException
     */
    public function encodedRequest(string $request, $requestSessionKey): string
    {
        return $this->crypto->encrypt($request, $requestSessionKey);
    }

    public function thumbprint(): string
    {
        return $this->crypto->certThumbprint();
    }

    /**
     * @throws EncryptionException
     */
    public function requestSessionKey(): string
    {
        return $this->crypto->requestSessionKey();
    }
}
