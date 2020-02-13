<?php


namespace App\HttpController\Api;

use App\HttpController\BaseController as Controller;
use App\Service\ServiceException;
use EasySwoole\EasySwoole\Core;
use EasySwoole\EasySwoole\Trigger;
use EasySwoole\Http\Exception\ParamAnnotationValidateError;
use LogicAssert\LogicAssertException;

class BaseController extends Controller
{
    protected function onException(\Throwable $throwable): void
    {
        if ($throwable instanceof ParamAnnotationValidateError) {
            $msg = $throwable->getValidate()->getError()->getErrorRuleMsg();
            $this->writeJson(400, null, "{$msg}");
        } elseif ($throwable instanceof ServiceException) {
            $this->writeJson(400, null, $throwable->getMessage());
        } elseif ($throwable instanceof LogicAssertException) {
            $this->writeJson(400, null, $throwable->getMessage());
        } else {
            if (Core::getInstance()->isDev()) {
                $this->writeJson(500, null, $throwable->getMessage());
            } else {
                Trigger::getInstance()->throwable($throwable);
                $this->writeJson(500, null, '系统内部错误，请稍后重试');
            }
        }
    }
}