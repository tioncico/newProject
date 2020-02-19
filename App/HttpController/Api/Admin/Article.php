<?php

namespace App\HttpController\Api\Admin;

use App\Model\Article\ArticleModel;
use App\Service\Admin\ArticleCategoryService;
use App\Service\Admin\OssFileService;
use EasySwoole\Http\Annotation\Param;
use EasySwoole\Http\Message\Status;
use LogicAssert\Assert;

/**
 * Class Article
 * Create With Automatic Generator
 */
class Article extends BaseController
{
    protected $storageName = 'Article';

	/**
	 * @api {get|post} /Api/Admin/Article/add
	 * @apiName add article
	 * @apiGroup Admin/Article
	 * @apiPermission admin
	 * @apiDescription 新增文章
     * @Param(name="categoryId", alias="分类id", required="", integer="")
     * @Param(name="title", alias="标题", required="", lengthMax="64")
     * @Param(name="description", alias="简介", optional="", lengthMax="255")
     * @Param(name="imgUrl", alias="缩略图",  lengthMax="255")
     * @Param(name="author", alias="作者", required="", lengthMax="32")
     * @Param(name="content", alias="内容", optional="")
     * @Param(name="state", alias="状态", optional="", inArray="{1, 2}")
     * @Param(name="note", alias="文章备注", optional="")
     * @apiParam {String} adminSession 权限验证token
	 * @apiParam {int} categoryId 分类id
	 * @apiParam {string} title 标题
	 * @apiParam {string} [description] 简介
	 * @apiParam {string} author 作者
     * @apiParam {string} [imgUrl] 缩略图
     * @apiParam {string} [content] 内容
     * @apiParam {string} [note] 文章备注
	 * @apiParam {int} state 状态 1正常,2隐藏
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

		// 检查文章分类是否存在
        $category = ArticleCategoryService::checkArticleCategory($param['categoryId']);

		$model = new ArticleModel();

        // 存放移动之后的图片路径
        $data = $this->getFiles();
        $files = $data['files'];
        $content = $data['content'];

		$data = [
		    'categoryId' => $param['categoryId'],
            'categoryName' => $category->categoryName,
            'title' => $param['title'],
            'imgUrl' => OssFileService::moveFile($param['imgUrl'], $this->storageName),
            'description' => $param['description']??'',
            'author' => $param['author']??'',
            'note' => $param['note']??'',
            'content' => $content ?? '',
            'addTime' => time(),
            'state' => $param['state'] ?? 1
        ];
        if (!empty($param['imgUrl'])) {
            $data['imgUrl'] = OssFileService::moveFile($param['imgUrl'], $this->storageName);
        }
		$rs = $model::create($data)->save();

		if ($rs) {
		    $this->writeJson(Status::CODE_OK, null, "success");
		} else {
            if (!empty($param['imgUrl'])) {
                OssFileService::delete($data['imgUrl']);
            }
		    foreach ($files as $file) {
                OssFileService::delete($file);
            }

		    $this->writeJson(Status::CODE_BAD_REQUEST, [], $model->lastQueryResult()->getLastError());
		}
	}


	/**
	 * @api {get|post} /Api/Admin/Article/update
	 * @apiName update article
	 * @apiGroup Admin/Article
	 * @apiPermission admin
	 * @apiDescription 修改文章
     * @Param(name="articleId", alias="文章id", required="", integer="")
     * @Param(name="categoryId", alias="分类id", optional="", integer="")
     * @Param(name="title", alias="标题", optional="", lengthMax="64")
     * @Param(name="imgUrl", alias="缩略图", lengthMax="100")
     * @Param(name="description", alias="父级分类id", optional="", lengthMax="255")
     * @Param(name="author", alias="作者", optional="", lengthMax="32")
     * @Param(name="content", alias="内容", optional="")
     * @Param(name="state", alias="状态", optional="", inArray="{1, 2}")
     * @Param(name="note", alias="文章备注", optional="")
     * @apiParam {String} adminSession 权限验证token
	 * @apiParam {int} articleId 文章id
	 * @apiParam {int} [categoryId] 分类id
	 * @apiParam {string} [title] 标题
	 * @apiParam {string} [title] 英文标题
     * @apiParam {string} [imgUrl] 缩略图
     * @apiParam {string} [description] 简介
	 * @apiParam {string} [author] 作者
	 * @apiParam {string} [content] 内容
     * @apiParam {string} [note] 文章备注
     * @apiParam {int} [state] 状态 1正常,2隐藏
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
		$model = new ArticleModel();
        $model = $model->get($param['articleId']);
        Assert::assertTrue(!!$model, '该数据不存在');

        // 存放移动之后的图片路径
        $data = $this->getFiles();
        $files = $data['files'];
        $content = $data['content'];

		$data = [
		    'title' => $param['title'] ?? $model->title,
            'imgUrl' => isset($param['imgUrl']) ? OssFileService::moveFile($param['imgUrl'], $this->storageName) : $model->imgUrl,
            'description' => $param['description'] ?? $model->description,
            'author' => $param['author'] ?? $model->author,
            'content' => $content ?? $model->content,
            'updateTime' => time(),
            'state' => $param['state'] ?? $model->state
        ];

        if (isset($param['categoryId']) && $param['categoryId'] != $model->getCategoryId()) {
            // 检查文章分类是否存在
            $category = ArticleCategoryService::checkArticleCategory($param['categoryId']);
            $data['categoryId'] = $param['categoryId'];
            $data['categoryName'] = $category->categoryName;
        }
        if (!empty($param['imgUrl'])) {
            $data['imgUrl'] = OssFileService::moveFile($param['imgUrl'], $this->storageName);
        }
		$rs = $model->update($data);
		if ($rs) {
		    $this->writeJson(Status::CODE_OK, $rs, "success");
		} else {
            if (!empty($param['imgUrl'])) {
                OssFileService::delete($data['imgUrl']);
            }
            foreach ($files as $file) {
                OssFileService::delete($file);
            }

		    $this->writeJson(Status::CODE_BAD_REQUEST, [], $model->lastQueryResult()->getLastError());
		}
	}


	/**
	 * @api {get|post} /Api/Admin/Article/getOne
	 * @apiName get one article
	 * @apiGroup Admin/Article
	 * @apiPermission admin
	 * @apiDescription 获取文章
     * @Param(name="articleId", alias="文章id", required="", integer="")
     * @apiParam {String} adminSession 权限验证token
	 * @apiParam {int} articleId 文章id
	 * @apiSuccess {Number} code
     *
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
		$bean = $model->get($param['articleId']);

		Assert::assertTrue(!!$bean, 'fail');

        $this->writeJson(Status::CODE_OK, $bean, "success");
	}


	/**
	 * @api {get|post} /Api/Admin/Article/getAll
	 * @apiName get article list
	 * @apiGroup Admin/Article
	 * @apiPermission admin
	 * @apiDescription 获取文章列表
     * @Param(name="page", optional="", integer="")
     * @Param(name="limit", optional="", integer="")
     * @Param(name="keyword", optional="", lengthMax="32")
     * @apiParam {String} adminSession 权限验证token
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
	 * @api {get|post} /Api/Admin/Article/getCategoryIdAll
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
	public function getCategoryIdAll()
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
	 * @api {get|post} /Api/Admin/Article/delete
	 * @apiName delete article
	 * @apiGroup Admin/Article
	 * @apiPermission admin
	 * @apiDescription 删除文章
     * @Param(name="articleId", alias="文章id", required="", integer="")
     * @apiParam {String} adminSession 权限验证token
	 * @apiParam {int} articleId 文章id
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
		$model = new ArticleModel();

		$rs = $model->destroy($param['articleId']);

		Assert::assertTrue(!!$rs, 'fail');

        $this->writeJson(Status::CODE_OK, [], "success");
	}

    /**
     * 获取文本上传图片迁移数组数据
     * @return array
     */
	private function getFiles() {
	    $param = $this->request()->getRequestParam();
        $files = [];
        $content = $param['content'];
        if (!empty($param['content'])) {
            preg_match_all('/src="(.*?)"/', $content, $matches);
            foreach ($matches[1] as $match) {
                $path = OssFileService::moveFile($match, $this->storageName);
                if ($path !== $match) {
                    $files[] = $path;
                    $content = str_replace($match, $path, $content);
                }
            }
            $param['content'] = $content;
        }
        return ['content' => $content, 'files' => $files];
    }

}

