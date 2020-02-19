<?php

namespace App\Model\Article;

use App\Model\Model;

/**
 * Class ArticleModel
 * @property $articleId;
 * @property $categoryId;
 * @property $categoryName;
 * @property $title;
 * @property $englishTitle;
 * @property $imgUrl;
 * @property $description;
 * @property $author;
 * @property $content;
 * @property $addTime;
 * @property $updateTime;
 * @property $state;
 * @property $note;
 * Create With Automatic Generator
 */
class ArticleModel extends Model
{
	protected $table = 'article_list';

	protected $primaryKey = 'articleId';

	/**
	 * @getAll
	 * @keyword title
	 * @param  int  $page  1
	 * @param  string  $keyword
	 * @param  int  $pageSize  10
	 * @param  string  $field  *
	 * @return array[total,list]
	 */
	public function getAll(int $page = 1, string $keyword = null, int $pageSize = 10, string $field = '*'): array
	{
        $where = [];
        if (!empty($keyword)) {
            $where['title'] = ['%' . $keyword . '%', 'like'];
        }
        $list = $this
            ->limit($pageSize * ($page - 1), $pageSize)
            ->order($this->schemaInfo()->getPkFiledName(), 'DESC')
            ->field($field)
            ->withTotalCount()
            ->all($where);
        $total = $this->lastQueryResult()->getTotalCount();
        return ['total' => $total, 'list' => $list];
	}

	public function getListByCategoryId() {
        return $this->where(['categoryId'=>$this->categoryId])->all();
    }

    public function getAllByCategoryId(int $page = 1,  $categoryId ,string $keyword = null, int $pageSize = 10, string $field = '*') {
        $where = [];
        if (!empty($keyword)) {
            $where['categoryName'] = ['%' . $keyword . '%', 'like'];
        }
        $list = $this
            ->limit($pageSize * ($page - 1), $pageSize)
            ->where(['categoryId' => $categoryId])
            ->field($field)
            ->order($this->schemaInfo()->getPkFiledName(), 'DESC')
            ->all();
        $total = $this->lastQueryResult()->getTotalCount();

        return ['total' => $total, 'list' => $list];
    }
    public function getListByArticleId(int $page = 1,  $articleId ,string $keyword = null, int $pageSize = 10, string $field = '*') {
        $where = [];
        if (!empty($keyword)) {
            $where['categoryName'] = ['%' . $keyword . '%', 'like'];
        }
        $list = $this
            ->limit($pageSize * ($page - 1), $pageSize)
            ->where(['articleId' => $articleId])
            ->order('sort', 'DESC')
            ->order($this->schemaInfo()->getPkFiledName(), 'DESC')
            ->all();
        $total = $this->lastQueryResult()->getTotalCount();

        return ['total' => $total, 'list' => $list];
    }
}

