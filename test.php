<?php

/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 2020-05-20
 * Time: 10:26
 */
include "./vendor/autoload.php";
\EasySwoole\EasySwoole\Core::getInstance()->initialize();

go(function () {
    //生成基础类
    $generation = new \EasySwoole\CodeGeneration\InitBaseClass\UnitTest\UnitTestGeneration();
    $mysqlConfig = new \EasySwoole\ORM\Db\Config(\EasySwoole\EasySwoole\Config::getInstance()->getConf('MYSQL'));

    //获取连接
    $connection = new \EasySwoole\ORM\Db\Connection($mysqlConfig);
    $tableName = 'admin_user_list';

    $codeGeneration = new EasySwoole\CodeGeneration\CodeGeneration($tableName, $connection);
    //生成model
    $codeGeneration->generationModel("\\Admin", '', \App\Model\BaseModel::class);

    //生成controller
    $codeGeneration->generationController("\\Api\\User", null, '', \App\HttpController\Api\User\UserBase::class);
    //生成unitTest
    $codeGeneration->generationUnitTest("\\User", null,null,'',\UnitTest\User\UserBaseTestCase::class);

    //生成controller
    $codeGeneration->generationController("\\Api\\Admin", null, '', \App\HttpController\Api\Admin\AdminBase::class);
    //生成unitTest
    $codeGeneration->generationUnitTest("\\Admin", null,null,'',\UnitTest\Admin\AdminBaseTestCase::class);

    \EasySwoole\Component\Timer::getInstance()->clearAll();
});
