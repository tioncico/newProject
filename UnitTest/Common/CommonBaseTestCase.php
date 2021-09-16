<?php
/**
 * Created by PhpStorm.
 * User: evalor
 * Date: 2019-03-26
 * Time: 19:17
 */

namespace UnitTest\Common;

use App\HttpController\Api\User\UserBase;
use App\Model\User\UserModel;
use Curl\Curl;
use UnitTest\BaseTest;

/**
 * API测试基类
 * Class BaseApiTestCase
 * @package Test\ApiTest
 */
class CommonBaseTestCase extends BaseTest
{
    protected $apiBase = '/Api/';
    /**
     * @var Curl
     */
    protected $curl;
    protected $modelName = '';


    function setUp(): void
    {
        parent::setUp();
        $this->curl = new Curl();
    }

    public function tearDown(): void
    {
        parent::tearDown(); //
    }

}
