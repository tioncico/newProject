<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 19-7-22
 * Time: 下午5:02
 */

namespace App\Service\Admin;


use App\Service\Common\OssUploadService;
use App\Service\ServiceException;
use App\Utility\OssClient;
use EasySwoole\EasySwoole\Config;
use EasySwoole\Spl\SplBean;
use EasySwoole\Spl\SplString;
use EasySwoole\Utility\Random;

class OssFileService extends AdminBaseService
{
    protected $filePath;            // 临时文件
    protected $storage = 'Temp/';    // 临时文件前缀
    protected $host;

    function __construct($filePath, $storage = 'Temp/')
    {
        $host = Config::getInstance()->getConf('ALI_OSS.HOST');
        $this->host = rtrim($host, '/') . '/';
        $this->filePath = $filePath;
        $this->storage = $storage;
    }

    static function moveFile($filePath, $storage)
    {
        $service = new self($filePath);
        $ret = $service->isStartPrefix();
        if ($ret) {
            return $goodsImg = $service->move($storage);
        } else {
            return $filePath;
        }
    }

    function copy($path, $name, $fromBucket = 'slct', $toBucket = 'slct')
    {
        $obj = new SplString($path);
        $ret = $obj->startsWith($this->host);
        if ($ret) {
            // 左右去除 /
            $name = trim($name, '/');

            // 替换之前的路径(除去域名)
            $oldPath = substr_replace($path, '', 0, strlen($this->host));

            $pos = strripos($name, '.');

            $ext = $pos === false ? '.png' : substr($name, $pos);

            // 替换之后的路径(除去域名)
            $newPath = $name . '/' . time() . '' . rand(100, 999) . $ext;

            $oss = new OssClient();
            $ossClient = $oss->aliOssClient();
            $ossClient->copyObject($fromBucket, $oldPath, $toBucket, $newPath);
            return $this->host . $newPath;
        } else {
            throw new ServiceException('图片域名不对');
        }
    }

    /**
     * @param $name
     * @return string
     * @throws \App\Service\ServiceException
     */
    function move($name)
    {
        $ret = $this->isStartPrefix();
        if ($ret) {
            // 左右去除 /
            $name = trim($name, '/');

            // 替换之前的路径(除去域名)
            $oldPath = substr_replace($this->filePath, '', 0, strlen($this->host));

            // 替换之后的路径(除去域名)
            $newPath = substr_replace($oldPath, $name . '/', 0, strlen($this->storage));

            OssUploadService::moveOss($oldPath, $newPath);
            return $this->host . $newPath;
        }
    }

    /**
     * 删除图片文件
     * @param        $path
     * @param string $bucket
     * @throws \OSS\Core\OssException
     */
    static function delete($path, $bucket = 'slct')
    {
        $host = Config::getInstance()->getConf('ALI_OSS.HOST');
        $obj = new SplString($path);
        $ret = $obj->startsWith($host);
        if ($ret) {
            if (substr($host, -1)!='/'){
                $host.='/';
            }
            $path = substr_replace($path, '', 0, strlen($host));
            $oss = new OssClient();
            $ossClient = $oss->aliOssClient();
            $ossClient->deleteObject($bucket, $path);
        }
    }


    /**
     * 判断是否是以 $host.$storage 开头
     */
    function isStartPrefix(): bool
    {
        $obj = new SplString($this->filePath);
        $ret = $obj->startsWith($this->host);
        if ($ret) {
            $obj->replaceFirst($this->host, '');
            return $obj->startsWith($this->storage);
        } else {
            return false;
        }
    }
}