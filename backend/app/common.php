<?php
const STATUS_CODE_SUCCESS=1; //状态码-正常
const STATUS_CODE_FAIL=-1; //状态码-错误
//
// 应用公共文件
//if (!function_exists('success')) {
//    function success($data = null, string $msg = 'success', int $code = 1)
//    {
//        if (is_object($data)) $data = $data->toArray();
//        return json(compact('data', 'code', 'msg'));
//    }
//}

if (!function_exists('error')) {
    function error(string $msg = 'error', int $code = 0)
    {
        return json(compact('code', 'msg'));
    }
}

if (!function_exists('password')) {
    function password($pw, $authCode = '')
    {
        if (empty($authCode)) {
            $authCode = env('database.authcode');
        }
        $result = md5(md5($authCode . $pw));
        return $result;
    }
}
if (!function_exists('validate_extra')) {

    /**
     * @param array $data
     * @param       $validate
     * @param array $message
     * @param bool  $batch
     *
     * @return bool
     */
    function validate_extra(array $data, $validate, array $message = [], bool $batch = false)
    {
        if (is_array($validate)) {
            $v = new \think\Validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                // 支持场景
                [$validate, $scene] = explode('.', $validate);
            }
            $class = false !== strpos($validate, '\\') ? $validate : app()->parseClass('validate', $validate);
            $v = new $class();
            if (!empty($scene)) $v->scene($scene);
        }

        $v->message($message);

        // 是否批量验证
        if ($batch) $v->batch(true);

        return $v->failException(true)->check($data);
    }
    /**
     * 获取系统配置，通用
     *
     * @param $key
     *
     * @return array|mixed
     */
    function get_option($key)
    {
        if (!is_string($key) || empty($key)) {
            return [];
        }

        static $cmfGetOption;

        if (empty($cmfGetOption)) {
            $cmfGetOption = [];
        } else {
            if (!empty($cmfGetOption[$key])) {
                return $cmfGetOption[$key];
            }
        }

        $optionValue = cache('cmf_options_' . $key);

        if (empty($optionValue)) {
            $optionValue = \think\facade\Db::name('option')->where('option_name', $key)->value('option_value');
            if (!empty($optionValue)) {
                $optionValue = json_decode($optionValue, true);

                \think\facade\Cache::tag('sys')->set('cmf_options_' . $key, $optionValue);
            }
        }

        $cmfGetOption[$key] = $optionValue;
        return $optionValue;
    }
    function get_upload_setting()
    {
        $uploadSetting = get_option('upload_setting');
        if (empty($uploadSetting) || empty($uploadSetting['file_types'])) {
            $uploadSetting = [
                'file_types' => [
                    'image' => [
                        'upload_max_filesize' => '10240', //单位KB
                        'extensions'          => 'jpg,jpeg,png,gif,bmp4'
                    ],
                    'video' => [
                        'upload_max_filesize' => '10240',
                        'extensions'          => 'mp4,avi,wmv,rm,rmvb,mkv'
                    ],
                    'audio' => [
                        'upload_max_filesize' => '10240',
                        'extensions'          => 'mp3,wma,wav'
                    ],
                    'file'  => [
                        'upload_max_filesize' => '10240',
                        'extensions'          => 'txt,pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar'
                    ]
                ],
                'chunk_size' => 512, //单位KB
                'max_files'  => 20 //最大同时上传文件数
            ];
        }
    }
}


/**
 * 获取应用名称
 */
if (!function_exists('getAppName')) {
    function getAppName(){
        return substr(substr(root_path(),0,strlen(root_path())-1), strrpos(substr(root_path(),0,strlen(root_path())-1),"\\",0)+1);
    }
}


/**
 * 统一返回格式
 * @param type $status
 * @param type $message
 * @param type $data
 * @return type
 */
if (!function_exists('responseResult')) {
    function responseResult($status=STATUS_CODE_SUCCESS,$result=true,$message='',$data=array()){
        $result =array(
            'code'=>$status,
            'result'=>$result,
            'msg'=>$message,
            'data'=>$data,
        );
        return $result;
    }
}

/*
* 正常时返回的信息
*/
if (!function_exists('success')) {
    function success($data='',$msg='success'){
        return json(responseResult(STATUS_CODE_SUCCESS, true, $msg, $data),200);
    }
}

/**
* 出错时返回的信息
* @param type $msg
* @return type
*/
if (!function_exists('fail')) {
    function fail($msg='系统未定义错误',$errorCode=STATUS_CODE_FAIL) {
       return json(responseResult($errorCode, false, $msg),200);
    }
}