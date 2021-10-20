<?php
namespace app\admin\controller;

use app\admin\middleware\AdminUserAuth;
use app\BaseController;
use app\model\User;

class Member extends BaseController
{
    protected $middleware = [
        AdminUserAuth::class
    ];

    /**
     * list 用户列表
     */
    public function list()
    {
        $params = $this->request->param();
        
        $result = User::list($params);

        return success($result);
    }

}