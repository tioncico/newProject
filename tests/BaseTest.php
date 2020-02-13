<?php


namespace Test;


use EasySwoole\MysqliPool\Mysql;
use EasySwoole\EasySwoole\Core;
use PHPUnit\Framework\TestCase;

class BaseTest extends TestCase
{
    static $isInit=0;
    /**
     * 准备测试基境
     * @return void
     */
    function setUp(): void
    {
        if (self::$isInit==0){
            require_once dirname(__FILE__, 2) . '/vendor/autoload.php';
            defined('EASYSWOOLE_ROOT') or define('EASYSWOOLE_ROOT', dirname(__FILE__, 2));
            require_once dirname(__FILE__, 2) . '/EasySwooleEvent.php';
            Core::getInstance()->initialize();
            self::$isInit=1;
        }
    }
}