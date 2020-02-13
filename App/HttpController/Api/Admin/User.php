<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 19-7-26
 * Time: 下午3:39
 */

namespace App\HttpController\Api\Admin;


use App\Model\User\UserModel;
use App\Service\Admin\AddressService;
use EasySwoole\Http\Message\Status;
use LogicAssert\Assert;


class User extends BaseController
{

    /**
     * @api {get|post} /Api/Admin/User/getAll
     * @apiName get user list
     * @apiGroup Admin/User
     * @apiPermission admin
     * @apiDescription 获取用户列表
     * @Param(name="page", alias="页数", optional="", integer="")
     * @Param(name="limit", alias="每页数量", optional="", lengthMax="3", integer="")
     * @Param(name="keyword", alias="关键字", optional="", lengthMax="64")
     * @apiParam {String} adminSession 权限验证token
     * @apiParam {String} [page] 页数 post|get
     * @apiParam {String} [limit] 每页数量 post|get
     * @apiParam {String} [keyword] 关键字 post|get
     * @apiSuccess {Number} code
     * @apiSuccess {Object[]} result
     * @apiSuccess {String} msg
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {"code": 200, "result": {list:[{...},{...}], total: 2}, "msg": "success"}
     * @author: tioncico < 1067197739@qq.cn >
     */
    function getAll() {
        $page = (int)$this->input('page', 1);
        $limit = (int)$this->input('limit', 20);
        $keyword = $this->input('keyword');
        $model = new UserModel();
        $data = $model->getAll($page, $keyword, $limit);
        $this->writeJson(Status::CODE_OK, $data, 'success');
    }

    /**
     * @api {get|post} /Api/Admin/User/getOne
     * @apiName get one user
     * @apiGroup Admin/User
     * @apiPermission admin
     * @apiDescription 获取用户
     * @Param(name="userId", required="", integer="")
     * @apiParam {String} adminSession 权限验证token
     * @apiParam {String} userId post|get
     * @apiSuccess {Number} code
     * @apiSuccess {Object[]} result
     * @apiSuccess {String} msg
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {"code": 200, "result": {...}, "msg": "success"}
     * @author: tioncico < 1067197739@qq.cn >
     */
    function getOne() {
        $param = $this->request()->getRequestParam();
        $model = new UserModel();
        $result = $model->get($param['userId']);

        //断言结果为true
        Assert::assertTrue(!!$result, $model->lastQueryResult()->getLastError());
        Assert::assertEquals($result->isDelete, UserModel::DELETE_TYPE_NORMAL, '用户不存在');
        $this->writeJson(Status::CODE_OK, null, "success");
    }

    /**
     * @api {get|post} /Api/Admin/User/add
     * @apiName add one user
     * @apiGroup Admin/User
     * @apiPermission admin
     * @apiDescription 添加用户
     * @Param(name="userName", alias="用户名称", required="", lengthMax="30")
     * @Param(name="phone", alias="用户手机号", required="", regex="/^1\d{10}$/")
     * @Param(name="sex", alias="用户性别", optional="", inArray="{1, 2}")
     * @Param(name="userPassword", alias="用户密码", required="", lengthMin="6", lengthMax="16")
     * @Param(name="isForbid", alias="禁用状态", optional="", inArray="{0, 1}")
     * @apiParam {String} adminSession 权限验证token
     * @apiParam {String} userName 用户名称 post|get
     * @apiParam {String} phone 用户手机号 post|get
     * @apiParam {String} [sex] 用户性别 {1, 2} post|get
     * @apiParam {String} userPassword 用户密码 post|get
     * @apiParam {String} [provinceId] 省级 post|get
     * @apiParam {String} [cityId] 市区 post|get
     * @apiParam {String} [countyId] 县级 post|get
     * @apiParam {String} [address] 详细地址 post|get
     * @apiParam {String} [isForbid] 禁用状态 {0, 1} post|get
     * @apiSuccess {Number} code
     * @apiSuccess {Object[]} result
     * @apiSuccess {String} msg
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {"code": 200, "result": null, "msg": "success"}
     * @author: tioncico < 1067197739@qq.cn >
     */
    function add() {
        $param = $this->request()->getRequestParam();
        $model = new UserModel();

        $data = [
            'userName' => $param['userName'],
            'phone' => $param['phone'],
            'sex' => $param['sex'] ?? 0,
            'userPassword' => md5($param['userPassword']),
            'addTime' => time(),
            'isForbid' => $param['isForbid'] ?? UserModel::FORBID_TYPE_NORMAL,
            'isDelete' => $param['isDelete'] ?? UserModel::DELETE_TYPE_NORMAL,
        ];

        $model = $model::create($data);
        $result = $model->save();

        Assert::assertTrue(!!$result, $model->lastQueryResult()->getLastError());

        $this->writeJson(Status::CODE_OK, null, "success");
    }

