<?php

namespace app\model;

use app\model\Base;
use think\model\concern\SoftDelete;

class BannerImage extends Base
{
    use SoftDelete;
    protected $autoWriteTimestamp = true;

    public function banner()
    {
        return $this->belongsTo(Banner::class);
    }

    public static function upsert($params) {
        $model = new self();
        if (isset($params['id'])) {
            $model = self::find($params['id']);
            if ($model == null) {
                return null;
            }
        }

        $model->allowField([
            'banner_id',
            'image',
            'title',
            'url',
            'status',
            'sorting'
        ])->save($params);

        return $model;
    }

    public static function lists($params) {
        $query = self::order(['sorting'=>'desc','create_time'=>'desc']);
        $query = $query->with(['banner']);

        if (isset($params['banner_id'])) {
            $query = $query->where('banner_id', $params['banner_id']);
        }

        if (isset($params['status'])) {
            $query = $query->where('status', $params['status']);
        }

        if (isset($params['page_size']) && $params['page_size'] <= 0) {
            return $query->select();
        }

        return $query->paginate($params['page_size'] ?? 20);
    }
}
