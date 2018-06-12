<?php

namespace Aliyun\Core;

use Aliyun\Core\Auth\Credential;
use Aliyun\Core\Auth\ISigner;

/**
 * Class RoaAcsRequest
 * @package Aliyun\Core
 */
abstract class RoaAcsRequest extends AcsRequest
{
    /**
     * @var string
     */
    protected $uriPattern;
    /**
     * @var array
     */
    private $pathParameters = [];
    /**
     * @var array
     */
    private $domainParameters = [];
    /**
     * @var string
     */
    private $dateTimeFormat = "D, d M Y H:i:s \G\M\T";
    /**
     * @var string
     */
    private static $headerSeparator = "\n";
    /**
     * @var string
     */
    private static $querySeparator = "&";

    /**
     * RoaAcsRequest constructor.
     * @param string $product
     * @param string $version
     * @param string $actionName
     */
    function __construct(string $product, string $version, string $actionName)
    {
        parent::__construct($product, $version, $actionName);
        $this->setVersion($version);
        $this->initialize();
    }

    /**
     *
     */
    private function initialize()
    {
        $this->setMethod("RAW");
    }

    /**
     * @param ISigner $iSigner
     * @param Credential $credential
     * @param string $domain
     * @return string
     */
    public function composeUrl(ISigner $iSigner, Credential $credential, string $domain): string
    {
        $this->prepareHeader($iSigner);

        $signString = $this->getMethod() . self::$headerSeparator;
        if (isset($this->headers["Accept"])) {
            $signString = $signString . $this->headers["Accept"];
        }
        $signString = $signString . self::$headerSeparator;

        if (isset($this->headers["Content-MD5"])) {
            $signString = $signString . $this->headers["Content-MD5"];
        }
        $signString = $signString . self::$headerSeparator;

        if (isset($this->headers["Content-Type"])) {
            $signString = $signString . $this->headers["Content-Type"];
        }
        $signString = $signString . self::$headerSeparator;

        if (isset($this->headers["Date"])) {
            $signString = $signString . $this->headers["Date"];
        }
        $signString = $signString . self::$headerSeparator;

        $uri = $this->replaceOccupiedParameters();
        $signString = $signString . $this->buildCanonicalHeaders();
        $queryString = $this->buildQueryString($uri);
        $signString .= $queryString;
        $this->headers["Authorization"] = "acs " . $credential->getAccessKeyId() . ":"
            . $iSigner->signString($signString, $credential->getAccessSecret());
        $requestUrl = $this->getProtocol() . "://" . $domain . $queryString;

        return $requestUrl;
    }

    /**
     * @param ISigner $iSigner
     */
    private function prepareHeader(ISigner $iSigner)
    {
        $this->headers["Date"] = gmdate($this->dateTimeFormat);
        if (null == $this->acceptFormat) {
            $this->acceptFormat = "RAW";
        }
        $this->headers["Accept"] = $this->formatToAccept($this->getAcceptFormat());
        $this->headers["x-acs-signature-method"] = $iSigner->getSignatureMethod();
        $this->headers["x-acs-signature-version"] = $iSigner->getSignatureVersion();
        $this->headers["x-acs-region-id"] = $this->regionId;
        $content = $this->getDomainParameter();
        if ($content != null) {
            $this->headers["Content-MD5"] = base64_encode(md5(json_encode($content), true));
        }
        $this->headers["Content-Type"] = "application/octet-stream;charset=utf-8";
    }

    /**
     * @return string
     */
    private function replaceOccupiedParameters(): string
    {
        $result = $this->uriPattern;
        foreach ($this->pathParameters as $pathParameterKey => $apiParameterValue) {
            $target = "[" . $pathParameterKey . "]";
            $result = str_replace($target, $apiParameterValue, $result);
        }
        return $result;
    }

    /**
     * @return string
     */
    private function buildCanonicalHeaders(): string
    {
        $sortMap = [];
        foreach ($this->headers as $headerKey => $headerValue) {
            $key = strtolower($headerKey);
            if (strpos($key, "x-acs-") === 0) {
                $sortMap[$key] = $headerValue;
            }
        }
        ksort($sortMap);
        $headerString = '';
        foreach ($sortMap as $sortMapKey => $sortMapValue) {
            $headerString = $headerString . $sortMapKey . ":" . $sortMapValue . self::$headerSeparator;
        }
        return $headerString;
    }

    /**
     * @param string $uri
     * @return array
     */
    private function splitSubResource(string $uri): array
    {
        $queIndex = strpos($uri, "?");
        $uriParts = [];
        if (null != $queIndex) {
            array_push($uriParts, substr($uri, 0, $queIndex));
            array_push($uriParts, substr($uri, $queIndex + 1));
        } else {
            array_push($uriParts, $uri);
        }
        return $uriParts;
    }

    /**
     * @param string $uri
     * @return string
     */
    private function buildQueryString(string $uri): string
    {
        $uriParts = $this->splitSubResource($uri);
        $sortMap = $this->queryParameters;
        if (isset($uriParts[1])) {
            $sortMap[$uriParts[1]] = null;
        }
        $queryString = $uriParts[0];
        if (count($uriParts)) {
            $queryString = $queryString . "?";
        }
        ksort($sortMap);
        foreach ($sortMap as $sortMapKey => $sortMapValue) {
            $queryString = $queryString . $sortMapKey;
            if (isset($sortMapValue)) {
                $queryString = $queryString . "=" . $sortMapValue;
            }
            $queryString = $queryString . static::$querySeparator;
        }

        if (null == count($sortMap)) {
            $queryString = substr($queryString, 0, strlen($queryString) - 1);
        }
        return $queryString;
    }

    /**
     * @param string $acceptFormat
     * @return string
     */
    private function formatToAccept(string $acceptFormat): string
    {
        $acceptFormat = strtoupper($acceptFormat);
        if (strtoupper($acceptFormat) === "JSON") {
            return "application/json";
        } elseif ($acceptFormat === "XML") {
            return "application/xml";
        }
        return "application/octet-stream";
    }

    /**
     * @return array
     */
    public function getPathParameters(): array
    {
        return $this->pathParameters;
    }

    /**
     * @param string $name
     * @param $value
     */
    public function putPathParameter(string $name, $value)
    {
        $this->pathParameters[$name] = $value;
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

    /**
     * @return string
     */
    public function getUriPattern(): string
    {
        return $this->uriPattern;
    }

    /**
     * @param string $uriPattern
     * @return string
     */
    public function setUriPattern(string $uriPattern): string
    {
        return $this->uriPattern = $uriPattern;
    }

    /**
     * @param string $version
     */
    public function setVersion(string $version)
    {
        $this->version = $version;
        $this->headers["x-acs-version"] = $version;
    }
}