    /**
     * @api {get|post} /Api/Admin/User/update
     * @apiName update one user
     * @apiGroup Admin/User
     * @apiPermission admin
     * @apiDescription 更新用户
     * @Param(name="userId", required="", integer="")
     * @Param(name="userName", alias="用户名称", optional="", lengthMax="30")
     * @Param(name="sex", alias="用户性别", optional="", inArray="{1, 2}")
     * @Param(name="userPassword", alias="用户密码", optional="", lengthMin="6", lengthMax="16")
     * @Param(name="isForbid", alias="禁用状态", optional="", inArray="{0, 1}")
     * @apiParam {String} adminSession 权限验证token
     * @apiParam {String} userId post|get
     * @apiParam {String} [userName] 用户名称 post|get
     * @apiParam {String} [sex] 用户性别 {1, 2} post|get
     * @apiParam {String} [userPassword] 用户密码 post|get
     * @apiParam {String} [isForbid] 禁用状态 {0, 1} post|get
     * @apiSuccess {Number} code
     * @apiSuccess {Object[]} result
     * @apiSuccess {String} msg
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {"code": 200, "result": null, "msg": "success"}
     * @author: tioncico < 1067197739@qq.cn >
     */
    function update() {
        $param = $this->request()->getRequestParam();
        $model = new UserModel();
        $userId = $param['userId'];

        $bean = $model->get($userId);

        Assert::assertTrue(!!$bean, '该数据不存在');
        Assert::assertEquals($bean->isDelete, UserModel::DELETE_TYPE_NORMAL, '该数据不存在');


        $data = [
            'userName' => $param['userName'] ?? $bean->userName,
            'sex' => $param['sex'] ?? $bean->sex,
            'userPassword' => isset($param['userPassword']) ? md5($param['userPassword']) : $bean->userPassword,
            'isForbid' => $param['isForbid'] ?? $bean->isForbid,
        ];

        $result = $bean->update($data);

        Assert::assertTrue(!!$result, $bean->lastQueryResult()->getLastError());

        $this->writeJson(Status::CODE_OK, null, "success");
    }

    /**
     * @api {get|post} /Api/Admin/User/delete
     * @apiName delete one user
     * @apiGroup Admin/User
     * @apiPermission admin
     * @apiDescription 删除用户
     * @Param(name="userId", required="", integer="")
     * @apiParam {String} adminSession 权限验证token
     * @apiParam {String} userId post|get
     * @apiSuccess {Number} code
     * @apiSuccess {Object[]} result
     * @apiSuccess {String} msg
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {"code": 200, "result": null, "msg": "success"}
     * @author: tioncico < 1067197739@qq.cn >
     */
    function delete() {
        $param = $this->request()->getRequestParam();
        $model = new UserModel();
        $result = $model->destroy($param['userId']);

        Assert::assertTrue(!!$result, $model->lastQueryResult()->getLastError());

        $this->writeJson(Status::CODE_OK, null, "success");
    }

}