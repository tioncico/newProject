<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 19-8-29
 * Time: 下午2:38
 */

namespace App\Service\Admin;


use App\Model\Article\ArticleCategoryModel;
use App\Model\Article\ArticleModel;
use App\Service\ServiceException;

class ArticleCategoryService extends AdminBaseService
{

    static function checkArticleCategory($categoryId):ArticleCategoryModel
    {
        $model = new ArticleCategoryModel();
        $category = $model->get(['categoryId' => $categoryId]);

        if (empty($category)) {
            throw new ServiceException('文章分类不存在');
        }
        return $category;
    }

    static function checkArticleByCategoryId($categoryId)
    {
        $model = new ArticleModel();
        $model->categoryId = $categoryId;
        $list = $model->getListByCategoryId();

        if (!empty($list)) {
            throw new ServiceException('该分类存在文章');
        }

        return $list;
    }

}