<?php
namespace app\admin\validate;

use think\Validate;

class Login extends Validate
{
    protected $rule = [
        'username' => 'require',
        'password' => 'require',
    ];

    protected $message = [
        'username.require' => '用户不能为空',
        'password.require' => '密码不能为空',
    ];

    protected $scene = [
        'login' => ['username','password'],
    ];
}