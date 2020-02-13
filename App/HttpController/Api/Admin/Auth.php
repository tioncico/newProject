<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 19-7-25
 * Time: 下午4:38
 */

namespace App\HttpController\Api\Admin;


use App\Model\Admin\AdminUserBean;
use App\Model\Admin\AdminUserModel;
use App\Service\Common\VerifyCodeService;
use EasySwoole\Http\Message\Status;
use EasySwoole\MysqliPool\Mysql;
use EasySwoole\Spl\SplBean;
use EasySwoole\Http\Annotation\Param;
use LogicAssert\Assert;

class Auth extends BaseController
{

    /**
     * @api {get|post} /Api/Admin/Auth/login
     * @apiName admin user login
     * @apiGroup Admin/Auth
     * @apiPermission admin
     * @apiDescription 后台用户登录
     * @Param(name="verifyCodeHash", from={COOKIE}, required="")
     * @Param(name="verifyCodeTime", from={COOKIE}, required="")
     * @Param(name="account", alias="帐号", required="", lengthMax="20")
     * @Param(name="password", alias="密码", required="", lengthMin="6", lengthMax="16")
     * @Param(name="verifyCode", alias="验证码", required="", length="4")
     * @apiParam {String} verifyCodeHash cookie
     * @apiParam {String} verifyCodeTime cookie
     * @apiParam {String} account 帐号 get|post
     * @apiParam {String} password 密码 get|post
     * @apiParam {String} verifyCode 验证码 get|post
     * @apiSuccess {Number} code
     * @apiSuccess {Object[]} result
     * @apiSuccess {String} msg
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {"code": 200, "result": {...}, "msg": null}
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

        if (VerifyCodeService::checkVerifyCode($param['verifyCode'], $time, $hash)) {

            $model = new AdminUserModel();
            $model->adminAccount = $param['account'];
            $model->adminPassword = md5($param['password']);

            $admin = $model->login();
            if ($admin) {
                // 判断用户状态
                if ($admin->isDelete === AdminUserModel::DELETE_TYPE_DELETED) {
                    $this->writeJson(Status::CODE_BAD_REQUEST, '帐号不存在');
                    return false;
                } else {

                    if ($admin->isForbid === AdminUserModel::FORBID_TYPE_DISABLED) {
                        $this->writeJson(Status::CODE_BAD_REQUEST, '该用户禁止登录');
                        return false;
                    } else {

                        // 更新用户session lastLoginTime lastLoginIp数据
                        $time = time();
                        $sessionHash = md5($time . $admin->adminId);
                        $admin->update([
                            'lastLoginTime' => $time,
                            'lastLoginIp'   => $this->clientRealIP(),
                            'adminSession'       => $sessionHash
                        ]);
                        $admin = $admin->toArray();
                        unset($admin['adminPassword']);
                        $admin['adminSession'] = $sessionHash;
                        $this->response()->setCookie('adminSession', $sessionHash, time() + 3600, '/');
                        $this->writeJson(Status::CODE_OK, $admin);
                    }
                }

            } else {
                $this->writeJson(Status::CODE_BAD_REQUEST, null, '帐号或密码错误');
                return false;
            }
        } else {
            $this->writeJson(Status::CODE_BAD_REQUEST, null, '验证码错误');
        }
    }


    /**
     * @api {get|post} /Api/Admin/Auth/login
     * @apiName admin user logout
     * @apiGroup Admin/Auth
     * @apiPermission admin
     * @apiDescription 后台用户注销
     * @apiParam {String} adminSession 权限验证token
     * @apiSuccess {Number} code
     * @apiSuccess {Object[]} result
     * @apiSuccess {String} msg
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {"code": 200, "result": null, "msg": "注销成功"}
     * @author: tioncico < 1067197739@qq.cn >
     */
    function logout()
    {
        $admin = $this->who();
        $result = $admin->logout();

        Assert::assertTrue(!!$result, '注销失败');

        $this->writeJson(Status::CODE_OK, null, "注销成功");
    }

    /**
     * @api {get|post} /Api/Admin/Auth/getInfo
     * @apiName get admin user info
     * @apiGroup Admin/Auth
     * @apiPermission admin
     * @apiDescription 用户基本信息
     * @apiParam {String} adminSession 权限验证token
     * @apiSuccess {Number} code
     * @apiSuccess {Object[]} result
     * @apiSuccess {String} msg
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {"code": 200, "result": {...}, "msg": "用户信息"}
     * @author: tioncico < 1067197739@qq.cn >
     */
    function getInfo()
    {
        $this->writeJson(Status::CODE_OK, $this->who(), '用户信息');
    }

}