<?php
namespace app\admin\validate;

use think\Validate;

class Info extends Validate
{
    protected $rule = [
        'id' => 'require',
    ];

    protected $scene = [
        'id' => ['id'],
    ];
}