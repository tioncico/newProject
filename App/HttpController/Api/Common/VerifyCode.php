<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2019-02-25
 * Time: 13:46
 */

namespace App\HttpController\Api\Common;


use App\Service\Common\VerifyCodeService;
use EasySwoole\Http\Message\Status;
use EasySwoole\Utility\Random;
use EasySwoole\VerifyCode\Conf;


class VerifyCode extends BaseController
{
    static $VERIFY_CODE_TTL = 120;
    static $VERIFY_CODE_LENGTH = 4;

    function verifyCode() {
        $conf = new Conf();
        $codeObj = new \EasySwoole\VerifyCode\VerifyCode($conf);
        // 随机生成验证码
        $random = Random::character(self::$VERIFY_CODE_LENGTH, '1234567890abcdefghijklmnopqrstuvwxyz');
        $code = $codeObj->DrawCode($random);
        $time = time();
        $result = [
            'verifyCode'     => $code->getImageBase64(),
            'verifyCodeTime' => $time,
        ];

        $this->response()->setCookie("verifyCodeHash", VerifyCodeService::getVerifyCodeHash($random, $time), $time + self::$VERIFY_CODE_TTL, '/');
        $this->response()->setCookie('verifyCodeTime', $time, $time + self::$VERIFY_CODE_TTL, '/');
        $this->writeJson(Status::CODE_OK, $result, 'success');
    }
}