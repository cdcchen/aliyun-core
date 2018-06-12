<?php

namespace Aliyun\Core\Auth;

/**
 * Class Credential
 * @package Aliyun\Core\Auth
 */
class Credential
{
    /**
     * @var string
     */
    private $dateTimeFormat = 'Y-m-d\TH:i:s\Z';
    /**
     * @var false|string
     */
    private $refreshDate;
    /**
     * @var string
     */
    private $expiredDate;
    /**
     * @var string
     */
    private $accessKeyId;
    /**
     * @var string
     */
    private $accessSecret;
    /**
     * @var string
     */
    private $securityToken;

    /**
     * Credential constructor.
     * @param string $accessKeyId
     * @param string $accessSecret
     */
    function __construct(string $accessKeyId, string $accessSecret)
    {
        $this->accessKeyId = $accessKeyId;
        $this->accessSecret = $accessSecret;
        $this->refreshDate = date($this->dateTimeFormat);
    }

    /**
     * @return bool
     */
    public function isExpired(): bool
    {
        if ($this->expiredDate == null) {
            return false;
        }
        if (strtotime($this->expiredDate) > date($this->dateTimeFormat)) {
            return false;
        }
        return true;
    }

    /**
     * @return string
     */
    public function getRefreshDate(): string
    {
        return $this->refreshDate;
    }

    /**
     * @return string
     */
    public function getExpiredDate(): string
    {
        return $this->expiredDate;
    }

    /**
     * @param $expiredHours
     * @return false|string
     */
    public function setExpiredDate($expiredHours)
    {
        if ($expiredHours > 0) {
            return $this->expiredDate = date($this->dateTimeFormat, strtotime("+" . $expiredHours . " hour"));
        }
    }

    /**
     * @return string
     */
    public function getAccessKeyId(): string
    {
        return $this->accessKeyId;
    }

    /**
     * @param string $accessKeyId
     */
    public function setAccessKeyId(string $accessKeyId)
    {
        $this->accessKeyId = $accessKeyId;
    }

    /**
     * @return string
     */
    public function getAccessSecret(): string
    {
        return $this->accessSecret;
    }

    /**
     * @param string $accessSecret
     */
    public function setAccessSecret(string $accessSecret)
    {
        $this->accessSecret = $accessSecret;
    }

}