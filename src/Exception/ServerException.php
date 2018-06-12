<?php


namespace Aliyun\Core\Exception;

/**
 * Class ServerException
 * @package Aliyun\Core\Exception
 */
class ServerException extends ClientException
{
    /**
     * @var int
     */
    private $httpStatus;
    /**
     * @var string
     */
    private $requestId;

    /**
     * ServerException constructor.
     * @param $errorMessage
     * @param $errorCode
     * @param $httpStatus
     * @param $requestId
     */
    function __construct($errorMessage, $errorCode, $httpStatus, $requestId)
    {
        $messageStr = $errorCode . " " . $errorMessage . " HTTP Status: " . $httpStatus . " RequestID: " . $requestId;
        parent::__construct($messageStr, $errorCode);
        $this->setErrorMessage($errorMessage);
        $this->setErrorType("Server");
        $this->httpStatus = $httpStatus;
        $this->requestId = $requestId;
    }

    /**
     * @return mixed
     */
    public function getHttpStatus()
    {
        return $this->httpStatus;
    }

    /**
     * @return string
     */
    public function getRequestId(): string
    {
        return $this->requestId;
    }

}
