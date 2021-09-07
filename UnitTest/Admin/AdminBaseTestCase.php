<?php
/**
 * Created by PhpStorm.
 * User: evalor
 * Date: 2019-03-26
 * Time: 19:17
 */

namespace UnitTest\Admin;

use App\HttpController\Api\Admin\AdminBase;
use App\Model\Admin\AdminUserModel;
use Curl\Curl;
use UnitTest\BaseTest;

/**
 * API测试基类
 * Class BaseApiTestCase
 * @package Test\ApiTest
 */
class AdminBaseTestCase extends BaseTest
{
    protected $apiBase = '/Api/Admin';
    /**
     * @var $userBean AdminUserModel
     */
    protected $userBean;
    protected $userSession;
    /**
     * @var Curl
     */
    protected $curl;
    protected $modelName = '';
    protected $userData = [
        'adminName'     => '单元测试用户',
        'adminAccount'  => 'unitTest',
        'adminPassword' => '123456',
    ];


    function setUp(): void
    {
        parent::setUp();
        $this->curl = new Curl();
        $this->register();
        $this->login();
        $this->curlInit();
        $this->getUserInfo();
    }

    function register()
    {
        $userData = $this->userData;
        //判断会员是否存在
        $model = new AdminUserModel();
        $userInfo = $model->get(['adminAccount' => $userData['adminAccount']]);
        if ($userInfo) {
            $userData['adminPassword'] = $model::hashPassword($userData['adminPassword']);
            $userInfo->update($userData);
        } else {
            $userData['adminPassword'] = $model::hashPassword($userData['adminPassword']);
            $model = new AdminUserModel($userData);
            $model->save();
            $userInfo = $model;
        }
        return $userInfo;
    }

    public function curlInit()
    {
        $this->curl->setCookies([
            AdminBase::ADMIN_TOKEN_NAME => $this->userSession,
        ]);var_dump([
        AdminBase::ADMIN_TOKEN_NAME => $this->userSession,
    ]);
    }

    public function tearDown(): void
    {
        parent::tearDown(); // TODO: Change the autogenerated stub
//        $this->logout();
//        $this->delete();
    }

    public function getUserInfo()
    {
        $response = $this->request('getInfo',[],'Auth');
        $this->userBean = new AdminUserModel((array)$response->result);
    }

    public function login()
    {
        $curl = $this->curl;
        $time = time();
        $curl->setCookies([
            'verifyCodeHash' => md5('1234' . $time),
            'verifyCodeTime' => $time,
        ]);
        $userData = $this->userData;
        $response = $this->request('login', $userData,'Auth');
        $this->userSession = $response->result->adminSession;
    }

    public function logout()
    {
        $this->request('logout',[],'Auth');
    }

}
