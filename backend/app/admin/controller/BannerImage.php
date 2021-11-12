<?php
namespace app\admin\controller;

use app\admin\middleware\AdminUserAuth;
use app\BaseController;
use app\model\BannerImage as ModelBannerImage;

/**
 * banner图
 */
class BannerImage extends BaseController
{
    protected $middleware = [
        AdminUserAuth::class
    ];
    
    public function save()
    {
        $params = $this->request->param();

        try {
            $this->validate($params, 'BannerImage.save');
        } catch (\Exception $th) {
            return error($th->getMessage());
        }
        
        $model = ModelBannerImage::upsert($params);
        if ($model == null) {
            return error('id不存在');
        }

        return success($model);
    }

    public function info()
    {
        $params = $this->request->param();

        try {
            $this->validate($params, 'Info.id');
        } catch (\Exception $th) {
            return error($th->getMessage());
        }

        $info = ModelBannerImage::find($params['id']);
        return success($info);
    }

    /**
     * list
     */
    public function list()
    {
        $params = $this->request->param();

        $result = ModelBannerImage::lists($params);

        return success($result);
    }

    /**
     * deleteMany
     */
    public function deleteMany()
    {
        $ids = $this->request->param('ids');

        if (empty($ids) || !is_array($ids)) {
            return error();
        }

        return success(ModelBannerImage::destroy($ids));
    }
}