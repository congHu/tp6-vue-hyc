import request from "./request";

export function list(data) {
  return request({
    method: 'post',
    url: 'admin/banner/list',
    data: data,
  })
}