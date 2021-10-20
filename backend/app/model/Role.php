<?php

namespace app\model;

use app\model\Base;
use think\facade\Db;
use think\model\concern\SoftDelete;

class Role extends Base
{
    use SoftDelete;
    protected $autoWriteTimestamp = true;

    public function menus() {
        return $this->belongsToMany(AdminMenu::class, RoleMenu::class, 'menu_id');
    }

    public static function upsert($params) {
        $model = new self();
        if (isset($params['id'])) {
            if ($params['id'] == 1) {
                return null;
            }
            $model = self::find($params['id']);
            if ($model == null) {
                return null;
            }
        }

        $model->allowField([
            'name',
        ])->save($params);

        if (isset($params['menu_ids']) && is_array($params['menu_ids'])) {
            Db::startTrans();
            $role_id = $model->id;
            try {
                RoleMenu::where('role_id', $role_id)->delete();
                $menus = AdminMenu::whereIn('id', $params['menu_ids'])->select();
                $role_menu = $menus->map(function ($item) use ($role_id) {
                    return ['menu_id' => $item['id'], 'role_id' => $role_id];
                });
                (new RoleMenu)->saveAll($role_menu);
                Db::commit();
            }catch(\Exception $ex) {
                Db::rollback();
                return null;
            }
        }

        return $model;
    }

    public static function lists($params)
    {
        $query = self::order('create_time', 'desc');

        if (!empty($params['name'])) {
            $query = $query->where('name', 'like', '%'.$params['name'].'%');
        }

        // $query = $query->where('id', '<>', 1);

        if (isset($params['page_size']) && $params['page_size'] <= 0) {
            return $query->select();
        }
        return $query->paginate($params['page_size'] ?? 20);

    }
}
