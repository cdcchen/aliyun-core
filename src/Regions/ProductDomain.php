<?php

namespace Aliyun\Core\Regions;

/**
 * Class ProductDomain
 * @package Aliyun\Core\Regions
 */
class ProductDomain
{
    /**
     * @var string
     */
    private $productName;
    /**
     * @var string
     */
    private $domainName;

    /**
     * ProductDomain constructor.
     * @param string $product
     * @param string $domain
     */
    function __construct(string $product, string $domain)
    {
        $this->productName = $product;
        $this->domainName = $domain;
    }

    /**
     * @return string
     */
    public function getProductName(): string
    {
        return $this->productName;
    }

    /**
     * @param string $productName
     */
    public function setProductName(string $productName)
    {
        $this->productName = $productName;
    }

    /**
     * @return string
     */
    public function getDomainName(): string
    {
        return $this->domainName;
    }

    /**
     * @param string $domainName
     */
    public function setDomainName(string $domainName)
    {
        $this->domainName = $domainName;
    }

}