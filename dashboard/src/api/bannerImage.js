import request from "./request";

export function list(data) {
  return request({
    method: 'post',
    url: 'admin/bannerImage/list',
    data: data,
  })
}
export function info(data) {
  return request({
    method: 'post',
    url: 'admin/bannerImage/info',
    data: data,
  })
}
export function save(data) {
  return request({
    method: 'post',
    url: 'admin/bannerImage/save',
    data: data,
  })
}
export function deleteMany(data) {
  return request({
    method: 'post',
    url: 'admin/bannerImage/deleteMany',
    data: data,
  })
}