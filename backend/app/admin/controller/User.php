<?php
namespace app\admin\controller;

use app\admin\middleware\AdminUserAuth;
use app\BaseController;
use app\model\AdminUser;
use app\utils\JwtToken;

/**
 * admin后台用户
 */
class User extends BaseController
{
    protected $middleware = [
        AdminUserAuth::class => ['except' => ['login']]
    ];
    
    /**
     * admin后台用户登陆
     */
    public function login()
    {
        $params = $this->request->only(['username', 'password']);
        try {
            $this->validate($params, 'Login.login');
        } catch (\Exception $th) {
            return error($th->getMessage());
        }
        $user = AdminUser::where('username', $params['username'])->findOrEmpty();
        if ($user->isEmpty()) {
            return fail('账号不存在');
        } else {
            if ($user->password !== password($params['password'])) {
                return fail('账号或密码错误');
            }
        }
        if ($user->status == 0) {
            return error('账号被禁止登陆');
        }
        
        $token = JwtToken::getToken([
            'id' => $user->id,
            'username' => $user->username
        ]);

        $user->token = $token;
        $user->save();

        if ($token) {
            return success($token, '登录成功');
        } else {
            return fail('登录失败');
        }
    }

    public function profile() 
    {
        $id = request()->header('admin')['id'];
        $user = AdminUser::with(['role'=>['menus'=>function($q){
            $q->order('sorting','desc');
        }]])->hidden(['password','token'])->find($id);
        if ($user == null) {
            return error();
        }
        return success($user);
    }

    public function save()
    {
        $params = $this->request->param();

        try {
            $this->validate($params, 'User.save');
        } catch (\Exception $th) {
            return error($th->getMessage());
        }

        $model = AdminUser::upsert($params);
        if ($model == null) {
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

        $info = AdminUser::find($params['id']);
        return success($info);
    }

    /**
     * list
     */
    public function list()
    {
        $params = $this->request->param();

        $result = AdminUser::lists($params);

        return success($result);
    }

    public function delete() {
        $params = $this->request->param();

        try {
            $this->validate($params, 'Info.id');
        } catch (\Exception $th) {
            return error($th->getMessage());
        }

        if ($params['id'] == request()->header('admin')['id']) {
            return error('不能删除自己');
        }

        $user = AdminUser::find($params['id']);
        if ($user == null) {
            return error();
        }
        if ($user->role_id == 1) {
            $adminCount = AdminUser::where('role_id', 1)->count();
            if ($adminCount <= 1) {
                return error('不能删除唯一的超级管理员');
            }
        }
        
        $res = $user->delete();
        if ($res) return success($res);
        else return error();
    }
}