<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 19-7-26
 * Time: 上午11:33
 */

namespace App\HttpController\Api\Admin;


use App\Model\Admin\AdminUserModel;
use EasySwoole\Http\Annotation\Param;
use EasySwoole\Http\Message\Status;
use EasySwoole\MysqliPool\Mysql;
use EasySwoole\Spl\SplBean;
use LogicAssert\Assert;

class AdminUser extends BaseController
{

    /**
     * @api {get|post} /Api/Admin/AdminUser/getAll
     * @apiName get admin user list
     * @apiGroup Admin/AdminUser
     * @apiPermission admin
     * @apiDescription 获取后台用户列表
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
    function getAll()
    {
        $page = (int)$this->input('page', 1);
        $limit = (int)$this->input('limit', 20);
        $keyword = $this->input('keyword');
        $model = new AdminUserModel();
        $data = $model->getAll($page, $keyword, $limit);
        $this->writeJson(Status::CODE_OK, $data, 'success');
    }

    /**
     * @api {get|post} /Api/Admin/AdminUser/getOne
     * @apiName get one admin user
     * @apiGroup Admin/AdminUser
     * @apiPermission admin
     * @apiDescription 获取后台用户
     * @Param(name="adminId", required="", integer="")
     * @apiParam {String} adminSession 权限验证token
     * @apiParam {String} adminId post|get
     * @apiSuccess {Number} code
     * @apiSuccess {Object[]} result
     * @apiSuccess {String} msg
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {"code": 200, "result": {...}, "msg": "success"}
     * @author: tioncico < 1067197739@qq.cn >
     */
    function getOne()
    {
        $param = $this->request()->getRequestParam();
        $model = new AdminUserModel();
        $result = $model->get(['adminId' => $param['adminId']]);

        Assert::assertTrue(!!$result, $model->lastQueryResult()->getLastError());
        Assert::assertEquals($result->isDelete, AdminUserModel::DELETE_TYPE_NORMAL, '不存在该记录');

        $this->writeJson(Status::CODE_OK, $result, "success");
    }

    /**
     * @api {get|post} /Api/Admin/AdminUser/add
     * @apiName add one admin user
     * @apiGroup Admin/AdminUser
     * @apiPermission admin
     * @apiDescription 添加后台用户
     * @Param(name="adminName", alias="管理员名称", required="", lengthMax="30")
     * @Param(name="adminAccount", alias="管理员帐号", required="", lengthMin="6", lengthMax="20")
     * @Param(name="adminPassword", alias="管理员密码", required="", lengthMin="6", lengthMax="16")
     * @Param(name="isForbid", alias="禁用状态", optional="", inArray="{0, 1}")
     * @apiParam {String} adminSession 权限验证token
     * @apiParam {String} adminName 管理员名称 post|get
     * @apiParam {String} adminAccount 管理员帐号 post|get
     * @apiParam {String} adminPassword 管理员密码 post|get
     * @apiParam {String} [isForbid] 禁用状态 (0, 1) post|get
     * @apiSuccess {Number} code
     * @apiSuccess {Object[]} result
     * @apiSuccess {String} msg
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {"code": 200, "result": null, "msg": "success"}
     * @author: tioncico < 1067197739@qq.cn >
     */
    function add()
    {
        $param = $this->request()->getRequestParam();
        $model = new AdminUserModel();
        $model->adminAccount = $param['adminAccount'];
        $admin = $model->getOneByAccount();

        // 帐号存在并且正常使用
        if ($admin && $admin->isDelete === AdminUserModel::DELETE_TYPE_NORMAL) {
            $this->writeJson(Status::CODE_BAD_REQUEST, null, '帐号已经被注册');
            return false;
        }

        $data = [
            'adminName'     => $param['adminName'],
            'adminAccount'  => $param['adminAccount'],
            'adminPassword' => md5($param['adminPassword']),
            'addTime'       => time(),
            'isForbid'      => $param['isForbid'] ?? 0,
            'isDelete'      => 0,
        ];
        // 记录不存在

        if (!$admin) {
            $result = $model::create($data)->save();

            Assert::assertTrue(!!$result, $model->lastQueryResult()->getLastError());

            $this->writeJson(Status::CODE_OK, null, "success");
            return false;
        }

        // 记录存在 更新数据
        $result = $admin->update($data);

        Assert::assertTrue(!!$result, $model->lastQueryResult()->getLastError());

        $this->writeJson(Status::CODE_OK, null, "success");

    }

    /**
     * @api {get|post} /Api/Admin/AdminUser/update
     * @apiName update one admin user
     * @apiGroup Admin/AdminUser
     * @apiPermission admin
     * @apiDescription 更新后台用户
     * @Param(name="adminId", required="", integer="")
     * @Param(name="adminName", alias="管理员名称", optional="", lengthMax="30")
     * @Param(name="adminPassword", alias="管理员密码", optional="", lengthMin="6", lengthMax="16")
     * @Param(name="isForbid", alias="禁用状态", optional="", inArray="{0, 1}")
     * @apiParam {String} adminSession 权限验证token
     * @apiParam {String} adminId 管理员名称 post|get
     * @apiParam {String} [adminName] 管理员名称 post|get
     * @apiParam {String} [adminPassword] 管理员密码 post|get
     * @apiParam {String} [isForbid] 禁用状态 (0, 1) post|get
     * @apiSuccess {Number} code
     * @apiSuccess {Object[]} result
     * @apiSuccess {String} msg
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {"code": 200, "result": null, "msg": "success"}
     * @author: tioncico < 1067197739@qq.cn >
     */
    function update()
    {
        $param = $this->request()->getRequestParam();
        $model = new AdminUserModel();
        $adminId = $param['adminId'];

        /**
         * @var $bean AdminUserModel
         */
        $bean = $model->get(['adminId' => $adminId]);
        Assert::assertTrue(!!$bean, '该数据不存在');
        Assert::assertEquals($bean->isDelete, AdminUserModel::DELETE_TYPE_NORMAL, '用户不存在');

        $updateData = [
            'adminName'     => $param['adminName'] ?? $bean->adminName,
            'adminPassword' => isset($param['adminPassword']) ? md5($param['adminPassword']) : $bean->adminPassword,
            'isForbid'      => $param['isForbid'] ?? $bean->isForbid,
        ];
        $result = $bean->update($updateData);

        Assert::assertTrue(!!$result, $model->lastQueryResult()->getLastError());

        $this->writeJson(Status::CODE_OK, null, "success");

    }

    /**
     * @api {get|post} /Api/Admin/AdminUser/delete
     * @apiName delete one admin user
     * @apiGroup Admin/AdminUser
     * @apiPermission admin
     * @apiDescription 删除后台用户
     * @Param(name="adminId", required="", integer="")
     * @apiParam {String} adminSession 权限验证token
     * @apiParam {String} adminId 管理员名称 post|get
     * @apiSuccess {Number} code
     * @apiSuccess {Object[]} result
     * @apiSuccess {String} msg
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {"code": 200, "result": null, "msg": "success"}
     * @author: tioncico < 1067197739@qq.cn >
     */
    function delete()
    {
        $param = $this->request()->getRequestParam();
        $model = new AdminUserModel();
        $result = $model->destroy($param['adminId']);

        Assert::assertTrue(!!$result, $model->lastQueryResult()->getLastError());

        $this->writeJson(Status::CODE_OK, null, "success");
    }

}