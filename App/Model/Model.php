<?php


namespace App\Model;


use EasySwoole\ORM\AbstractModel;

abstract class Model extends AbstractModel
{
    protected $table;
    public function __construct(array $data = [])
    {
        $this->tableName = $this->table??$this->tableName;
        parent::__construct($data);
    }

}