<?php

namespace Aliyun\Core;

use Aliyun\Core\Auth\Credential;
use Aliyun\Core\Auth\ISigner;

/**
 * Class AcsRequest
 * @package Aliyun\Core
 */
abstract class AcsRequest
{
    /**
     * @var string
     */
    protected $version;
    /**
     * @var string
     */
    protected $product;
    /**
     * @var string
     */
    protected $actionName;
    /**
     * @var string
     */
    protected $regionId;
    /**
     * @var string
     */
    protected $acceptFormat;
    /**
     * @var string
     */
    protected $method;
    /**
     * @var string
     */
    protected $protocolType = "http";
    /**
     * @var string
     */
    protected $content;

    /**
     * @var array
     */
    protected $queryParameters = [];
    /**
     * @var array
     */
    protected $headers = [];

    /**
     * AcsRequest constructor.
     * @param string $product
     * @param string $version
     * @param string $actionName
     */
    function __construct(string $product, string $version, string $actionName)
    {
        $this->headers["x-sdk-client"] = "php/2.0.0";
        $this->product = $product;
        $this->version = $version;
        $this->actionName = $actionName;
    }

    /**
     * @param ISigner $iSigner
     * @param Credential $credential
     * @param string $domain
     * @return mixed
     */
    public abstract function composeUrl(ISigner $iSigner, Credential $credential, string $domain): string;

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion(string $version)
    {
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getProduct(): string
    {
        return $this->product;
    }

    /**
     * @param string $product
     */
    public function setProduct(string $product)
    {
        $this->product = $product;
    }

    /**
     * @return string
     */
    public function getActionName(): string
    {
        return $this->actionName;
    }

    /**
     * @param string $actionName
     */
    public function setActionName(string $actionName)
    {
        $this->actionName = $actionName;
    }

    /**
     * @return string
     */
    public function getAcceptFormat(): string
    {
        return $this->acceptFormat;
    }

    /**
     * @param string $acceptFormat
     */
    public function setAcceptFormat(string $acceptFormat)
    {
        $this->acceptFormat = $acceptFormat;
    }

    /**
     * @return array
     */
    public function getQueryParameters()
    {
        return $this->queryParameters;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod(string $method)
    {
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getProtocol(): string
    {
        return $this->protocolType;
    }

    /**
     * @param string $protocol
     */
    public function setProtocol(string $protocol)
    {
        $this->protocolType = $protocol;
    }

    /**
     * @return string
     */
    public function getRegionId(): ?string
    {
        return $this->regionId;
    }

    /**
     * @param string $region
     */
    public function setRegionId(string $region)
    {
        $this->regionId = $region;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content)
    {
        $this->content = $content;
    }

    /**
     * @param string $headerKey
     * @param $headerValue
     */
    public function addHeader(string $headerKey, $headerValue)
    {
        $this->headers[$headerKey] = $headerValue;
    }


}