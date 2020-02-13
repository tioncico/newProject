<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 19-9-10
 * Time: 下午1:47
 */

namespace App\Service\Admin;


use App\Model\User\UserModel;
use App\Service\ServiceException;

class UserService extends AdminBaseService
{

    static function checkUser($userId):UserModel
    {
        $model = new UserModel();
        $user = $model->get(['userId' => $userId]);

        if (empty($user)) {
            throw new ServiceException('用户不存在');
        }

        return $user;
    }

}