<?php
include "./vendor/autoload.php";
include "./bootstrap.php";

\EasySwoole\EasySwoole\Core::getInstance()->initialize();
go(function (){
    $mysqlConfig = new \EasySwoole\ORM\Db\Config(\EasySwoole\EasySwoole\Config::getInstance()->getConf('MYSQL'));
    $connection = new \EasySwoole\ORM\Db\Connection($mysqlConfig);

    $tableName = 'payment_list';
    $tableObjectGeneration =  new \EasySwoole\ORM\Utility\TableObjectGeneration($connection, $tableName);
    $schemaInfo = $tableObjectGeneration->generationTable();

    $path = '\\Payment';
    $modelConfig = new \AutomaticGeneration\Config\ModelConfig();
    $modelConfig->setBaseNamespace("App\\Model" . $path);
    $modelConfig->setTable($schemaInfo);//传入上面的数据表数据
//    $modelConfig->setBaseDirectory(EASYSWOOLE_ROOT . '/' .\AutomaticGeneration\AppLogic::getAppPath() . 'Model');
    $modelConfig->setTablePre("");
    $modelConfig->setExtendClass(\App\Model\BaseModel::class);
    $modelConfig->setKeyword('');//生成该表getAll关键字
    $modelBuilder = new \AutomaticGeneration\ModelBuilder($modelConfig);
    $result = $modelBuilder->generateModel();
    var_dump($result);

    $path = '\\Api\\Admin\\Payment';
    $controllerConfig = new \AutomaticGeneration\Config\ControllerConfig();
    $controllerConfig->setBaseNamespace("App\\HttpController" . $path);
//    $controllerConfig->setBaseDirectory( EASYSWOOLE_ROOT . '/' . $automatic::APP_PATH . '/HttpController/Api/');
    $controllerConfig->setTablePre('');
    $controllerConfig->setTable($schemaInfo);//传入上面所说的数据表数据
    $controllerConfig->setExtendClass(\App\HttpController\Api\Admin\BaseController::class);
    $controllerConfig->setModelClass($modelBuilder->getClassName());
    $controllerBuilder = new \AutomaticGeneration\ControllerBuilder($controllerConfig);
    $result = $controllerBuilder->generateController();
    var_dump($result);


});