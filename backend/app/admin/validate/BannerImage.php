<?php
namespace app\admin\validate;

use think\Validate;

class BannerImage extends Validate
{
    protected $rule = [
        'banner_id' => 'require',
        'image' => 'require'
    ];


    protected $scene = [
        'save' => ['banner_id','image']
    ];

}