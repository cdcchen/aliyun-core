<?php

namespace Aliyun\Core\Auth;

/**
 * Class ShaHmac256Signer
 * @package Aliyun\Core\Auth
 */
class ShaHmac256Signer implements ISigner
{
    /**
     * @param string $source
     * @param string $accessSecret
     * @return string
     */
    public function signString(string $source, string $accessSecret): string
    {
        return base64_encode(hash_hmac('sha256', $source, $accessSecret, true));
    }

    /**
     * @return string
     */
    public function getSignatureMethod(): string
    {
        return "HMAC-SHA256";
    }

    /**
     * @return string
     */
    public function getSignatureVersion(): string
    {
        return "1.0";
    }

}