<?php

namespace app\model;

use app\model\Base;
use think\model\concern\SoftDelete;

class User extends Base
{
    use SoftDelete;
    protected $autoWriteTimestamp = true;

    public static function list($params)
    {
        $query = self::order('create_time', 'desc');

        if (isset($params['nickname'])) {
            $query = $query->where('nickname', 'like', '%'.$params['nickname'].'%');
        }

        if (isset($params['phone'])) {
            $query = $query->where('phone', $params['phone']);
        }

        if (isset($params['page_size']) && $params['page_size'] <= 0) {
            $result = $query->select()->toArray();
            $iter = &$result;
        }else{
            $result = $query->paginate($params['page_size'] ?? 20)->toArray();
            $iter = &$result['data'];
        }

        return $result;
    }
}
