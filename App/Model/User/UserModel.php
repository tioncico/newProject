<?php


namespace App\Model\User;


use App\Model\Model;
use EasySwoole\Spl\SplBean;

/**
 * Class UserModel
 * @property $userId;
 * @property $userName;            // 用户名
 * @property $phone;               // 手机
 * @property $sex;                 // 性别 0 未知  1 男  2女
 * @property $userPassword;        // 密码
 * @property $userSession;         // 会话信息
 * @property $lastLoginIp;         // 最后一次登录ip
 * @property $lastLoginTime;       // 最后一次登录时间
 * @property $addTime;             // 用户添加时间
 * @property $isForbid;            // 用户状态 0 禁用 1 正常使用
 * @property $isDelete;            // 软删除状态 0 未删除 1 已删除
 * @package App\Model\User
 */
class UserModel extends Model
{
    protected $table = 'user_list';
    protected $primaryKey = 'userId';

    const FORBID_TYPE_NORMAL = 0;   // 正常使用
    const FORBID_TYPE_DISABLED = 1; // 禁用
    const DELETE_TYPE_NORMAL = 0;   // 未删除
    const DELETE_TYPE_DELETED = 1;  // 已删除


    /**
     * 获取用户列表
     * @param int $page                 页码
     * @param string|null $keyword      关键词
     * @param int $pageSize             每页条数
     * @return array
     * @throws \Throwable
     */
    function getAll(int $page = 1, string $keyword = null, int $pageSize = 10): array {
        $where = [];
        if (!empty($keyword)) {
            $where['userName'] = ['%' . $keyword . '%', 'like'];
        }
        $where['isDelete'] = self::DELETE_TYPE_NORMAL;
        $list = $this->limit($pageSize * ($page - 1), $pageSize)->order($this->schemaInfo()->getPkFiledName(), 'DESC')->withTotalCount()->all($where);
        $total = $this->lastQueryResult()->getTotalCount();
        return ['total' => $total, 'list' => $list];
    }

    /**
     * 通过手机号查找用户
     * @param $phone
     * @param string $field
     * @throws \Throwable
     */
    function getOneByPhone($field = '*'):?UserModel {
        $user = $this->where(['phone'=>$this->phone])->field($field)->get();
        return $user;
    }


    /**
     * 删除用户
     * @return bool|null
     * @throws \Throwable
     */
    function delete() {
        return $this->update(['isDelete' => self::DELETE_TYPE_DELETED]);
    }

    /**
     * 登录
     * @return UserBean|null
     * @throws \Throwable
     */
    function login():?UserModel {
        $user = $this
            ->where(['phone'=>$this->phone])
            ->where(['userPassword'=>$this->userPassword])
            ->get();
        return $user;
    }

    /**
     * 通过session获取用户
     * @return UserModel|null
     * @throws \Throwable
     */
    function getOneBySession():?UserModel {
        $user = $this
            ->where(['userSession'=>$this->userSession])
            ->get();

        return $user;
    }

    /**
     * 注销用户
     * @return mixed
     * @throws \Throwable
     */
    function logout() {
        return $this->update(['userSession'=> '']);
    }

}