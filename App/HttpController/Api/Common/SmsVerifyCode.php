<?php
/**
 * Created by PhpStorm.
 * User: Tioncico
 * Date: 2019/5/21 0021
 * Time: 9:43
 */

namespace App\HttpController\Api\Common;


use App\Service\Common\VerifyCodeService;
use App\Utility\AliyunSms;
use EasySwoole\Http\Annotation\Param;
use EasySwoole\Http\Message\Status;
use EasySwoole\Utility\Random;
use EasySwoole\Validate\Validate;

class SmsVerifyCode extends BaseController
{
    /**
     * @api {get|post} /Api/Common/SmsVerifyCode/sendSms
     * @apiName sendSms
     * @apiGroup Common/SmsVerifyCode
     * @apiDescription sendSms 短信验证
     * @Param(name="phone", alias="手机号码", required="", numeric="",length="11")
     * @Param(name="type", alias="验证码类型", optional="", inArray="{1,2,3,4,5,6}")
     * @apiParam {String} phone  手机号码
     * @apiParam {String} [type]  验证码类型默认3 1:更新资料,2更新密码,3注册,4登陆异常,5登陆,6身份验证
     * @apiSuccess {Number} code
     * @apiSuccess {Object[]} data
     * @apiSuccess {String} msg
     * @apiSuccess {Number} count
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {"code":0,"data":[{},{}],"msg":"success",count:2}
     * @author: tioncico < 1067197739@qq.cn >
     */
    function sendSms()
    {
        $ttl = 60;
        // 随机生成验证码
        $phone = $this->input('phone');
        $random = Random::character(6, '1234567890');
        $result = AliyunSms::sendRegisterSms($phone, $random,$this->input('type',AliyunSms::TYPE_REGISTER));
        if ($result['Code'] != 'OK') {
            $this->writeJson(Status::CODE_BAD_REQUEST, [], $result['Message']);
            return false;
        }
        $smsVerifyCodeTime = time();
        $hash = VerifyCodeService::getSmsVerifyCodeHash($random,$smsVerifyCodeTime,$phone);
        $this->response()->setCookie("smsVerifyCodeHash", $hash, $smsVerifyCodeTime + $ttl, '/');
        $this->response()->setCookie('smsVerifyCodeTime', $smsVerifyCodeTime, $smsVerifyCodeTime + $ttl, '/');
        $this->response()->setCookie('smsVerifyPhone', $phone, $smsVerifyCodeTime + $ttl, '/');
        $this->writeJson(Status::CODE_OK, [
            'smsVerifyCodeTime' => $smsVerifyCodeTime,
            'smsVerifyPhone'    => $phone
        ], 'success');
    }
}