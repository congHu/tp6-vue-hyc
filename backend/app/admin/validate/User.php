<?php
namespace app\admin\validate;

use app\model\AdminUser;
use think\Validate;

class User extends Validate
{
    protected $rule = [
        'username' => 'require|alphaNum|max:20|checkNum',
        'password' => 'alphaNum|max:20',
        'role_id' => 'integer',
        'status' => 'in:0,1',
    ];

    protected $scene = [
        'save' => ['username','password','status','role_id'],
    ];

    public function checkNum($value, $rule, $data) {
        $query = AdminUser::where('username', $value);
        if (isset($data['id'])) {
            $query->where('id', '<>', $data['id']);
        }
        return $query->find() == null ? true : '用户名已存在';
    }
}