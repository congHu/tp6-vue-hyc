<?php
namespace app\utils;

use Firebase\JWT\JWT;
use think\facade\Config;

class JwtToken
{
    /**
     * 生成jwt token
     * @param array $data
     * @return string $token
     */
    public static function getToken(array $data) : string
    {
        $type = Config::get('jwt.jwt_type');
        $key = Config::get('jwt.jwt_key');
        $expire = Config::get('jwt.expire', 3600 * 24 * 7);
        $token = [
            'data' => $data,
            'iss' => 'abc_coupon',
            'iat' => time(),
            'exp' => time() + $expire
        ];
        return JWT::encode($token, $key, $type);
    }

    /**
     * 解密jwt token
     * @param string $token
     * @return array|bool $data
     */
    public static function decodeToken(string $token) : array
    {
        $type = Config::get('jwt.jwt_type');
        $key = Config::get('jwt.jwt_key');
        $data = json_decode(json_encode(JWT::decode($token, $key, [$type])), true);
        return $data;
    }
}