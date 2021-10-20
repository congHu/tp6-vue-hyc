<?php

namespace app\model;

use app\model\Base;
use think\model\concern\SoftDelete;

class AdminUser extends Base
{
    use SoftDelete;
    protected $autoWriteTimestamp = true;

    public function role() {
        return $this->belongsTo(Role::class);
    }

    public static function upsert($params) {
        $model = new self();
        if (isset($params['id'])) {
            $model = self::find($params['id']);
            if ($model == null) {
                return null;
            }
            if ($model->role_id == 1) {
                $params['status'] = 1;
            }
        }

        if (!empty($params['password'])) {
            $params['password'] = password($params['password']);
        }else{
            if (!isset($params['id'])) {
                $params['password'] = password('123456');
            }else{
                unset($params['password']);
            }
        }

        $model->allowField([
            'username',
            'nickname',
            'password',
            'role_id',
            'status',
        ])->save($params);

        return $model;
    }

    public static function lists($params)
    {
        $query = self::with(['role'])->order('create_time', 'desc');

        if (!empty($params['nickname'])) {
            $query = $query->where('nickname', 'like', '%'.$params['nickname'].'%');
        }

        if (!empty($params['role_id'])) {
            $query = $query->where('role_id', $params['role_id']);
        }

        if (!empty($params['status'])) {
            $query = $query->where('status', $params['status']);
        }

        return $query->paginate($params['page_size'] ?? 20);
    }
}
