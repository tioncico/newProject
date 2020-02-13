<?php


namespace App\HttpController\Api\User;

use App\HttpController\Api\BaseController as Controller;
use App\Model\User\UserBean;
use App\Model\User\UserModel;
use EasySwoole\MysqliPool\Mysql;
use EasySwoole\Http\Message\Status;

class BaseController extends Controller
{

    public $skipAuthMethod = ['login', 'register'];

    /**
     * @var UserModel
     */
    protected $who;
    protected $sessionKey = 'userSession';

    protected function onRequest(?string $action): ?bool
    {
        if (parent::onRequest($action) === false) {
            return false;
        }
        if (!in_array($action, $this->skipAuthMethod) && !$this->who()) {
            $this->writeJson(Status::CODE_UNAUTHORIZED);
            return false;
        }
        return true;
    }

    protected function who()
    {
        if (!$this->who) {
            /*
             * 执行session检查
             */
            $session = $this->request()->getRequestParam($this->sessionKey);
            if (empty($sessionKey)) {
                $session = $this->request()->getCookieParams($this->sessionKey);
            }
            if (empty($session)) {
                return null;
            }

            // 通过session查找用户
            $model = new UserModel();
            $model->userSession = $session;
            $who = $model->getOneBySession();

            // 判断用户状态
            if (!$who||$who->isDelete == $model::DELETE_TYPE_DELETED||$who->isForbid==$model::FORBID_TYPE_DISABLED) {
                return null;
            } else {
                $this->who = $who;
                return $who;
            }
        }
        return $this->who;
    }
}