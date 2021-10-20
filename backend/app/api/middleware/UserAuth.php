<?php
namespace app\api\middleware;

use app\utils\JwtToken;
use app\Request;

/**
 * 小程序用户token中间件
 */
class UserAuth
{
    public function handle(Request $request, \Closure $next)
    {
        $token = trim($request->header('Authorization'));

        try {
            $data = JwtToken::decodeToken($token);
            $header = $request->header();
            $header['user'] = $data['data'];
            $request->withHeader($header);

            return $next($request);
        } catch (\Exception $ex) {
            return error('登录校验失败', 10001);
        }

    }
}