import request from '@/utils/http';
export function homeBanner() {
	return request({
		url: '/api/bannerImage/list',
		method: 'get',
		data: {
			banner_id: 1,
			page_size: -1
		}
	})
}

export function adBanner() {
	return request({
		url: '/api/bannerImage/list',
		method: 'get',
		data: {
			banner_id: 2,
			page_size: -1
		}
	})
}
