<?php
namespace EasySwoole\Oss\Tests\QiNiu;

use EasySwoole\Oss\QiNiu\Http\Client;

class HttpTest extends QiNiuBaseTestCase
{
    public function testGet()
    {
        $response = Client::get('http://baidu.com');
        $this->assertEquals($response->statusCode, 200);
        $this->assertNotNull($response->body);
        $this->assertEmpty($response->error);
    }

    public function testGetQiniu()
    {
        $response = Client::get('http://up.qiniu.com');
        $this->assertEquals(405, $response->statusCode);
        $this->assertNotNull($response->body);
        $this->assertNotNull($response->xReqId());
        $this->assertNotNull($response->xLog());
        $this->assertNotEmpty($response->error);
    }

    public function testPost()
    {
        $response = Client::post('http://baidu.com', null);
        $this->assertEquals($response->statusCode, 200);
        $this->assertNotNull($response->body);
        $this->assertNotEmpty($response->error);
    }

    public function testPostQiniu()
    {
        $response = Client::post('http://up.qiniu.com', null);
        $this->assertEquals($response->statusCode, 400);
        $this->assertNotNull($response->body);
        $this->assertNotNull($response->xReqId());
        $this->assertNotNull($response->xLog());
        $this->assertNotEmpty($response->error);
    }
}
