<?php
namespace app\admin\validate;

use think\Validate;

class Member extends Validate
{
    protected $rule = [
        'user_id' => 'require|integer',
        'is_delivery' => 'require|in:0,1',
    ];

    protected $scene = [
        'edit' => ['user_id','is_delivery'],
    ];
}