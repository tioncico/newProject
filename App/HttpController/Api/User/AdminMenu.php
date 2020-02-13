<?php
/**
 * Created by PhpStorm.
 * User: yangzhenyu
 * Date: 2018/11/13
 * Time: 下午5:55
 */

namespace App\HttpController\Api\User;


class AdminMenu extends BaseController
{
    function index()
    {
        $icoList = [
            'home'   => 'layui-icon-home',

            'order'   => 'layui-icon-list',

        ];
        $menuList = [
            [
                'title' => "数据概览",
                "icon"  => $icoList['home'],
                "list"  => [
                    [ "title" => "统计中心", "jump" => "/" ],
                ]
            ],

            [
                'title' => "订单管理",
                "icon"  => $icoList['order'],
                "list"  => [
                    [ "title" => "订单管理列表", "jump" => "/order/list" ],
                ]
            ],

//

//            [
//                'title' => "用户管理",
//                "icon"  => $icoList['user'],
//                "list"  => [
//                    [ "title" => "用户列表", "jump" => "/users/list" ],
////                    [ "title" => "用户收货地址", "jump" => "/userAddress/list" ],
////                    [ "title" => "用户收藏表", "jump" => "/userGoodsCollection/list" ],
//
//                ]
//            ],
//            [
        ];
        $this->writeJson(200, $menuList);
    }


}