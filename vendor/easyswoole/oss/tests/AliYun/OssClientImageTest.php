<?php

namespace EasySwoole\Oss\Tests\AliYun;

require_once __DIR__ . '/Common.php';

use EasySwoole\Oss\AliYun\OssConst;
use OSS\OssClient;

class OssClinetImageTest extends AliYunBaseTestCase
{
    private $bucketName;
    private $local_file;
    private $object;
    private $download_file;

    public function setUp()
    {
        parent::setUp();
        $this->client = Common::getOssClient();
        $this->bucketName = 'php-sdk-test-bucket-image-' . strval(rand(0, 10000));
//        $this->bucketName = 'tioncicoxyz';
        $this->client->createBucket($this->bucketName);
        Common::waitMetaSync();

        $path = dirname(__FILE__,2);
        $this->local_file = $path . "/Img/test.jpg";
        $this->object =  "oss-test.jpg";
        $this->download_file = $path . "/Img/test-down.jpg";

        $this->client->uploadFile($this->bucketName, $this->object, $this->local_file);
//        $this->ossClient->uploadFile($this->bucketName, $this->object, $this->local_file);

    }

    public function tearDown()
    {
        $this->client->deleteObject($this->bucketName, $this->object);
        $this->client->deleteBucket($this->bucketName);
    }

    public function testImageResize()
    {
        $options = array(
            OssClient::OSS_FILE_DOWNLOAD => $this->download_file,
            OssClient::OSS_PROCESS       => "image/resize,m_fixed,h_100,w_100",);
        $this->check($options, 100, 100, 3267, 'jpg');
    }

    public function testImageCrop()
    {
        $options = array(
            OssClient::OSS_FILE_DOWNLOAD => $this->download_file,
            OssClient::OSS_PROCESS       => "image/crop,w_100,h_100,x_100,y_100,r_1",);
        $this->check($options, 100, 100, 100, 'jpg');
    }

    public function testImageRotate()
    {
        $options = array(
            OssClient::OSS_FILE_DOWNLOAD => $this->download_file,
            OssClient::OSS_PROCESS       => "image/rotate,90",);
        $this->check($options, 572, 579, 20998, 'jpg');
    }

    public function testImageSharpen()
    {
        $options = array(
            OssClient::OSS_FILE_DOWNLOAD => $this->download_file,
            OssClient::OSS_PROCESS       => "image/sharpen,100",);
        $this->check($options, 579, 572, 23015, 'jpg');
    }

    public function testImageWatermark()
    {
        $options = array(
            OssClient::OSS_FILE_DOWNLOAD => $this->download_file,
            OssClient::OSS_PROCESS       => "image/watermark,text_SGVsbG8g5Zu-54mH5pyN5YqhIQ",);
        $this->check($options, 579, 572, 26369, 'jpg');
    }

    public function testImageFormat()
    {
        $options = array(
            OssClient::OSS_FILE_DOWNLOAD => $this->download_file,
            OssClient::OSS_PROCESS       => "image/format,png",);
        $this->check($options, 579, 572, 160733, 'png');
    }

    public function testImageTofile()
    {
        $options = array(
            OssClient::OSS_FILE_DOWNLOAD => $this->download_file,
            OssClient::OSS_PROCESS       => "image/resize,m_fixed,w_100,h_100",);
        $this->check($options, 100, 100, 3267, 'jpg');
    }

    private function check($options, $width, $height, $size, $type)
    {
        $this->client->getObject($this->bucketName, $this->object, $options);
//        $this->ossClient->getObject($this->bucketName, $this->object, $options);
        $array = getimagesize($this->download_file);
        $this->assertEquals($width, $array[0]);
        $this->assertEquals($height, $array[1]);
        $this->assertEquals($type === 'jpg' ? 2 : 3, $array[2]);//2 <=> jpg
    }
}
