<?php


namespace App\Model\Admin;


use App\Model\Model;

/**
 * Class AdminUserModel
 * @property $adminId;
 * @property $adminName;           // 用户名
 * @property $adminAccount;        // 账号
 * @property $adminPassword;       // 密码
 * @property $adminSession;        // 会话信息
 * @property $lastLoginIp;         // 最后一次登录ip
 * @property $lastLoginTime;       // 最后一次登录时间
 * @property $addTime;             // 添加时间
 * @property $isForbid;            // 是否禁用 0 正常使用 1 禁用
 * @property $isDelete;            // 软删除状态 0 未删除 1 已删除
 * @package App\Model\Admin
 */
class AdminUserModel extends Model
{
    protected $table = 'admin_list';
    protected $primaryKey = 'adminId';

    const FORBID_TYPE_NORMAL = 0;   // 正常使用
    const FORBID_TYPE_DISABLED = 1; // 禁用
    const DELETE_TYPE_NORMAL = 0;   // 未删除
    const DELETE_TYPE_DELETED = 1;  // 已删除

    /**
     * 获取用户列表
     * @param int         $page 页码
     * @param string|null $keyword 关键词
     * @param int         $pageSize 每页条数
     * @return array
     * @throws \EasySwoole\Mysqli\Exceptions\ConnectFail
     * @throws \EasySwoole\Mysqli\Exceptions\Option
     * @throws \EasySwoole\Mysqli\Exceptions\OrderByFail
     * @throws \EasySwoole\Mysqli\Exceptions\PrepareQueryFail
     * @throws \Throwable
     */
    function getAll(int $page = 1, string $keyword = null, int $pageSize = 10): array
    {
        $where = [];
        if (!empty($keyword)) {
            $where['adminName'] = ['%' . $keyword . '%', 'like'];
        }
        $where['isDelete'] = self::DELETE_TYPE_NORMAL;
        $list = $this->limit($pageSize * ($page - 1), $pageSize)->order($this->schemaInfo()->getPkFiledName(), 'DESC')->withTotalCount()->all($where);
        $total = $this->lastQueryResult()->getTotalCount();
        return ['total' => $total, 'list' => $list];
    }

    /**
     * 软删除用户
     * @return bool|null
     * @throws \Throwable
     */
    function delete()
    {
        return $this->where([$this->primaryKey => $this->adminId])->update(['isDelete' => self::DELETE_TYPE_DELETED]);
    }

    /**
     * 登录
     * @throws \Throwable
     */
    function login(): ?AdminUserModel
    {
        $adminUser = $this
            ->where(['adminAccount' => $this->adminAccount])
            ->where(['adminPassword' => $this->adminPassword])
            ->get();

        return $adminUser;
    }

    /**
     * 通过session获取用户
     * @throws \Throwable
     */
    function getOneBySession(): ?AdminUserModel
    {
        $adminUser = $this
            ->where(['adminSession' => $this->adminSession])
            ->get();

        return $adminUser;
    }

    /**
     * 通过account获取用户
     * @throws \Throwable
     */
    function getOneByAccount(): ?AdminUserModel
    {
        $adminUser = $this
            ->where(['adminAccount' => $this->adminAccount])
            ->get();
        return $adminUser;
    }

    /**
     * 注销用户
     * @return mixed
     * @throws \Throwable
     */
    function logout()
    {
        return $this->update(['adminSession' => '']);
    }

}