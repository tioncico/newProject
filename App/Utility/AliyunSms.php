<?php
/**
 * Created by PhpStorm.
 * User: Tioncico
 * Date: 2019/5/20 0020
 * Time: 15:25
 */

namespace App\Utility;


use AlibabaCloud\Client\AlibabaCloud;
use EasySwoole\EasySwoole\Config;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

class AliyunSms
{
    const ENDPOINT_SEND_SMS = 'dysmsapi';
    const METHOD_SEND_SMS = 'SendSms';
    const SIGN_NAME = 'easyShop';

    const TEMPLATE = [
        1 => 'SMS_165560709',
        2 => 'SMS_165560710',
        3 => 'SMS_165560711',
        4 => 'SMS_165560712',
        5 => 'SMS_165560713',
        6 => 'SMS_165560714',
    ];

    const TYPE_UPDATE_INFO = 1;
    const TYPE_UPDATE_PASSWORD = 2;
    const TYPE_REGISTER = 3;
    const TYPE_LOGIN_EXCEPTION = 4;
    const TYPE_LOGIN = 5;
    const TYPE_USER_VERIFY = 6;

    static function sendRegisterSms($phone, $code, $templateCode = self::TYPE_REGISTER)
    {
        AlibabaCloud::accessKeyClient(Config::getInstance()->getConf('ALI_SMS.accessKeyId'), Config::getInstance()->getConf('ALI_SMS.accessKeySecret'))
            ->regionId('cn-hangzhou')// replace regionId as you need
            ->asDefaultClient();
        try {
            $result = AlibabaCloud::rpc()
                ->product(self::ENDPOINT_SEND_SMS)
                ->version('2017-05-25')
                ->action(self::METHOD_SEND_SMS)
                ->method('POST')
                ->options([
                    'query' => [
                        'PhoneNumbers'  => $phone,
                        'SignName'      => "厦门扶树信息技术有限公司",
                        'TemplateCode'  => self::TEMPLATE[$templateCode],
                        'TemplateParam' => json_encode(['code' => $code]),
                    ],
                ])
                ->request();
            return $result->toArray();
        } catch (ClientException $e) {
            throw new ServerException($e->getMessage());
        } catch (ServerException $e) {
            throw new ServerException($e->getMessage());
        }
    }


}