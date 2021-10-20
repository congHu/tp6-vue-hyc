<?php
namespace app\api\validate;

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