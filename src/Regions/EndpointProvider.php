<?php

namespace Aliyun\Core\Regions;

/**
 * Class EndpointProvider
 * @package Aliyun\Core\Regions
 */
class EndpointProvider
{
    /**
     * @var array
     */
    private static $endpoints = [];

    /**
     * @param string $regionId
     * @param string $product
     * @return null|string
     */
    public static function findProductDomain(string $regionId, string $product): ?string
    {
        if (null == $regionId || null == $product || null == self::$endpoints) {
            return null;
        }

        foreach (self::$endpoints as $key => $endpoint) {
            if (in_array($regionId, $endpoint->getRegionIds())) {
                return self::findProductDomainByProduct($endpoint->getProductDomains(), $product);
            }
        }
        return null;
    }

    /**
     * @param ProductDomain[] $productDomains
     * @param string $product
     * @return null|string
     */
    private static function findProductDomainByProduct(array $productDomains, string $product): ?string
    {
        if (empty($productDomains)) {
            return null;
        }
        foreach ($productDomains as $key => $productDomain) {
            if ($product == $productDomain->getProductName()) {
                return $productDomain->getDomainName();
            }
        }
        return null;
    }

    /**
     * @return Endpoint[]
     */
    public static function getEndpoints(): array
    {
        return self::$endpoints;
    }

    /**
     * @param Endpoint[] $endpoints
     */
    public static function setEndpoints(array $endpoints)
    {
        self::$endpoints = $endpoints;
    }

}