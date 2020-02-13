<?php


namespace App\HttpController\Api\Admin;
use App\HttpController\Api\BaseController as Controller;
use App\Model\Admin\AdminUserModel;
use EasySwoole\Http\Message\Status;

class BaseController extends Controller
{
    public $skipAuthMethod = ['login'];

    /**
     * @var AdminUserModel
     */
    public $who;

    protected $sessionKey = 'adminSession';


    protected function onRequest(?string $action): ?bool
    {
        if(parent::onRequest($action) === false){
            return false;
        }
        if(!in_array($action,$this->skipAuthMethod) && !$this->who()){
            return false;
        }
        return true;
    }

    protected function who():?AdminUserModel
    {
        if(!$this->who){
            /*
             * 执行session检查
             */

            // 获取session信息
            $session = $this->request()->getRequestParam($this->sessionKey);
            if (empty($session)) {
                $session = $this->request()->getCookieParams($this->sessionKey);
            }
            if (empty($session)) {
                $this->writeJson(Status::CODE_UNAUTHORIZED, null, '请先登录');
                return null;
            }

            // 通过session查找用户
            $model = new AdminUserModel();
            $model->adminSession = $session;
            $who = $model->getOneBySession();

            // 判断用户状态

            if (!$who) {
                $this->writeJson(Status::CODE_UNAUTHORIZED, null, '请先登录');
                return null;
            }

            if ($who->isDelete === AdminUserModel::DELETE_TYPE_DELETED) {
                $this->writeJson(Status::CODE_BAD_REQUEST, null, '帐号不存在');
                return null;
            }

            if ($who->isForbid === AdminUserModel::FORBID_TYPE_DISABLED) {
                $this->writeJson(Status::CODE_BAD_REQUEST, null, '该用户禁止登录');
                return null;
            }

            $this->who = $who;
            return $who;
        }
        return $this->who;
    }


}