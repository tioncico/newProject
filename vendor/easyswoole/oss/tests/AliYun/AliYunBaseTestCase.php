<?php
/**
 * Created by PhpStorm.
 * User: Tioncico
 * Date: 2019/11/18 0018
 * Time: 15:03
 */

namespace EasySwoole\Oss\Tests\AliYun;


use EasySwoole\Oss\AliYun\OssClient;
use PHPUnit\Framework\TestCase;

class AliYunBaseTestCase extends TestCase
{
    /**
     * @var $client OssClient
     */
    protected $client;
    /**
     * @var $ossClient \OSS\OssClient
     */
    protected $ossClient;

    function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $config = new \EasySwoole\Oss\AliYun\Config([
            'accessKeyId'     => ACCESS_KEY_ID,
            'accessKeySecret' => ACCESS_KEY_SECRET,
            'endpoint'        => END_POINT,
        ]);
        $ossClient = new OssClient($config);
        $this->client = $ossClient;

        $ossClient = new \OSS\OssClient(ACCESS_KEY_ID, ACCESS_KEY_SECRET, END_POINT);
        $this->ossClient = $ossClient;
    }

}