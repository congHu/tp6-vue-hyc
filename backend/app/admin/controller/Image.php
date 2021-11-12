<?php
namespace app\admin\controller;

use app\admin\middleware\AdminUserAuth;
use app\BaseController;
use app\model\Image as ModelImage;
use think\facade\Config;

class Image extends BaseController
{
    protected $middleware = [
        AdminUserAuth::class
    ];

    public function upload()
    {
        $files = request()->file();
        try {
            validate(['file'=>'fileExt:jpg,jpeg,png'])
                ->check($files);
            // $savename = [];
            $savename = \think\facade\Filesystem::disk('public')->putFile('', $files['file'], time());
            $savename = str_replace("\\", '/', $savename);
            $model = new ModelImage();
            $model->url = '/storage'.'/'.$savename;
            $model->save();
            return success($model);
        } catch (\Exception $e) {
            return error($e->getMessage());
        }
    }

    public function list()
    {
        $pageSize = $this->request->param('page_size') ?? 20;
        $result = ModelImage::order('create_time', 'desc')->paginate($pageSize);
        return success($result);
    }

    public function deleteMany()
    {
        $ids = $this->request->param('ids');

        if (empty($ids) || !is_array($ids)) {
            return error();
        }

        $list = ModelImage::whereIn('id', $ids)->select()->toArray();

        foreach ($list as $model) {
            try {
                unlink(app()->getRootPath() . 'public/' . $model['url']);
            } catch (\Exception $ex) {
                // return error($ex->getMessage());
            }
        }
        
        return success(ModelImage::destroy($ids));
    }
}