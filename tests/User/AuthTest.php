<?php
/**
 * Created by PhpStorm.
 * User: Tioncico
 * Date: 2019/3/26 0026
 * Time: 16:28
 */

namespace Test\User;

use Curl\Curl;
use EasySwoole\EasySwoole\Config;

class AuthTest extends UserBaseTestCase
{
    protected $modelName = 'Auth';

    public function testGetInfo()
    {
        $url = $this->apiBase . '/' . $this->modelName . '/getInfo';
        $curl = $this->curl;
        $data = [
        ];
        $curl->get($url, $data);
        if ($curl->response) {
//            var_dump($curl->response);
        } else {
            echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
        }
        $this->assertTrue(!!$curl->response);
        $this->assertEquals(200, $curl->response->code);
    }
}
