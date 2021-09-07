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
    //获取数据表结构对象
    $tableObjectGeneration = new \EasySwoole\ORM\Utility\TableObjectGeneration($connection, $tableName);
    $schemaInfo = $tableObjectGeneration->generationTable();

    $tablePre = '';//表前缀
    $path = "App\\Model";
    $extendClass = \App\Model\BaseModel::class;
    $modelConfig = new \EasySwoole\CodeGeneration\ModelGeneration\ModelConfig($schemaInfo, $tablePre, "{$path}", $extendClass);
    $modelGeneration = new \EasySwoole\CodeGeneration\ModelGeneration\ModelGeneration($modelConfig);
    $modelName = $modelGeneration->getPhpNamespace()->getName().'\\'.$modelGeneration->getClassName();
    $result = $modelGeneration->generate();
    var_dump($result);//生成成功返回生成文件路径,否则返回false

    $path = "App\\HttpController\\User";
    $extendClass = \App\HttpController\Api\User\UserBase::class;
    $modelClass = $modelName;
    $controllerConfig = new \EasySwoole\CodeGeneration\ControllerGeneration\ControllerConfig($modelClass, $schemaInfo, $tablePre, "{$path}", $extendClass);
    $controllerGeneration = new \EasySwoole\CodeGeneration\ControllerGeneration\ControllerGeneration($controllerConfig);
    $result = $controllerGeneration->generate();
    var_dump($result);

    $path = "App\\HttpController\\Admin";
    $extendClass = \App\HttpController\Api\Admin\AdminBase::class;
    $modelClass = $modelName;
    $controllerConfig = new \EasySwoole\CodeGeneration\ControllerGeneration\ControllerConfig($modelClass, $schemaInfo, $tablePre, "{$path}", $extendClass);
    $controllerGeneration = new \EasySwoole\CodeGeneration\ControllerGeneration\ControllerGeneration($controllerConfig);
    $result = $controllerGeneration->generate();
    var_dump($result);


    $path = "UnitTest\\Admin";
    $modelClass = \App\Model\UserModel::class;
    $controllerClass= \App\HttpController\User::class;
    $extendClass = \PHPUnit\Framework\TestCase::class;
    $tablePre = '';//表前缀
    $controllerConfig = new \EasySwoole\CodeGeneration\UnitTest\UnitTestConfig($modelClass, $controllerClass, $schemaInfo, $tablePre, "{$path}", $extendClass);
    $controllerConfig->setRootPath(EASYSWOOLE_ROOT);
    $unitTestGeneration = new \EasySwoole\CodeGeneration\UnitTest\UnitTestGeneration($controllerConfig);
    $result = $unitTestGeneration->generate();
    var_dump($result);


    \Swoole\Timer::clearAll();
    \EasySwoole\Component\Timer::getInstance()->clearAll();
});
