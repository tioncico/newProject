<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 19-7-25
 * Time: 下午5:22
 */

namespace App\Service\Common;


class VerifyCodeService extends BaseService
{

    const DURATION = 5 * 60;

    static function checkVerifyCode($code, $time, $hash)
    {
        if ($time + self::DURATION < time()) {
            return false;
        }
        $code = strtolower($code);
        return self::getVerifyCodeHash($code, $time) == $hash;
    }

    static function getVerifyCodeHash($code, $time)
    {
        return md5($code . $time);
    }

    static function checkSmsVerifyCode($code, $time, $phone, $hash)
    {
        if ($time + self::DURATION < time()) {
            return false;
        }
        return self::getSmsVerifyCodeHash($code, $time, $phone) == $hash;
    }

    static function getSmsVerifyCodeHash($code, $time, $phone)
    {
        $halt = 'sms';
        return md5($code . $time . $phone . $halt);
    }

}