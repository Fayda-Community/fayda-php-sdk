<?php

include './vendor/autoload.php';

use Fayda\SDK\Utils\Crypto;


$cert = file_get_contents(getenv('FAYDA_CERT_PATH'));
$key = file_get_contents(getenv('FAYDA_KEYPAIR_PATH'));
$passphrase = getenv('FAYDA_P12_PASSWORD');

$crypto = new Crypto($cert, $key, $passphrase);

try {
    // Example request payload
    $payload = json_encode([
        "id" => "fayda.identity.otp",
        "requestTime" => "2022-08-29T19:25:42.289+05:30",
        "env" => "Developer",
        "version" => "1.0",
        "domainUri" => "https://dev.fayda.et",
        "transactionID" => "1234512345",
        "individualId" => "4157164106193802",
        "individualIdType" => "VID",
        "otpChannel" => [
            "PHONE"
        ]
    ]);

    $signature = $crypto->sign($payload);
    print "============ SIGNATURE ============\n";
    print $signature . "\n\n";


    // Example data to encrypt
    $rawRequest = json_encode([
        "otp" => '111111',
        "requestTime" => "2022-10-27T07:05:07.867Z",
    ]);


    // generated Symmetric Key and encrypt it using Fayda public key, to be used for encryption of the request
    $requestSessionKey = $crypto->requestSessionKey();
    print "============ Request Session Key ============\n";
    print $requestSessionKey . "\n\n";

    $request = $crypto->encrypt($rawRequest, $requestSessionKey);
    print "============ Encrypted Request ============\n";
    print $request . "\n\n";

    $requestHmac = $crypto->generateHmac($rawRequest, $requestSessionKey);
    print "============ Request HMAC ============\n";
    print $requestHmac . "\n\n";


    // Thumbprint of public key certificate used for encryption of the requestSessionKey.
    // This might be for Fayda to identify the public key used for encryption.
    $thumbprint = $crypto->certThumbprint();
    print "============ Thumbprint ============\n";
    print $thumbprint . "\n\n";

} catch (Exception $e) {
    print $e->getMessage();
}

