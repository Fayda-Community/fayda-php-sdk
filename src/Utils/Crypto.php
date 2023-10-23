<?php

namespace Fayda\SDK\Utils;

use Fayda\SDK\Exceptions\CertificateException;
use Fayda\SDK\Exceptions\DecryptionException;
use Fayda\SDK\Exceptions\EncryptionException;
use Firebase\JWT\JWT;

class Crypto
{
    const SIGN_ALG = 'RS256';
    const HASH_ALG = 'SHA256';

    /**
     * TODO(Anteneh): verify via VPN if fayda accepts encryption with this algorithm
     *
     * Fayda states algorithm used for encryption can be RSA/ECB/OAEPWITHSHA-256ANDMGF1PADDING
     */
    const CIPHER_SESSION_KEY = 'aes-128-gcm';

    /**
     * Fayda states algorithm used for encryption can be AES/GCM/PKCS5Padding.
     *
     * But GCM is a stream cipher mode and does not use padding.
     *
     * TODO (Anteneh): check if fayda ignores the PKCS5Padding specification and does not pad, so this has no impact
     * TODO (Anteneh): check via VPN if fayda accepts this algorithm
     */
    const CIPHER_REQUEST = 'aes-128-gcm';

    const SYMMETRIC_KEY_LENGTH = 256;

    /**
     * @var string
     */
    private $cert;

    /**
     * @var string
     */
    private $faydaP12Key;

    /**
     * @var string
     */
    private $faydaP12Password;


    public function __construct(
        string $cert,
        string $faydaP12Key,
        string $faydaP12Password
    ) {
        $this->cert = $cert;
        $this->faydaP12Key = $faydaP12Key;
        $this->faydaP12Password = $faydaP12Password;
    }


    /**
     * @throws CertificateException
     */
    public function sign(string $body): string
    {
        try {
            $p12CertInfo = $this->loadFaydaP12();

            $x5c = str_replace("-----BEGIN CERTIFICATE-----", "", $p12CertInfo['cert']);
            $x5c = str_replace("-----END CERTIFICATE-----", "", $x5c);

            $headers = [
                'alg' => self::SIGN_ALG,
                'typ' => 'JWS',
                'x5c' => [trim($x5c)]
            ];

            return JWT::encode(json_decode($body, true), $p12CertInfo['pkey'], self::SIGN_ALG, null, $headers);

        } catch (\Exception $e) {
            throw new CertificateException($e->getMessage());
        }
    }

    public function certThumbprint(bool $isBinary = true): string
    {
        return base64_encode(bin2hex(openssl_x509_fingerprint($this->cert, self::HASH_ALG, $isBinary)));
    }


    /**
     * @throws EncryptionException
     */
    public function generateHmac(string $data, string $secret, bool $isBinary = true): string
    {
        $hashmap = hash(self::HASH_ALG, $data, $isBinary);

        return $this->encrypt($hashmap, $secret);
    }

    /**
     *
     * @throws CertificateException
     */
    public function loadFaydaP12(): array
    {
        return $this->loadP12($this->faydaP12Key, $this->faydaP12Password);
    }

    private function loadP12($key, $passphrase): array
    {
        if (openssl_pkcs12_read($key, $p12CertInfo, $passphrase)) {

            /**
             * $p12CertInfo['pkey']  //private key
             * $p12CertInfo['cert']  //public key
             */

            return $p12CertInfo;

        }

        throw new CertificateException(sprintf('Error: Unable to read the cert store, Message: %s',
            openssl_error_string()));

    }


    private function randomSessionKey()
    {
        return openssl_random_pseudo_bytes(static::SYMMETRIC_KEY_LENGTH);
    }


    /**
     * @throws EncryptionException
     */
    public function requestSessionKey(): string
    {
        $randomSecret = $this->randomSessionKey();

        return $this->encrypt($randomSecret, $this->cert, self::CIPHER_SESSION_KEY, OPENSSL_PKCS1_OAEP_PADDING);
    }

    /**
     * Encrypt text for by a given algorithm
     *
     * @throws EncryptionException
     */
    public function encrypt(
        string $data,
        string $secret,
        string $cipherAlg = self::CIPHER_REQUEST,
        $option = OPENSSL_PKCS1_PADDING
    ): string {
        try {
            if (!in_array($cipherAlg, openssl_get_cipher_methods())) {
                throw new EncryptionException('Cipher method not supported: ' . $cipherAlg);
            }


            // Get random initialization vector

            $ivLength = openssl_cipher_iv_length($cipherAlg);

            $secret_iv1 = openssl_random_pseudo_bytes($ivLength);
            $secret_iv = bin2hex($secret_iv1);
            $initVector = substr(hash(self::HASH_ALG, $secret_iv), 0, $ivLength);

            // Encrypt input text
            $raw = openssl_encrypt(
                $data,
                $cipherAlg,
                $secret,
                $option,
                $initVector,
                $tag
            );

            if ($raw === false) {
                throw new EncryptionException(openssl_error_string());
            }

            // Return base64-encoded string: encrypted result
            return base64_encode($raw);

        } catch (\Exception $e) {
            throw new EncryptionException($e->getMessage());
        }
    }

    /**
     * Decrypt encoded text for a given algorithm
     *
     * @throws DecryptionException
     */
    public function decrypt(
        string $data,
        string $secret,
        string $cipherAlg = self::CIPHER_REQUEST,
        $option = OPENSSL_RAW_DATA
    ): string {
        try {
            if (!in_array($cipherAlg, openssl_get_cipher_methods())) {
                throw new EncryptionException('Cipher method not supported: ' . $cipherAlg);
            }


            $encoded = base64_decode($data);

            $ivLength = openssl_cipher_iv_length($cipherAlg);

            // Slice initialization vector
            $initVector = substr($encoded, 0, $ivLength);

            // Slice encoded data
            $data = substr($encoded, $ivLength);

            // Trying to get decrypted text
            $decoded = openssl_decrypt(
                $data,
                $cipherAlg,
                $secret,
                $option,
                $initVector
            );

            if ($decoded === false) {
                throw new DecryptionException(openssl_error_string());
            }

            // Return successful decoded object
            return $decoded;
        } catch (\Exception $e) {
            throw new DecryptionException($e->getMessage());
        }
    }

}
