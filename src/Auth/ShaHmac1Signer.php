<?php

namespace Aliyun\Core\Auth;

/**
 * Class ShaHmac1Signer
 * @package Aliyun\Core\Auth
 */
class ShaHmac1Signer implements ISigner
{
    /**
     * @param string $source
     * @param string $accessSecret
     * @return string
     */
    public function signString(string $source, string $accessSecret): string
    {
        return base64_encode(hash_hmac('sha1', $source, $accessSecret, true));
    }

    /**
     * @return string
     */
    public function getSignatureMethod(): string
    {
        return "HMAC-SHA1";
    }

    /**
     * @return string
     */
    public function getSignatureVersion(): string
    {
        return "1.0";
    }

}