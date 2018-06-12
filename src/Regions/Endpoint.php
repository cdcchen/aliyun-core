<?php

namespace Aliyun\Core\Regions;

/**
 * Class Endpoint
 * @package Aliyun\Core\Regions
 */
class Endpoint
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var array
     */
    private $regionIds      = [];
    /**
     * @var array
     */
    private $productDomains = [];

    /**
     * Endpoint constructor.
     * @param string $name
     * @param array $regionIds
     * @param array $productDomains
     */
    function  __construct(string $name, array $regionIds, array $productDomains)
	{
		$this->name = $name;
		$this->regionIds = $regionIds;
		$this->productDomains = $productDomains;
	}

    /**
     * @return string
     */
    public function getName():string
	{
		return $this->name;
	}

    /**
     * @param string $name
     */
    public function setName(string $name)
	{
		$this->name = $name;
	}

    /**
     * @return array
     */
    public function getRegionIds():array
	{
		return $this->regionIds;
	}

    /**
     * @param array $regionIds
     */
    public function setRegionIds(array $regionIds)
	{
		$this->regionIds = $regionIds;
	}

    /**
     * @return array
     */
    public function getProductDomains():array
	{
		return $this->productDomains;
	}

    /**
     * @param ProductDomain[] $productDomains
     */
	public function setProductDomains(array $productDomains)
	{
		$this->productDomains = $productDomains;
	}
}