/**
 * @description http请求函数封装
 */

import { REQ_API_HOST, RESP_CODE_SUCCESS, INVALID_TOKEN, EXPIRED_TOKEN, CARGO_OWNER } from '@/utils/constant';
import { getToken, removeUserData, getUserData, isEmpty } from '@/utils';

// 错误code 处理
const handleErrorCode = code => {
	if (code === INVALID_TOKEN || code === EXPIRED_TOKEN) {
		removeUserData();
		uni.navigateTo({
		    url: '/pages/login/login'
		});
	}
}

const _request = (url, resolve, reject, data = {}, method = 'GET') => {
	uni.request({
		header: {
			'Content-Type': 'application/json', // 默认json
			'Authorization': getToken(),
		},
		url: REQ_API_HOST + url,
		method,
		data,
		success: (res) => {
			console.log({
				url,
				method,
				data: res.data
			})
			if (res.data.code === RESP_CODE_SUCCESS) {
				resolve(res.data);
			} else {
				console.log(`出错了! [${res.data.code}] ${res.data.msg}`);
				handleErrorCode(res.data.code);				
				reject(res.data);
				if (res.data.msg) {
					uni.showToast({
						title: `${res.data.msg}`,
						icon: 'none'
					});
				} else {
					uni.showToast({
						title: '服务端出错，请联系管理员',
						icon: 'none'
					});
				}
			}
		},
		fail: (err) => {
			console.log('请求调用失败!');
			uni.showToast({
				title: '服务端出错，请联系管理员',
				icon: 'none'
			});
			reject(err);
		}
	})
}

export default ({ url, data, method }) => {
	return new Promise((resolve, reject) => {
		_request(url, resolve, reject, data, method);
	});
}
