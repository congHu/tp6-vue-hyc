<?php
namespace app\api\controller;

use app\api\middleware\UserAuth;
use app\BaseController;
use app\model\User as ModelUser;
use app\utils\JwtToken;
use think\facade\Config;
use EasyWeChat\Factory;

/**
 * 小程序用户
 */
class User extends BaseController
{
    protected $middleware = [
        UserAuth::class => ['except' => ['mplogin']]
    ];
    
    /**
     * 小程序用户登陆
     */
    public function mplogin()
    {
        $params = $this->request->param();

        try {
            $this->validate($params, 'User.mplogin');
        } catch (\Exception $th) {
            return error($th->getMessage());
        }

        $config = Config::get('wx.mp');
        $app = Factory::miniProgram($config);
        $session = $app->auth->session($params['code']);
        $openid = $session['openid'];

        $user = ModelUser::where('openid', $openid)->find();
        if ($user == null) {
            $user = new ModelUser();
        }
        $user->openid = $openid;

        $decrypted = $app->encryptor->decryptData($session['session_key'], $params['iv'], $params['encrypted_data']);

        $user->phone = $decrypted['phoneNumber'] ?? $user->phone;
        $user->save();

        $token = JwtToken::getToken([
            'id' => $user->id,
            'openid' => $user->openid
        ]);

        if ($token) {
            $user->token = $token;
            return success($user, '登录成功');
        } else {
            return fail('登录失败');
        }
    }

    public function profile() 
    {
        $id = request()->header('user')['id'];
        $user = ModelUser::field([
            'nickname',
            'openid',
            'avatar',
            'phone',
            'create_time',
            'update_time'
        ])->find($id);
        if ($user == null) {
            return error('登录校验失败', 10001);
        }
        return success($user);
    }

    public function mpUserInfo() {
        $params = $this->request->param();

        $id = request()->header('user')['id'];
        $user = ModelUser::field([
            'nickname',
            'openid',
            'avatar',
            'phone',
            'create_time',
            'update_time'
        ])->find($id);
        if ($user == null) {
            return error('登录校验失败', 10001);
        }

        $user->nickname = $params['nickname'] ?? $user->nickname;
        $user->avatar = $params['avatar'] ?? $user->avatar;
        $user->save();
        return success($user);
    }
}