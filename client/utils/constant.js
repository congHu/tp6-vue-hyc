const debug = true; // 是否调试

// 请求地址
export const REQ_API_HOST = debug ? 'http://localhost:8000' : '';

// 图片地址
export const IMG_HOST = REQ_API_HOST + '/storage/'

// 返回响应码，非1的响应码都是错误码
export const RESP_CODE_SUCCESS = 1; // 成功
export const RESP_CODE_FAIL = 0; // 失败
export const INVALID_TOKEN = 10001 // token失效
export const EXPIRED_TOKEN = 10002 // token失效

// 缓存设置
export const PREFIX_KEY = 'storage_'; // storage 前缀
export const TOKEN = 'token'; // storage token 的key
export const AUTH_DATA = 'authData'; // storage 用户信息 的key
export const PHONE = 'phone'; // storage 用户信息 的key
export const SELECT_PLACE = 'place'; // 取餐点