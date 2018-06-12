<?php

namespace Aliyun\Core\Profile;

use Aliyun\Core\Auth\Credential;
use Aliyun\Core\Auth\ISigner;
use Aliyun\Core\Auth\ShaHmac1Signer;
use Aliyun\Core\Regions\Endpoint;
use Aliyun\Core\Regions\EndpointProvider;
use Aliyun\Core\Regions\ProductDomain;

/**
 * Class DefaultProfile
 * @package Aliyun\Core\Profile
 */
class DefaultProfile implements IClientProfile
{
    /**
     * @var string
     */
    private static $profile;
    /**
     * @var array
     */
    private static $endpoints = [];
    /**
     * @var Credential
     */
    private static $credential;
    /**
     * @var string
     */
    private static $regionId;
    /**
     * @var string
     */
    private static $acceptFormat;

    /**
     * @var ISigner
     */
    private static $iSigner;
    /**
     * @var Credential
     */
    private static $iCredential;

    /**
     * DefaultProfile constructor.
     * @param string $regionId
     * @param Credential $credential
     */
    private function __construct(string $regionId, Credential $credential)
    {
        self::$regionId = $regionId;
        self::$credential = $credential;
    }

    /**
     * @param string $regionId
     * @param string $accessKeyId
     * @param string $accessSecret
     * @return DefaultProfile
     */
    public static function getProfile(string $regionId, string $accessKeyId, string $accessSecret): DefaultProfile
    {
        $credential = new Credential($accessKeyId, $accessSecret);
        self::$profile = new DefaultProfile($regionId, $credential);
        return self::$profile;
    }

    /**
     * @return ISigner
     */
    public function getSigner(): ISigner
    {
        if (null == self::$iSigner) {
            self::$iSigner = new ShaHmac1Signer();
        }
        return self::$iSigner;
    }

    /**
     * @return string
     */
    public function getRegionId(): string
    {
        return self::$regionId;
    }

    /**
     * @return string
     */
    public function getFormat(): string
    {
        return self::$acceptFormat;
    }

    /**
     * @return Credential
     */
    public function getCredential(): Credential
    {
        if (null == self::$credential && null != self::$iCredential) {
            self::$credential = self::$iCredential;
        }
        return self::$credential;
    }

    /**
     * @return array
     */
    public static function getEndpoints(): array
    {
        if (null == self::$endpoints) {
            self::$endpoints = EndpointProvider::getEndpoints();
        }
        return self::$endpoints;
    }

    /**
     * @param string $endpointName
     * @param string $regionId
     * @param string $product
     * @param string $domain
     */
    public static function addEndpoint(string $endpointName, string $regionId, string $product, string $domain): void
    {
        if (null == self::$endpoints) {
            self::$endpoints = self::getEndpoints();
        }
        $endpoint = self::findEndpointByName($endpointName);
        if (null == $endpoint) {
            self::addEndpoint_($endpointName, $regionId, $product, $domain);
        } else {
            self::updateEndpoint($regionId, $product, $domain, $endpoint);
        }
    }

    /**
     * @param string $endpointName
     * @return Endpoint|null
     */
    public static function findEndpointByName(string $endpointName): ?Endpoint
    {
        foreach (self::$endpoints as $key => $endpoint) {
            if ($endpoint->getName() == $endpointName) {
                return $endpoint;
            }
        }
        return null;
    }

    /**
     * @param string $endpointName
     * @param string $regionId
     * @param string $product
     * @param string $domain
     */
    private static function addEndpoint_(string $endpointName, string $regionId, string $product, string $domain): void
    {
        $regionIds = [$regionId];
        $productDomains = [new ProductDomain($product, $domain)];
        $endpoint = new Endpoint($endpointName, $regionIds, $productDomains);
        array_push(self::$endpoints, $endpoint);
    }

    /**
     * @param string $regionId
     * @param string $product
     * @param string $domain
     * @param Endpoint $endpoint
     */
    private static function updateEndpoint(string $regionId, string $product, string $domain, Endpoint $endpoint): void
    {
        $regionIds = $endpoint->getRegionIds();
        if (!in_array($regionId, $regionIds)) {
            array_push($regionIds, $regionId);
            $endpoint->setRegionIds($regionIds);
        }

        $productDomains = $endpoint->getProductDomains();
        if (null == self::findProductDomain($productDomains, $product, $domain)) {
            array_push($productDomains, new ProductDomain($product, $domain));
        }
        $endpoint->setProductDomains($productDomains);
    }

    /**
     * @param ProductDomain[] $productDomains
     * @param string $product
     * @param string $domain
     * @return ProductDomain|null
     */
    private static function findProductDomain(array $productDomains, string $product, string $domain): ?ProductDomain
    {
        foreach ($productDomains as $key => $productDomain) {
            if ($productDomain->getProductName() == $product && $productDomain->getDomainName() == $domain) {
                return $productDomain;
            }
        }
        return null;
    }

}