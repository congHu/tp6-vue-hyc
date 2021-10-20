<?php
namespace app\api\controller;

use app\BaseController;
use app\model\BannerImage as ModelBannerImage;

/**
 * 客户端获取banner列表
 */
class BannerImage extends BaseController
{
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

        $params['status'] = 1;
        $result = ModelBannerImage::lists($params);

        return success($result);
    }

}