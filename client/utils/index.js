import {
	PREFIX_KEY,
	RESP_CODE_SUCCESS,
	AUTH_DATA,
	IMG_HOST
} from './constant';
import {
	isEmpty
} from './validator'; // 导出isEmpty方法
export {
	isEmpty
};
import {profile} from '@/api/my'

/**
 * 获取token
 */
export function getToken() {
	let token = undefined;
	const userData = getUserData();
	if (!isEmpty(userData) && !isEmpty(userData.token)) {
		token = userData.token;
	}
	return token;
}

export async function checkLogin() {
	if (!getToken()) {
		return false
	}
	try {
		return await profile()
	}catch{
		return false
	}
}

/**
 * 获取用户数据
 */
export function getUserData() {
	const userData = getStorage(AUTH_DATA);
	if (!isEmpty(userData)) {
		return JSON.parse(userData);
	}
	return userData;
}

/**
 * 记录用户数据
 * @param {Object} userData
 */
export function setUserData(userData) {
	return setStorage(AUTH_DATA, JSON.stringify(userData));
}

/**
 * 清除用户数据
 */
export function removeUserData() {
	return removeStorage(AUTH_DATA);
}

/**
 * 获取指定本地缓存数据
 * @param {String} key
 */
export function getStorage(key) {
	return uni.getStorageSync(PREFIX_KEY + key);
}

/**
 * 设置指定本地缓存数据
 * @param {String} key
 * @param {Mixed} value
 */
export function setStorage(key, value) {
	return uni.setStorageSync(PREFIX_KEY + key, value);
}

/**
 * 移除指定本地缓存数据
 * @param {String} key
 */
export function removeStorage(key) {
	return uni.removeStorageSync(PREFIX_KEY + key);
}

/**
 * Object -> queryString
 * @param {Object} params
 */
export function getQueryString(params) {
	let query = '';
	if (params) {
		try {
			const p = Object.keys(params).forEach(key => {
				return key + '=' + params[key];
			});
			query = '?' + p.join('&');
		} catch (e) {
			query = '';
		}
	}
	return query;
}

/**
 * queryString -> Object
 * @param {string} url
 */
export function getParams(url) {
	url = decodeURIComponent(url);
	const theRequest = {};
	if (url.indexOf("?") != -1) {
		const str = url.substr(url.indexOf("?") + 1);
		const strs = str.split("&");
		for (let i = 0; i < strs.length; i++) {
			const pair = strs[i].split("=");
			theRequest[pair[0]] = unescape(pair[1]);
		}
	}
	return theRequest;
}

/**
 * 做参数过滤用，更新数据接口的时候
 * @param {Object} obj1  后端所需要的字段对象
 * @param {Object} obj2  源对象 （后端返回的一大堆数据）
 */
export const copyParams = function(obj1, obj2) {
	const newObj = {}
	for (let key in obj1) {
		if (obj1.hasOwnProperty(key)) {
			newObj[key] = obj2[key];
		}
	}
	return newObj;
}

/**
 * Parse the time to string
 * @param {(Object|string|number)} time
 * @param {string} cFormat
 * @returns {string}
 */
export function parseTime(time, cFormat) {
	if (arguments.length === 0) {
		return null;
	}
	const format = cFormat || '{y}-{m}-{d} {h}:{i}:{s}';
	let date;
	if (typeof time === 'object') {
		date = time;
	} else {
		if (typeof time === 'string' && /^[0-9]+$/.test(time)) {
			time = parseInt(time);
		}
		if (typeof time === 'number' && time.toString().length === 10) {
			time = time * 1000;
		}
		date = new Date(time);
	}
	const formatObj = {
		y: date.getFullYear(),
		m: date.getMonth() + 1,
		d: date.getDate(),
		h: date.getHours(),
		i: date.getMinutes(),
		s: date.getSeconds(),
		a: date.getDay()
	};
	const time_str = format.replace(/{(y|m|d|h|i|s|a)+}/g, (result, key) => {
		let value = formatObj[key];
		// Note: getDay() returns 0 on Sunday
		if (key === 'a') {
			return ['日', '一', '二', '三', '四', '五', '六'][value];
		}
		if (result.length > 0 && value < 10) {
			value = '0' + value;
		}
		return value || 0;
	});
	return time_str;
}

/**
 * 过滤无效参数
 * 约定当该筛选参数值为-1时忽略该参数
 *
 * @param {Object} params
 * @return {Object}
 */
export function filterListQuery(params) {
	const result = {};
	const keys = Object.keys(params);
	for (const key of keys) {
		if (!params[key] && params[key] !== 0) {
			continue;
		}
		// 约定'-1'作为'全部'的意思(即忽略该条件约束)
		if (params[key] === -1) {
			continue;
		}
		result[key] = params[key];
	}
	return result;
}

export function getImgFullUrl(url) {
	return IMG_HOST + url;
}