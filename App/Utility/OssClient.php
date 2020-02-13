<?php
/**
 * Created by PhpStorm.
 * User: evalor
 * Date: 2018/7/7
 * Time: 下午8:03
 */

namespace App\Utility;

/**
 * Class OssClient
 * @author  : evalor <master@evalor.cn>
 * @package App\Utility
 */
class OssClient
{
    private $ossKey;
    private $ossSecret;
    private $ossEndPoint;
    private $ossBucket;

    function __construct()
    {
        $conf = \EasySwoole\EasySwoole\Config::getInstance()->getConf('ALI_OSS');
        $this->ossKey = $conf['KEY'];
        $this->ossBucket = $conf['BUCKET'];
        $this->ossSecret = $conf['SECRET'];
        $this->ossEndPoint = $conf['END_POINT'];
    }

    /**
     * 获取 OSS Client
     * @author : evalor <master@evalor.cn>
     * @return \OSS\OssClient
     * @throws \OSS\Core\OssException
     */
    function aliOssClient(): \OSS\OssClient
    {
        return new \OSS\OssClient($this->ossKey, $this->ossSecret, $this->ossEndPoint);
    }

    /**
     * 获取储存桶名称
     * @return mixed
     */
    public function getOssBucket()
    {
        return $this->ossBucket;
    }

    /**
     * @return mixed
     */
    public function getOssKey()
    {
        return $this->ossKey;
    }

    /**
     * @return mixed
     */
    public function getOssSecret()
    {
        return $this->ossSecret;
    }

    /**
     * @return mixed
     */
    public function getOssEndPoint()
    {
        return $this->ossEndPoint;
    }


}