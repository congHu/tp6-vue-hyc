/**
 * 我的接口
 */
import request from '@/utils/http';

/**
 * @description 登录
 * @data {Object}
 */
export function mpLogin(data) {
	return request({
		url: '/api/user/mplogin',
		method: 'POST',
		data
	});
}

/**
 * @description 个人资料
 * @data {Object}
 */
export function profile(data) {
	return request({
		url: '/api/user/profile',
		method: 'POST',
		data
	});
}

/**
 * @description 设置个人资料
 * @data {Object}
 */
export function mpUserInfo(data) {
	return request({
		url: '/api/user/mpUserInfo',
		method: 'POST',
		data
	});
}
