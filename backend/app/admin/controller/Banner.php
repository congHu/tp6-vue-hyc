<?php
namespace app\admin\controller;

use app\admin\middleware\AdminUserAuth;
use app\BaseController;
use app\model\Banner as ModelBanner;

/**
 * banner类，表示是出现在何处的banner
 */
class Banner extends BaseController
{
    protected $middleware = [
        AdminUserAuth::class
    ];

    /**
     * list
     */
    public function list()
    {
        $result = ModelBanner::select();
        return success($result);
    }
}