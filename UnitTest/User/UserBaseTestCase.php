<?php
/**
 * Created by PhpStorm.
 * User: evalor
 * Date: 2019-03-26
 * Time: 19:17
 */

namespace UnitTest\User;

use App\HttpController\Api\User\UserBase;
use App\Model\User\UserModel;
use Curl\Curl;
use UnitTest\BaseTest;

/**
 * API测试基类
 * Class BaseApiTestCase
 * @package Test\ApiTest
 */
class UserBaseTestCase extends BaseTest
{
    protected $apiBase = '/Api/';
    /**
     * @var $userBean UserModel
     */
    protected $userBean;
    protected $userSession;
    /**
     * @var Curl
     */
    protected $curl;
    protected $modelName = '';
    protected $userData = [
        'name'     => '单元测试用户',
        'account'  => 'unitTest',
        'password' => '123456',
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
        $model = new UserModel();
        $userInfo = $model->get(['account' => $userData['account']]);
        if ($userInfo) {
            $userData['password'] = $model::hashPassword($userData['password']);
            $userInfo->update($userData);
        } else {
            $userData['password'] = $model::hashPassword($userData['password']);
            $model = new UserModel($userData);
            $model->save();
            $userInfo = $model;
        }
        return $userInfo;
    }

    public function curlInit()
    {
        $this->curl->setCookies([
            UserBase::USER_TOKEN_NAME => $this->userSession,
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
        $response = $this->request('getInfo');
        $this->userBean = new UserModel((array)$response->result);
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
        $response = $this->request('login', $userData);
        $this->userSession = $response->result->session;
    }

    public function logout()
    {
        $this->request('logout');
    }


}
