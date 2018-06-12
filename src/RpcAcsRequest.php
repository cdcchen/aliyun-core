<?php

namespace Aliyun\Core;

use Aliyun\Core\Auth\Credential;
use Aliyun\Core\Auth\ISigner;

/**
 * Class RpcAcsRequest
 * @package Aliyun\Core
 */
abstract class RpcAcsRequest extends AcsRequest
{
    /**
     * @var string
     */
    private $dateTimeFormat = 'Y-m-d\TH:i:s\Z';
    /**
     * @var array
     */
    private $domainParameters = [];

    /**
     * RpcAcsRequest constructor.
     * @param string $product
     * @param string $version
     * @param string $actionName
     */
    function __construct(string $product, string $version, string $actionName)
    {
        parent::__construct($product, $version, $actionName);
        $this->initialize();
    }

    /**
     * init
     */
    private function initialize()
    {
        $this->setMethod('GET');
        $this->setAcceptFormat('JSON');
    }


    /**
     * @param $value
     * @return string
     */
    private function prepareValue($value)
    {
        if (is_bool($value)) {
            if ($value) {
                return 'true';
            } else {
                return 'false';
            }
        } else {
            return $value;
        }
    }

    /**
     * @param ISigner $iSigner
     * @param Credential $credential
     * @param string $domain
     * @return string
     */
    public function composeUrl(ISigner $iSigner, Credential $credential, string $domain): string
    {
        $apiParams = parent::getQueryParameters();
        foreach ($apiParams as $key => $value) {
            $apiParams[$key] = $this->prepareValue($value);
        }
        $apiParams["RegionId"] = $this->getRegionId();
        $apiParams["AccessKeyId"] = $credential->getAccessKeyId();
        $apiParams["Format"] = $this->getAcceptFormat();
        $apiParams["SignatureMethod"] = $iSigner->getSignatureMethod();
        $apiParams["SignatureVersion"] = $iSigner->getSignatureVersion();
        $apiParams["SignatureNonce"] = uniqid(mt_rand(0, 0xffff), true);
        $apiParams["Timestamp"] = gmdate($this->dateTimeFormat);
        $apiParams["Action"] = $this->getActionName();
        $apiParams["Version"] = $this->getVersion();
        $apiParams["Signature"] = $this->computeSignature($apiParams, $credential->getAccessSecret(), $iSigner);
        if (parent::getMethod() == "POST") {

            $requestUrl = $this->getProtocol() . "://" . $domain . "/";
            foreach ($apiParams as $apiParamKey => $apiParamValue) {
                $this->putDomainParameters($apiParamKey, $apiParamValue);
            }
            return $requestUrl;
        } else {
            $requestUrl = $this->getProtocol() . "://" . $domain . "/?";

            foreach ($apiParams as $apiParamKey => $apiParamValue) {
                $requestUrl .= "$apiParamKey=" . urlencode($apiParamValue) . "&";
            }
            return substr($requestUrl, 0, -1);
        }
    }

    /**
     * @param array $parameters
     * @param string $accessKeySecret
     * @param ISigner $iSigner
     * @return string
     */
    private function computeSignature(array $parameters, string $accessKeySecret, ISigner $iSigner): string
    {
        ksort($parameters);
        $canonicalizedQueryString = '';
        foreach ($parameters as $key => $value) {
            $canonicalizedQueryString .= '&' . $this->percentEncode($key) . '=' . $this->percentEncode($value);
        }
        $stringToSign = parent::getMethod() . '&%2F&' . $this->percentencode(substr($canonicalizedQueryString, 1));
        $signature = $iSigner->signString($stringToSign, $accessKeySecret . "&");

        return $signature;
    }

    /**
     * @param string $str
     * @return string
     */
    protected function percentEncode(string $str): string
    {
        $res = urlencode($str);
        $res = preg_replace('/\+/', '%20', $res);
        $res = preg_replace('/\*/', '%2A', $res);
        $res = preg_replace('/%7E/', '~', $res);
        return $res;
    }

    /**
     * @return array
     */
    public function getDomainParameter(): array
    {
        return $this->domainParameters;
    }

    /**
     * @param string $name
     * @param $value
     */
    public function putDomainParameters(string $name, $value)
    {
        $this->domainParameters[$name] = $value;
    }

}
