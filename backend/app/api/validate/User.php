<?php
namespace app\api\validate;

use think\Validate;

class User extends Validate
{
    protected $rule = [
        'code' => 'require',
        'encrypted_data' => 'require',
        'iv' => 'require',
    ];

    protected $scene = [
        'mplogin' => ['code','encrypted_data','iv'],
    ];
}