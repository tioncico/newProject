<?php

namespace App\HttpController\Api\Admin;

use App\Model\Article\ArticleCategoryModel;
use App\Service\Admin\ArticleCategoryService;
use EasySwoole\Http\Annotation\Param;
use EasySwoole\Http\Message\Status;
use LogicAssert\Assert;

/**
 * Class ArticleCategory
 * Create With Automatic Generator
 */
class ArticleCategory extends BaseController
{
	/**
	 * @api {get|post} /Api/Admin/ArticleCategory/add
	 * @apiName add article category
	 * @apiGroup Admin/ArticleCategory
	 * @apiPermission admin
	 * @apiDescription 新增文章分类
     * @Param(name="categoryName", alias="分类名称", required="", lengthMax="64")
     * @Param(name="pid", alias="父级分类id", required="", integer="")
     * @Param(name="note", alias="分类备注", optional="")
     * @apiParam {String} adminSession 权限验证token
	 * @apiParam {string} categoryName 分类名称
	 * @apiParam {int} pid 父级分类id
	 * @apiParam {int} [note] 分类备注
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
		$model = new ArticleCategoryModel();
		$data = [
		    'categoryName' => $param['categoryName'],
            'pid' => $param['pid'],
            'note' => $param['note'],
        ];
        $model = $model::create($data);
		$rs = $model->save();

		Assert::assertTrue(!!$rs, $model->lastQueryResult()->getLastError());

        $this->writeJson(Status::CODE_OK, null, "success");
	}


	/**
	 * @api {get|post} /Api/Admin/ArticleCategory/update
	 * @apiName update article category
	 * @apiGroup Admin/ArticleCategory
	 * @apiPermission admin
	 * @apiDescription 修改文章分类
     * @Param(name="categoryId", alias="文章分类id", required="", integer="")
     * @Param(name="categoryName", alias="分类名称", optional="", lengthMax="64")
     * @Param(name="pid", alias="父级分类id", optional="", integer="")
     * @Param(name="note", alias="分类备注", optional="")
     * @apiParam {String} adminSession 权限验证token
	 * @apiParam {int} categoryId 文章分类id
	 * @apiParam {string} [categoryName] 分类名称
	 * @apiParam {int} [pid] 父级分类id
	 * @apiParam {int} [note] 分类备注
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
		$model = new ArticleCategoryModel();
        $model = $model->get($param['categoryId']);
        if (empty($model)) {
            $this->writeJson(Status::CODE_BAD_REQUEST, [], '该数据不存在');
            return false;
        }
		$data = [
		    'categoryName' => $param['categoryName'] ?? $model->categoryName,
            'pid' => $param['categoryName'] ?? $model->categoryName,
            'note' => $param['note']??$model->note,
        ];
        $result = $model->update($data);

        if ($result) {
            $this->writeJson(Status::CODE_OK, $result, "success");
        } else {
            $this->writeJson(Status::CODE_BAD_REQUEST, [], $model->lastQueryResult()->getLastError());
        }
	}


	/**
	 * @api {get|post} /Api/Admin/ArticleCategory/getOne
	 * @apiName get one article category
	 * @apiGroup Admin/ArticleCategory
	 * @apiPermission admin
	 * @apiDescription 获取文章分类
	 * @apiParam {int} categoryId 主键id
     * @Param(name="categoryId", alias="文章分类id", required="", integer="")
     * @apiParam {String} adminSession 权限验证token
     * @apiParam {int} categoryId 文章分类id
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
		$model = new ArticleCategoryModel();
		$bean = $model->get($param['categoryId']);

		Assert::assertTrue(!!$bean, 'fail');

        $this->writeJson(Status::CODE_OK, $bean, "success");
	}


	/**
	 * @api {get|post} /Api/Admin/ArticleCategory/getAll
	 * @apiName get article category list
	 * @apiGroup Admin/ArticleCategory
	 * @apiPermission admin
	 * @apiDescription 获取文章分类列表
     * @Param(name="page", optional="", integer="")
     * @Param(name="limit", optional="", integer="")
     * @Param(name="keyword", optional="", lengthMax="32")
     * @apiParam {String} adminSession 权限验证token
	 * @apiParam {String} [page=1]
	 * @apiParam {String} [limit=20]
	 * @apiParam {String} [keyword] 文章分类名称
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
		$page = (int)($param['page']??1);
		$limit = (int)($param['limit']??20);
		$model = new ArticleCategoryModel();
		$data = $model->getAll($page, $param['keyword']??null, $limit);
		$this->writeJson(Status::CODE_OK, $data, 'success');
	}


	/**
	 * @api {get|post} /Api/Admin/ArticleCategory/delete
	 * @apiName delete article category
	 * @apiGroup Admin/ArticleCategory
	 * @apiPermission admin
	 * @apiDescription 删除文章分类
     * @Param(name="categoryId", alias="文章分类id", required="", integer="")
     * @apiParam {String} adminSession 权限验证token
	 * @apiParam {int} categoryId 主键id
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

		// 检查该分类有没有文章

        ArticleCategoryService::checkArticleByCategoryId($param['categoryId']);

		$model = new ArticleCategoryModel();

		$rs = $model->destroy($param['categoryId']);

		Assert::assertTrue(!!$rs, 'fail');

        $this->writeJson(Status::CODE_OK, [], "success");
	}

    /**
     * @api {get|post} /Api/Admin/ArticleCategory/getPidAll
     * @apiName get article category list
     * @apiGroup Admin/ArticleCategory
     * @apiPermission admin
     * @apiDescription 根据pid获取文章分类列表
     * @Param(name="pid", alias="父级id", optional="", integer="", lengthMax="10")
     * @Param(name="page", optional="", integer="")
     * @Param(name="limit", optional="", integer="")
     * @Param(name="keyword", optional="", lengthMax="32")
     * @apiParam {String} adminSession 权限验证token
     * @apiParam {String} [page=1]
     * @apiParam {String} [limit=20]
     * @apiParam {String} [keyword] 文章分类名称
     * @apiParam {int} [pid] 分类id
     * @apiSuccess {Number} code
     * @apiSuccess {Object[]} data
     * @apiSuccess {String} msg
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {"code":200,"data":{},"msg":"success"}
     * @author: AutomaticGeneration < 1067197739@qq.com >
     */
    public function getPidAll()
    {
        $param = $this->request()->getRequestParam();
        $page = (int)($param['page']??1);
        $limit = (int)($param['limit']??20);
        $pid = (int)$this->input('pid', 0);
        $model = new ArticleCategoryModel();
        $data = $model->getPidAll($page, $pid, $param['keyword']??null, $limit);
        $this->writeJson(Status::CODE_OK, $data, 'success');
    }


}

