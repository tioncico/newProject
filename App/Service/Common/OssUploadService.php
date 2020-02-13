<?php
/**
 * Created by PhpStorm.
 * User: Tioncico
 * Date: 2019/4/18 0018
 * Time: 11:27
 */

namespace App\Service\Common;


use App\Model\Aliyun\AliyunOssResourceBean;
use App\Model\Aliyun\AliyunOssResourceModel;
use App\Service\ServiceException;
use EasySwoole\MysqliPool\Mysql;
use EasySwoole\EasySwoole\Config;
use EasySwoole\Utility\File;

class OssUploadService extends BaseService
{

    static function saveToOss($filePath, $fileName = null)
    {
        if (!file_exists($filePath)) {
            throw new ServiceException('文件数据不存在');
        }
        try {
            $oss = new \App\Utility\OssClient();
            $ossClient = $oss->aliOssClient();
            $ossBucket = $oss->getOssBucket();
            $fileName == null && ($fileName = basename($filePath));
            $ossClient->uploadFile($ossBucket, $fileName, $filePath);
            $ossClient->putObjectAcl($ossBucket, $fileName, $ossClient::OSS_ACL_TYPE_PUBLIC_READ);
            return $oss->getOssEndPoint() . '/' . $fileName;
        } catch (\Throwable $e) {
            throw new ServiceException($e->getMessage());
        }
    }

    static function moveOss($fromObject, $toObject, $fromBucket = 'slct', $toBucket = 'slct', $options = NULL)
    {
        try {
            $oss = new \App\Utility\OssClient();
            $ossClient = $oss->aliOssClient();
            $result = $ossClient->copyObject($fromBucket, $fromObject, $toBucket, $toObject, $options);
            $result = $ossClient->deleteObject($fromBucket,$fromObject);
        } catch (\Throwable $e) {
            throw new ServiceException($e->getMessage());
        }
    }

    static function addOssResource($path, $note, $bucket = 'slct'): ?AliyunOssResourceModel
    {
        $model = new AliyunOssResourceModel();
        $data  =[
          'addTime'=>time(),
          'note'=>$note,
          'isUse'=>0,
          'ossPath'=>$path,
          'bucket'=>$bucket,
        ];
        $result = $model->data($data)->save();
        if ($result === false) {
            throw new ServiceException('新增记录失败');
        }
        return $model;
    }

    static function gmtIso8601($time)
    {
        $dtStr = date("c", $time);
        $mydatetime = new \DateTime($dtStr);
        $expiration = $mydatetime->format(\DateTime::ISO8601);
        $pos = strpos($expiration, '+');
        $expiration = substr($expiration, 0, $pos);
        return $expiration . "Z";
    }

    static function getSign($dirPreFix = null)
    {
        $oss = new \App\Utility\OssClient();
        $id = $oss->getOssKey();          // 请填写您的AccessKeyId。
        $key = $oss->getOssSecret();     // 请填写您的AccessKeySecret。
        $callbackUrl = (Config::getInstance()->getConf('WEB_SSL') ? 'https://' : 'http://') . Config::getInstance()->getConf('WEB_HOST') . '/Api/Common/OssFile/fileCallback';
        // $host的格式为 bucketname.endpoint，请替换为您的真实信息。
        $host = Config::getInstance()->getConf('ALI_OSS.HOST');
        // $callbackUrl为上传回调服务器的URL，请将下面的IP和Port配置为您自己的真实URL信息。
        $dir = $dirPreFix;          // 用户上传文件时指定的前缀。

        $callback_param = array('callbackUrl'      => $callbackUrl,
                                'callbackBody'     => 'filename=${object}&size=${size}&mimeType=${mimeType}&height=${imageInfo.height}&width=${imageInfo.width}',
                                'callbackBodyType' => "application/x-www-form-urlencoded");
        $callback_string = json_encode($callback_param);
        $base64_callback_body = base64_encode($callback_string);


        $now = time();
        $expire = 30;  //设置该policy超时时间是10s. 即这个policy过了这个有效时间，将不能访问。
        $end = $now + $expire;
        $expiration = self::gmtIso8601($end);


        //最大文件大小.用户可以自己设置
        $condition = array(0 => 'content-length-range', 1 => 0, 2 => (Config::getInstance()->getConf('MAIN_SERVER.SETTING.package_max_length'))??2048*1024);
        $conditions[] = $condition;

        // 表示用户上传的数据，必须是以$dir开始，不然上传会失败，这一步不是必须项，只是为了安全起见，防止用户通过policy上传到别人的目录。
//        $start = array(0 => 'starts-with', 1 => $key, 2 => $dir);
//        $conditions[] = $start;

        $arr = array('expiration' => $expiration, 'conditions' => $conditions);
        $policy = json_encode($arr);
        $base64_policy = base64_encode($policy);
        $string_to_sign = $base64_policy;
        $signature = base64_encode(hash_hmac('sha1', $string_to_sign, $key, true));

        $result = array();
        $result['accessid'] = $id;
        $result['host'] = $host;
        $result['policy'] = $base64_policy;
        $result['signature'] = $signature;
        $result['expire'] = $end;
        $result['callback'] = $base64_callback_body;
        $result['dir'] = $dir;  // 这个参数是设置用户上传文件时指定的前缀。
        return $result;
    }

    static function ossCallback(\EasySwoole\Http\Request $request)
    {
// 2.获取OSS的签名
        $authorization = base64_decode($request->getHeader('authorization')[0]);
// 3.获取公钥
        $pubKeyUrl = base64_decode($request->getHeader('x-oss-pub-key-url')[0]);
        //先尝试本地是否有文件
        if (file_exists(EASYSWOOLE_ROOT . '/Temp/Oss/' . md5($pubKeyUrl))) {
            $pubKey = file_get_contents(EASYSWOOLE_ROOT . '/Temp/Oss/' . md5($pubKeyUrl));
        } else {
            $pubKey = file_get_contents($pubKeyUrl);
            File::createFile(EASYSWOOLE_ROOT . '/Temp/Oss/' . md5($pubKeyUrl), $pubKey);
        };
// 4.获取回调body
        $requestData = explode("\n", $request->getSwooleRequest()->getData());
        $body = $requestData[count($requestData) - 1];
// 5.拼接待签名字符串
        $authStr = '';
        $path = $request->getServerParams()['request_uri'];
        $pos = strpos($path, '?');
        if ($pos === false) {
            $authStr = urldecode($path) . "\n" . $body;
        } else {
            $authStr = urldecode(substr($path, 0, $pos)) . substr($path, $pos, strlen($path) - $pos) . "\n" . $body;
        }
// 6.验证签名
        return openssl_verify($authStr, $authorization, $pubKey, OPENSSL_ALGO_MD5);
    }
}