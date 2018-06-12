<?php

namespace Aliyun\Core;

use Aliyun\Core\Auth\Credential;
use Aliyun\Core\Auth\ISigner;
use Aliyun\Core\exception\ClientException;
use Aliyun\Core\exception\ServerException;
use Aliyun\Core\Http\HttpHelper;
use Aliyun\Core\Profile\IClientProfile;
use Aliyun\Core\Regions\EndpointProvider;

class DefaultAcsClient implements IAcsClient
{
    /**
     * @var IClientProfile
     */
    public $iClientProfile;
    public $__urlTestFlag__;

    function __construct(IClientProfile $iClientProfile)
    {
        $this->iClientProfile = $iClientProfile;
        $this->__urlTestFlag__ = false;
    }

    /**
     * @param AcsRequest|RpcAcsRequest $request
     * @param ISigner|null $iSigner
     * @param Credential|null $credential
     * @param bool $autoRetry
     * @param int $maxRetryNumber
     * @return mixed|\SimpleXMLElement|string
     * @throws ClientException
     * @throws ServerException
     */
    public function getAcsResponse(
        AcsRequest $request,
        ?ISigner $iSigner = null,
        ?Credential $credential = null,
        bool $autoRetry = true,
        int $maxRetryNumber = 3
    ) {
        $httpResponse = $this->doActionImpl($request, $iSigner, $credential, $autoRetry, $maxRetryNumber);
        $respObject = $this->parseAcsResponse($httpResponse->getBody(), $request->getAcceptFormat());
        if (false == $httpResponse->isSuccess()) {
            $this->buildApiException($respObject, $httpResponse->getStatus());
        }
        return $respObject;
    }

    /**
     * @param AcsRequest|RpcAcsRequest $request
     * @param ISigner|null $iSigner
     * @param Credential|null $credential
     * @param bool $autoRetry
     * @param int $maxRetryNumber
     * @return Http\HttpResponse
     * @throws ClientException
     */
    private function doActionImpl(
        AcsRequest $request,
        ?ISigner $iSigner = null,
        ?Credential $credential = null,
        bool $autoRetry = true,
        int $maxRetryNumber = 3
    ) {
        if (null === $this->iClientProfile && (null === $iSigner || null === $credential
                || empty($request->getRegionId()) || empty($request->getAcceptFormat()))) {
            throw new ClientException("No active profile found.", "SDK.InvalidProfile");
        }

        if (null === $iSigner) {
            $iSigner = $this->iClientProfile->getSigner();
        }
        if (null === $credential) {
            $credential = $this->iClientProfile->getCredential();
        }
        $request = $this->prepareRequest($request);
        $domain = EndpointProvider::findProductDomain($request->getRegionId(), $request->getProduct());

        if (null === $domain) {
            throw new ClientException("Can not find endpoint to access.", "SDK.InvalidRegionId");
        }
        $requestUrl = $request->composeUrl($iSigner, $credential, $domain);

        if ($this->__urlTestFlag__) {
            throw new ClientException($requestUrl, "URLTestFlagIsSet");
        }

        if (count($request->getDomainParameter()) > 0) {
            $httpResponse = HttpHelper::curl($requestUrl, $request->getMethod(), $request->getDomainParameter(),
                $request->getHeaders());
        } else {
            $httpResponse = HttpHelper::curl($requestUrl, $request->getMethod(), $request->getContent(),
                $request->getHeaders());
        }

        $retryTimes = 1;
        while (500 <= $httpResponse->getStatus() && $autoRetry && $retryTimes < $maxRetryNumber) {
            $requestUrl = $request->composeUrl($iSigner, $credential, $domain);

            if (count($request->getDomainParameter()) > 0) {
                $httpResponse = HttpHelper::curl($requestUrl, $request->getMethod(), $request->getDomainParameter(),
                    $request->getHeaders());
            } else {
                $httpResponse = HttpHelper::curl($requestUrl, $request->getMethod(), $request->getContent(),
                    $request->getHeaders());
            }
            $retryTimes++;
        }
        return $httpResponse;
    }

    /**
     * @param AcsRequest|RpcAcsRequest $request
     * @param ISigner|null $iSigner
     * @param Credential|null $credential
     * @param bool $autoRetry
     * @param int $maxRetryNumber
     * @return Http\HttpResponse
     * @throws ClientException
     */
    public function doAction(
        AcsRequest $request,
        ?ISigner $iSigner = null,
        ?Credential $credential = null,
        bool $autoRetry = true,
        int $maxRetryNumber = 3
    ) {
        trigger_error("doAction() is deprecated. Please use getAcsResponse() instead.", E_USER_NOTICE);
        return $this->doActionImpl($request, $iSigner, $credential, $autoRetry, $maxRetryNumber);
    }

    /**
     * @param AcsRequest $request
     * @return AcsRequest|RpcAcsRequest
     */
    private function prepareRequest(AcsRequest $request): AcsRequest
    {
        if (null == $request->getRegionId()) {
            $request->setRegionId($this->iClientProfile->getRegionId());
        }
        if (null == $request->getAcceptFormat()) {
            $request->setAcceptFormat($this->iClientProfile->getFormat());
        }
        if (null == $request->getMethod()) {
            $request->setMethod("GET");
        }
        return $request;
    }


    /**
     * @param $respObject
     * @param $httpStatus
     * @throws ServerException
     */
    private function buildApiException($respObject, $httpStatus)
    {
        throw new ServerException($respObject->Message, $respObject->Code, $httpStatus, $respObject->RequestId);
    }

    private function parseAcsResponse(string $body, string $format)
    {
        $format = strtoupper($format);
        if ('JSON' === $format) {
            $respObject = json_decode($body);
        } elseif ('XML' === $format) {
            $respObject = @simplexml_load_string($body);
        } elseif ('RAW' === $format) {
            $respObject = $body;
        } else {
            throw new \InvalidArgumentException('format invalid, JSON|XML|RAW');
        }

        return $respObject;
    }
}
