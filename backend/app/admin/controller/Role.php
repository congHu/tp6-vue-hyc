<?php
namespace app\admin\controller;

use app\admin\middleware\AdminUserAuth;
use app\BaseController;
use app\model\AdminMenu;
use app\model\AdminUser;
use app\model\Role as ModelRole;

class Role extends BaseController
{
    protected $middleware = [
        AdminUserAuth::class
    ];

    public function save()
    {
        $params = $this->request->param();

        try {
            $this->validate($params, 'Role.save');
        } catch (\Exception $th) {
            return error($th->getMessage());
        }

        $model = ModelRole::upsert($params);
        if ($model == null) {
            if ($params['id'] == 1) {
                return error('不能编辑超级管理员权限');
            }
            return error('id不存在');
        }

        return success($model);
    }

    public function info()
    {
        $params = $this->request->param();

        try {
            $this->validate($params, 'Info.id');
        } catch (\Exception $th) {
            return error($th->getMessage());
        }

        $info = ModelRole::with(['menus'])->find($params['id']);
        return success($info);
    }

    /**
     * list
     */
    public function list()
    {
        $params = $this->request->param();

        $result = ModelRole::lists($params);

        return success($result);
    }

    public function delete() {
        $params = $this->request->param();

        try {
            $this->validate($params, 'Info.id');
        } catch (\Exception $th) {
            return error($th->getMessage());
        }

        $count = AdminUser::where('role_id', $params['id'])->count();
        if ($count > 0) {
            return error('无法删除，角色正在被使用');
        }

        $res = ModelRole::destroy($params['id']);
        if ($res) return success($res);
        else return error();
    }

    public function menu() {
        return success(AdminMenu::order('sorting', 'desc')->select());
    }
}