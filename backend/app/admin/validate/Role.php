<?php
namespace app\admin\validate;

use app\model\Role as ModelRole;
use think\Validate;

class Role extends Validate
{
    protected $rule = [
        'name' => 'require|checkNum',
        'menu_ids' => 'array'
    ];

    protected $scene = [
        'save' => ['name','menu_ids'],
    ];

    public function checkNum($value, $rule, $data) {
        $query = ModelRole::where('name', $value);
        if (isset($data['id'])) {
            $query->where('id', '<>', $data['id']);
        }
        return $query->find() == null ? true : '角色名已存在';
    }
}