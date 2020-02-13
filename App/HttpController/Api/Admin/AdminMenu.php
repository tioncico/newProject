<?php
/**
 * Created by PhpStorm.
 * User: yangzhenyu
 * Date: 2018/11/13
 * Time: 下午5:55
 */

namespace App\HttpController\Api\Admin;


class AdminMenu extends BaseController
{
    function index()
    {
        $icoList = [
            'home'   => 'layui-icon-home',
            'chain'   => 'layui-icon-template',
            'shop'   => 'layui-icon-app',
            'maintenanceWorker'   => 'layui-icon-set-fill',
            'drug'   => 'layui-icon-auz',
            'users'   => 'layui-icon-user',
            'repository'   => 'layui-icon-diamond',
            'cabinetType'   => 'layui-icon-template-1',
            'customer'   => 'layui-icon-senior',
//            'cabinetBanner'   => 'layui-icon-carousel',
            'order'   => 'layui-icon-list',
            'set'    => 'layui-icon-set',
            'drugGuide'    => 'layui-icon-file-b',
            'pc'    => 'layui-icon-website',
            'article'    => 'layui-icon-read',


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
                'title' => "连锁机构",
                "icon"  => $icoList['chain'],
                "list"  => [
                    [ "title" => "连锁机构列表", "jump" => "/chain/chainList" ],
                ]
            ],

//            [
//                'title' => "门店管理列表",
//                "icon"  => $icoList['anz'],
//                "list"  => [
//                    [ "title" => "门店列表", "jump" => "/stores/StaffManagementList" ],
//                ]
//            ],
            [
                'title' => "门店管理",
                "icon"  => $icoList['shop'],
                "list"  => [
                    [ "title" => "门店管理列表", "jump" => "/shop/shopList" ],
                    [ "title" => "店铺申请表", "jump" => "/shop/shopApply" ],
                    [ "title" => "门店结算表", "jump" => "/shopSettlement/shopSettlementList" ],

                ]
            ],
            [
                'title' => "维修员",
                "icon"  => $icoList['maintenanceWorker'],
                "list"  => [
                    [ "title" => "维修员列表", "jump" => "/maintenanceWorker/list" ],
                    [ "title" => "维修员工单", "jump" => "/maintenanceOrder/maintenanceOrder" ],
                ]
            ],
            [
                'title' => "用户管理",
                "icon"  => $icoList['users'],
                "list"  => [
                    [ "title" => "用户列表", "jump" => "/users/list" ],
//                    [ "title" => "用户收货地址", "jump" => "/userAddress/list" ],
//                    [ "title" => "用户收藏表", "jump" => "/userGoodsCollection/list" ],

                ]
            ],
            [
                'title' => "订单管理",
                "icon"  => $icoList['order'],
                "list"  => [
                    [ "title" => "订单列表", "jump" => "/order/orderList" ],

                ]
            ],
            [
                'title' => "柜子管理",
                "icon"  => $icoList['cabinetType'],
                "list"  => [
                    [ "title" => "柜子列表", "jump" => "/cabinetType/cabinetList" ],
                    [ "title" => "柜子类型表", "jump" => "/cabinetType/cabinetType" ],
                    [ "title" => "柜子分配列表", "jump" => "/cabinetAssignShop/cabinetAssignShop" ],
                ]
            ],
            [
                'title' => "药品管理",
                "icon"  => $icoList['drug'],
                "list"  => [
                    [ "title" => "药品列表", "jump" => "/drug/drug" ],
                    [ "title" => "药品分类", "jump" => "/drug/drugCategory" ],
                ]
            ],
            [
                'title' => "药品导购",
                "icon"  => $icoList['drugGuide'],
                "list"  => [
                    [ "title" => "疾病列表", "jump" => "/drugGuideDisease/drugGuideDiseaseList" ],
                    [ "title" => "疾病症状列表", "jump" => "/drugGuideDiseaseSymptom/drugGuideDiseaseSymptomList" ],
                    [ "title" => "症状药品组合方案列表", "jump" => "/drugGuideSymptomDrugGroup/drugGuideSymptomDrugGroupList" ],
                    [ "title" => "症状药品列表", "jump" => "/drugGuideSymptomDrug/drugGuideSymptomDrugList" ],

                ]
            ],
            [
                'title' => "仓库管理",
                "icon"  => $icoList['repository'],
                "list"  => [
                    [ "title" => "仓库列表", "jump" => "/repository/repository" ],
                    [ "title" => "仓库操作类型表", "jump" => "/repository/repositoryActionType" ],
                    [ "title" => "柜子操作仓库记录表", "jump" => "/repository/cabinetActionRepository" ],
                ]
            ],
            [
                'title' => "客户管理",
                "icon"  => $icoList['customer'],
                "list"  => [
                    [ "title" => "客户列表", "jump" => "/customer/customer" ],

                ]
            ],

            [
                'title' => "电脑端管理",
                "icon"  => $icoList['pc'],
                "list"  => [
                    [ "title" => "电脑端管理列表", "jump" => "/pc/pcList" ],
                    [ "title" => "电脑端配置列表", "jump" => "/pcSetting/pcSettingList" ],
                    [ "title" => "留言列表", "jump" => "/pcComment/pcCommentList" ],
                ]
            ],

            [
                'title' => "文章管理",
                "icon"  => $icoList['article'],
                "list"  => [
                    [ "title" => "文章列表", "jump" => "/article/articleList" ],
                    [ "title" => "文章分类列表", "jump" => "/articleCategory/articleCategoryList" ],
                ]
            ],
            [
                'title' => "系统设置",
                "icon"  => $icoList['set'],
                "list"  => [
                    [ "title" => "后台用户", "jump" => "/admin/list" ],
                    [ "title" => "柜子广告表", "jump" => "/cabinetBannerList/cabinetBannerList" ],
                    [ "title" => "柜子广告分组表", "jump" => "/cabinetBannerGroupList/cabinetBannerGroupList" ],
                    [ "title" => "柜子关联表", "jump" => "/cabinetBannerGroupRelateList/cabinetBannerGroupRelateList" ],

                ]
            ],
        ];
        $this->writeJson(200, $menuList);
    }


}