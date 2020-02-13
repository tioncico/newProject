<?php


namespace App\HttpController;


use EasySwoole\RedisPool\Redis;

class Index extends BaseController
{
    public function index()
    {
        $this->response()->redirect('/pc/index.html');
    }

    function admin()
    {
        $this->response()->redirect('/admin.html');
    }

    function chainOrganization()
    {
        $this->response()->redirect('/chainOrganization.html');
    }

    function shop()
    {
        $this->response()->redirect('/shop.html');
    }

    function maintenanceWorker()
    {
        $this->response()->redirect('/maintenanceWorker.html');
    }

}