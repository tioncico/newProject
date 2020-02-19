<?php

namespace App\HttpController\Api\Admin;

use App\Model\WebSettingModel;
use EasySwoole\Http\Annotation\Param;
use EasySwoole\Http\Message\Status;
use EasySwoole\Validate\Validate;

/**
 * Class WebSetting
 * Create With Automatic Generator
 */
class WebSetting extends BaseController
{
    /**
     * @api {get|post} /Api/Admin/WebSetting/add
     * @apiName add
     * @apiGroup /Api/Admin/WebSetting
     * @apiPermission
     * @apiDescription add新增数据
     * @Param(name="name", alias="参数名", required="", lengthMax="64")
     * @Param(name="note", alias="备注", optional="", lengthMax="255")
     * @Param(name="value", alias="参数值", optional="")
     * @Param(name="type", alias="参数值类型", required="", lengthMax="32")
     * @apiParam {string} name 参数名
     * @apiParam {string} [note] 备注
     * @apiParam {string} [value] 参数值
     * @apiParam {string} type 参数值类型
     * @apiSuccess {Number} code
     * @apiSuccess {Object[]} data
     * @apiSuccess {String} msg
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {"code":200,"data":{},"msg":"success"}
     * @author: AutomaticGeneration < 1067197739@qq.com >
     */
    public function add()
    {
        $param = $this->request()->getRequestParam();
        $data = [
            'name'  => $param['name'],
            'note'  => $param['note'],
            'value' => $param['value'],
            'type'  => $param['type'],
        ];
        $model = new WebSettingModel($data);
        $rs = $model->save();
        if ($rs) {
            $this->writeJson(Status::CODE_OK, $model->toArray(), "success");
        } else {
            $this->writeJson(Status::CODE_BAD_REQUEST, [], $model->lastQueryResult()->getLastError());
        }
    }


    /**
     * @api {get|post} /Api/Admin/WebSetting/update
     * @apiName update
     * @apiGroup /Api/Admin/WebSetting
     * @apiPermission
     * @apiDescription update修改数据
     * @Param(name="id", alias="id", required="", lengthMax="11")
     * @Param(name="name", alias="参数名", optional="", lengthMax="64")
     * @Param(name="note", alias="备注", optional="", lengthMax="255")
     * @Param(name="value", alias="参数值", optional="")
     * @Param(name="type", alias="参数值类型", optional="", lengthMax="32")
     * @apiParam {int} id 主键id
     * @apiParam {mixed} [name] 参数名
     * @apiParam {mixed} [note] 备注
     * @apiParam {mixed} [value] 参数值
     * @apiParam {mixed} [type] 参数值类型
     * @apiSuccess {Number} code
     * @apiSuccess {Object[]} data
     * @apiSuccess {String} msg
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {"code":200,"data":{},"msg":"success"}
     * @author: AutomaticGeneration < 1067197739@qq.com >
     */
    public function update()
    {
        $param = $this->request()->getRequestParam();
        $model = new WebSettingModel();
        $info = $model->get(['id' => $param['id']]);
        if (empty($info)) {
            $this->writeJson(Status::CODE_BAD_REQUEST, [], '该数据不存在');
            return false;
        }
        $updateData = [];

        $updateData['name'] = $param['name'] ?? $info->name;
        $updateData['note'] = $param['note'] ?? $info->note;
        $updateData['value'] = $param['value'] ?? $info->value;
        $updateData['type'] = $param['type'] ?? $info->type;
        $rs = $info->update($updateData);
        if ($rs) {
            $this->writeJson(Status::CODE_OK, $rs, "success");
        } else {
            $this->writeJson(Status::CODE_BAD_REQUEST, [], $model->lastQueryResult()->getLastError());
        }
    }


    /**
     * @api {get|post} /Api/Admin/WebSetting/getOne
     * @apiName getOne
     * @apiGroup /Api/Admin/WebSetting
     * @apiPermission
     * @apiDescription 根据主键获取一条信息
     * @Param(name="id", alias="", optional="", lengthMax="11")
     * @apiParam {int} id 主键id
     * @apiSuccess {Number} code
     * @apiSuccess {Object[]} data
     * @apiSuccess {String} msg
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {"code":200,"data":{},"msg":"success"}
     * @author: AutomaticGeneration < 1067197739@qq.com >
     */
    public function getOne()
    {
        $param = $this->request()->getRequestParam();
        $model = new WebSettingModel();
        $bean = $model->get(['id' => $param['id']]);
        if ($bean) {
            $this->writeJson(Status::CODE_OK, $bean, "success");
        } else {
            $this->writeJson(Status::CODE_BAD_REQUEST, [], 'fail');
        }
    }


    /**
     * @api {get|post} /Api/Admin/WebSetting/getAll
     * @apiName getAll
     * @apiGroup /Api/Admin/WebSetting
     * @apiPermission
     * @apiDescription 获取一个列表
     * @apiParam {String} [page=1]
     * @apiParam {String} [limit=20]
     * @apiParam {String} [keyword] 关键字,根据表的不同而不同
     * @apiSuccess {Number} code
     * @apiSuccess {Object[]} data
     * @apiSuccess {String} msg
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {"code":200,"data":{},"msg":"success"}
     * @author: AutomaticGeneration < 1067197739@qq.com >
     */
    public function getAll()
    {
        $param = $this->request()->getRequestParam();
        $page = (int)($param['page'] ?? 1);
        $limit = (int)($param['limit'] ?? 20);
        $model = new WebSettingModel();
        $data = $model->getAll($page, $limit);
        $this->writeJson(Status::CODE_OK, $data, 'success');
    }


    /**
     * @api {get|post} /Api/Admin/WebSetting/delete
     * @apiName delete
     * @apiGroup /Api/Admin/WebSetting
     * @apiPermission
     * @apiDescription 根据主键删除一条信息
     * @Param(name="id", alias="", required="", lengthMax="11")
     * @apiParam {int} id 主键id
     * @apiSuccess {Number} code
     * @apiSuccess {Object[]} data
     * @apiSuccess {String} msg
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {"code":200,"data":{},"msg":"success"}
     * @author: AutomaticGeneration < 1067197739@qq.com >
     */
    public function delete()
    {
        $param = $this->request()->getRequestParam();
        $model = new WebSettingModel();

        $rs = $model->destroy(['id' => $param['id']]);
        if ($rs) {
            $this->writeJson(Status::CODE_OK, [], "success");
        } else {
            $this->writeJson(Status::CODE_BAD_REQUEST, [], 'fail');
        }
    }
}

