<?php

namespace Aliyun\Core\Profile;

use Aliyun\Core\Auth\Credential;
use Aliyun\Core\Auth\ISigner;

interface IClientProfile
{
    public function getSigner(): ISigner;

    public function getRegionId(): string;

    public function getFormat(): string;

    public function getCredential(): Credential;
}