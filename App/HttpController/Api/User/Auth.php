<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 19-7-26
 * Time: 上午11:08
 */

namespace App\HttpController\Api\User;


use App\Model\User\UserBean;
use App\Model\User\UserModel;
use App\Service\Common\VerifyCodeService;
use EasySwoole\MysqliPool\Mysql;
use EasySwoole\Http\Annotation\Param;
use EasySwoole\Http\Message\Status;
use EasySwoole\Spl\SplBean;

class Auth extends BaseController
{

    /**
     * @api {get|post} /Api/User/Auth/login
     * @apiName 会员登陆
     * @apiGroup User/Auth
     * @apiPermission user
     * @apiDescription 会员登录
     * @Param(name="verifyCodeHash", from={COOKIE}, required="")
     * @Param(name="verifyCodeTime", from={COOKIE}, required="")
     * @Param(name="phone", alias="手机号码", required="", lengthMax="20")
     * @Param(name="password", alias="密码", required="", lengthMin="6", lengthMax="16")
     * @Param(name="verifyCode", alias="验证码", required="", length="4")
     * @apiParam {String} phone post|get
     * @apiParam {String} password post|get
     * @apiParam {String} verifyCode post|get
     * @apiParam {String} verifyCodeHash  cookie
     * @apiParam {String} verifyCodeTime  cookie
     * @apiSuccess {Number} code
     * @apiSuccess {Object[]} data
     * @apiSuccess {String} msg
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {"code":0,"data":{user...:"",...},"msg":"success"}
     * @author: tioncico < 1067197739@qq.cn >
     */
    function login()
    {
        $hash = $this->request()->getCookieParams('verifyCodeHash');
        $time = $this->request()->getCookieParams('verifyCodeTime');
        $param = $this->request()->getRequestParam();
        //调用后过期
        $this->response()->setCookie('verifyCodeHash', null, -1);
        $this->response()->setCookie('verifyCodeTime', null, -1);

        if (!VerifyCodeService::checkVerifyCode($param['verifyCode'], $time, $hash)) {
            $this->writeJson(Status::CODE_BAD_REQUEST, null, '验证码错误');
            return false;
        }
        $model = new UserModel();
        $model->phone = $param['phone'];
        $model->userPassword = md5($param['password']);

        $user = $model->login();
        if (!$user) {
            $this->writeJson(Status::CODE_BAD_REQUEST, null, '帐号或密码错误');
            return false;
        }

        // 判断用户状态
        if ($user->isForbid == 1) {
            $this->writeJson(Status::CODE_BAD_REQUEST, '该用户禁止登录');
            return false;
        }
        if ($user->isDelete == 1) {
            $this->writeJson(Status::CODE_BAD_REQUEST, '该用户不存在');
            return false;
        }

        // 更新用户session adminLastLoginTime adminLastLoginIp数据

        $time = time();
        $sessionHash = md5($time . $user->userId);
        $user->update([
            'lastLoginIp'   => $this->clientRealIP(),
            'lastLoginTime' => $time,
            'userSession'   => $sessionHash
        ]);
        $user = $user->toArray();
        unset($user['userPassword']);
        $user['userSession'] = $sessionHash;
        $this->response()->setCookie('userSession', $sessionHash, time() + 3600, '/');
        $this->writeJson(Status::CODE_OK, $user);
    }

    /**
     * @api {get|post} /Api/User/Auth/logout
     * @apiName logout
     * @apiGroup User/Auth
     * @apiPermission user
     * @apiDescription 退出
     * @apiSuccess {Number} code
     * @apiSuccess {Object[]} data
     * @apiSuccess {String} msg
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {"code":0,"data":{user...:"",...},"msg":"success"}
     * @author: tioncico < 1067197739@qq.cn >
     */
    function logout()
    {
        $result =  $this->who()->logout();
        if ($result) {
            $this->writeJson(Status::CODE_OK, null, "注销成功");
        } else {
            $this->writeJson(Status::CODE_BAD_REQUEST, '', '注销成功');
        }
    }


    /**
     * @api {get|post} /Api/User/Auth/register
     * @apiName register
     * @apiGroup User/Auth
     * @apiPermission user
     * @apiDescription 会员注册
     * @Param(name="smsVerifyCodeHash", from={COOKIE}, required="")
     * @Param(name="smsVerifyCodeTime", from={COOKIE}, required="")
     * @Param(name="smsVerifyPhone", from={COOKIE}, required="")
     * @Param(name="userPassword",alias="会员密码",required="",lengthMax="18")
     * @Param(name="smsVerifyCode",alias="短信验证码相关",required="",length="6")
     * @apiParam {String} userPassword post|get
     * @apiParam {String} smsVerifyCode post|get
     * @apiParam {String} smsVerifyCodeHash  cookie
     * @apiParam {String} smsVerifyCodeTime  cookie
     * @apiParam {String} smsVerifyPhone  cookie
     * @apiSuccess {Number} code
     * @apiSuccess {Object[]} data
     * @apiSuccess {String} msg
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {"code":0,"data":{user...:"",...},"msg":"success"}
     * @author: tioncico < 1067197739@qq.cn >
     */
    function register()
    {
        $hash = $this->request()->getCookieParams('smsVerifyCodeHash');
        $time = $this->request()->getCookieParams('smsVerifyCodeTime');
        $phone = $this->request()->getCookieParams('smsVerifyPhone');
        $param = $this->request()->getRequestParam();
        if (!VerifyCodeService::checkSmsVerifyCode($param['smsVerifyCode'], $time, $phone, $hash)) {
            $this->writeJson(400, '', '验证码错误');
            return false;
        }

        //判断会员是否存在
        $model = new UserModel();
        $model->phone = $phone;
        $userInfo = $model->getOneByPhone('userId');
        if ($userInfo) {
            $this->writeJson(Status::CODE_BAD_REQUEST, [], '该用户已存在!');
            return false;
        }

        $sessionHash = md5(time() . $phone);
        $model->userPassword = md5($param['userPassword']);
        $model->addTime = time();
        $model->phone = $phone;
        $model->userName = '新用户' . $phone;
        $model->isDelete = UserModel::DELETE_TYPE_NORMAL;
        $model->isForbid = UserModel::FORBID_TYPE_NORMAL;
        $model->userSession = $sessionHash;
        $model->lastLoginIp = $this->clientRealIP();
        $this->response()->setCookie('userSession', $sessionHash, time() + 3600, '/');

        $rs = $model->save();
        unset($rs['userPassword']);
        if ($rs) {
            $this->writeJson(Status::CODE_OK, $rs, "success");
        } else {
            $this->writeJson(Status::CODE_BAD_REQUEST, [], $model->lastQueryResult()->getLastError());
        }
    }

    /**
     * @api {get|post} /Api/User/Auth/getInfo
     * @apiName getInfo
     * @apiGroup User/Auth
     * @apiPermission user
     * @apiDescription 获取会员信息
     * @apiSuccess {Number} code
     * @apiSuccess {Object[]} data
     * @apiSuccess {String} msg
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {"code":0,"data":{user...:"",...},"msg":"success"}
     * @author: tioncico < 1067197739@qq.cn >
     */
    function getInfo()
    {
        $this->writeJson(Status::CODE_OK, $this->who(), '用户信息');
    }
}