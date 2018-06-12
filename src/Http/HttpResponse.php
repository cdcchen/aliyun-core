<?php

namespace Aliyun\Core\Http;

/**
 * Class HttpResponse
 * @package Aliyun\Core\Http
 */
class HttpResponse
{
    /**
     * @var string
     */
    private $body;
    /**
     * @var int
     */
    private $status;

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody(string $body)
    {
        $this->body = $body;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status)
    {
        $this->status = $status;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return 200 <= $this->status && 300 > $this->status;
    }
}