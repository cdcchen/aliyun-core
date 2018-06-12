<?php

namespace Aliyun\Core\Auth;

interface ISigner
{
    public function getSignatureMethod(): string;

    public function getSignatureVersion(): string;

    public function signString(string $source, string $accessSecret): string;
}