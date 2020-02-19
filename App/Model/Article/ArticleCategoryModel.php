<?php

namespace App\Model\Article;

/**
 * Class ArticleCategoryModel
 * @property $categoryId;
 * @property $categoryName;
 * @property $pid;
 * @property $note;
 * Create With Automatic Generator
 */
class  ArticleCategoryModel extends \App\Model\Model
{
	protected $table = 'article_category_list';

	protected $primaryKey = 'categoryId';


	/**
	 * @getAll
	 * @keyword categoryName
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
            $where['categoryName'] = ['%' . $keyword . '%', 'like'];
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

    public function getPidAll(int $page = 1,  $pid = 0,string $keyword = null, int $pageSize = 10, string $field = '*') {
        $where = [];
        if (!empty($keyword)) {
            $where['categoryName'] = ['%' . $keyword . '%', 'like'];
        }
        $list = $this
            ->limit($pageSize * ($page - 1), $pageSize)
            ->where(['pid' => $pid])
            ->order('sort', 'DESC')
            ->order($this->schemaInfo()->getPkFiledName(), 'DESC')
            ->all(null, true);
        $total = $this->lastQueryResult()->getTotalCount();

        return ['total' => $total, 'list' => $list];
	}
}

