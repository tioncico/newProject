<?php
/**
 * Created by PhpStorm.
 * User: Tioncico
 * Date: 2019/11/5 0005
 * Time: 13:46
 */

\EasySwoole\EasySwoole\Command\CommandContainer::getInstance()->set(new \AutomaticGeneration\Generation());

//获得原先的config配置项,加载到新的配置项中
\EasySwoole\EasySwoole\Config::getInstance(new \EasySwoole\Config\SplArrayConfig());