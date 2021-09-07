<?php

namespace EasySwoole\Oss\AliYun\Result;

use EasySwoole\Oss\AliYun\Core\OssException;
use OSS\OssClient;

/**
 *
 * @package EasySwoole\Oss\AliYun\Result
 */
class SymlinkResult extends Result
{
    /**
     * @return string
     * @throws OssException
     */
    protected function parseDataFromResponse()
    {
        $this->rawResponse->getHeaders()[OssClient::OSS_SYMLINK_TARGET] = rawurldecode($this->rawResponse->getHeaders()[OssClient::OSS_SYMLINK_TARGET]);
        return $this->rawResponse->getHeaders();
    }
}

