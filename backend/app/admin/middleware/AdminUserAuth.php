<?php
namespace app\admin\middleware;

use app\utils\JwtToken;
use app\Request;

/**
 * admin用户token中间件
 */
class AdminUserAuth
{
    public function handle(Request $request, \Closure $next)
    {
        $token = trim($request->header('Authorization'));

        try {
            $data = JwtToken::decodeToken($token);
            $header = $request->header();
            $header['admin'] = $data['data'];
            $request->withHeader($header);

            return $next($request);
        } catch (\Exception $ex) {
            return error('登录校验失败', 10001);
        }

        
    }
}