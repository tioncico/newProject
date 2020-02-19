<?php

namespace App\HttpController\Api\Common;

use App\Model\Article\ArticleCategoryBean;
use App\Model\Article\ArticleCategoryModel;
use EasySwoole\Http\Message\Status;
use EasySwoole\MysqliPool\Mysql;

/**
 * Class ArticleCategory
 * Create With Automatic Generator
 */
class ArticleCategory extends BaseController
{

	/**
	 * @api {get|post} /Api/Common/ArticleCategory/getOne
	 * @apiName get one article category
	 * @apiGroup Common/ArticleCategory
	 * @apiPermission common
	 * @apiDescription 获取文章分类
	 * @apiParam {int} categoryId 主键id
     * @Param(name="categoryId", alias="文章分类id", required="", integer="")
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
		$bean = $model->get(['categoryId' => $param['categoryId']]);
		if ($bean) {
		    $this->writeJson(Status::CODE_OK, $bean, "success");
		} else {
		    $this->writeJson(Status::CODE_BAD_REQUEST, [], 'fail');
		}
	}


	/**
	 * @api {get|post} /Api/Common/ArticleCategory/getAll
	 * @apiName get article category list
	 * @apiGroup Common/ArticleCategory
	 * @apiPermission common
	 * @apiDescription 获取文章分类列表
     * @Param(name="page", optional="", integer="")
     * @Param(name="limit", optional="", integer="")
     * @Param(name="keyword", optional="", lengthMax="32")
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

}

