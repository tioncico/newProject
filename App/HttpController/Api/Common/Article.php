<?php

namespace App\HttpController\Api\Common;

use App\Model\Article\ArticleBean;
use App\Model\Article\ArticleModel;
use EasySwoole\Http\Message\Status;
use EasySwoole\MysqliPool\Mysql;

/**
 * Class Article
 * Create With Automatic Generator
 */
class Article extends BaseController
{

	/**
	 * @api {get|post} /Api/Common/Article/getOne
	 * @apiName get one article
	 * @apiGroup Common/Article
	 * @apiPermission common
	 * @apiDescription 获取文章
     * @Param(name="articleId", alias="文章id", required="", integer="")
	 * @apiParam {int} articleId 文章id
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
		$model = new ArticleModel();
		$bean = $model->get(['articleId' => $param['articleId']]);
		if ($bean) {
		    $this->writeJson(Status::CODE_OK, $bean, "success");
		} else {
		    $this->writeJson(Status::CODE_BAD_REQUEST, [], 'fail');
		}
	}

	/**
	 * @api {get|post} /Api/Common/Article/getAll
	 * @apiName get article list
	 * @apiGroup Common/Article
	 * @apiPermission common
	 * @apiDescription 获取文章列表
     * @Param(name="page", optional="", integer="")
     * @Param(name="limit", optional="", integer="")
     * @Param(name="keyword", optional="", lengthMax="32")
	 * @apiParam {String} [page=1]
	 * @apiParam {String} [limit=20]
	 * @apiParam {String} [keyword] 文章标题
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
		$model = new ArticleModel();
		$data = $model->getAll($page, $param['keyword']??null, $limit);
		$this->writeJson(Status::CODE_OK, $data, 'success');
	}
    /**
     * @api {get|post} /Api/Common/Article/getListByCategoryId
     * @apiName get article list
     * @apiGroup Admin/Article
     * @apiPermission admin
     * @apiDescription 根据分类id获取文章列表
     * @Param(name="categoryId", alias="分类id", optional="", integer="", lengthMax="10")
     * @Param(name="page", optional="", integer="")
     * @Param(name="limit", optional="", integer="")
     * @Param(name="keyword", optional="", lengthMax="32")
     * @apiParam {String} adminSession 权限验证token
     * @apiParam {String} [page=1]
     * @apiParam {String} [limit=20]
     * @apiParam {String} [keyword] 文章标题
     * @apiParam {int} [categoryId] 分类id
     * @apiSuccess {Number} code
     * @apiSuccess {Object[]} data
     * @apiSuccess {String} msg
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {"code":200,"data":{},"msg":"success"}
     * @author: AutomaticGeneration < 1067197739@qq.com >
     */
    public function getListByCategoryId()
    {
        $param = $this->request()->getRequestParam();
        $page = (int)($param['page']??1);
        $limit = (int)($param['limit']??20);
        $categoryId = (int)$this->input('categoryId');

        $model = new ArticleModel();
        $data = $model->getAllByCategoryId($page,$categoryId, $param['keyword']??null, $limit);
        $this->writeJson(Status::CODE_OK, $data, 'success');
    }
    /**
     * @api {get|post} /Api/Common/Article/getArticleIdAll
     * @apiName get article list
     * @apiGroup Admin/Article
     * @apiPermission admin
     * @apiDescription 根据文章id获取文章列表
     * @Param(name="articleId", alias="文章id", optional="", integer="", lengthMax="10")
     * @Param(name="page", optional="", integer="")
     * @Param(name="limit", optional="", integer="")
     * @Param(name="keyword", optional="", lengthMax="32")
     * @apiParam {String} adminSession 权限验证token
     * @apiParam {String} [page=1]
     * @apiParam {String} [limit=20]
     * @apiParam {String} [keyword] 文章标题
     * @apiParam {int} [articleId] 文章id
     * @apiSuccess {Number} code
     * @apiSuccess {Object[]} data
     * @apiSuccess {String} msg
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {"code":200,"data":{},"msg":"success"}
     * @author: AutomaticGeneration < 1067197739@qq.com >
     */
    public function getListByArticleId()
    {
        $param = $this->request()->getRequestParam();
        $page = (int)($param['page']??1);
        $limit = (int)($param['limit']??20);
        $articleId = (int)$this->input('articleId');

        $model = new ArticleModel();
        $data = $model->getListByArticleId($page,$articleId, $param['keyword']??null, $limit);
        $this->writeJson(Status::CODE_OK, $data, 'success');
    }

}

