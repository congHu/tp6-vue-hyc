import request from "./request";

export function list(data) {
  return request({
    method: 'post',
    url: 'admin/user/list',
    data: data,
  })
}
export function info(data) {
  return request({
    method: 'post',
    url: 'admin/user/info',
    data: data,
  })
}
export function save(data) {
  return request({
    method: 'post',
    url: 'admin/user/save',
    data: data,
  })
}
export function deleteOne(data) {
  return request({
    method: 'post',
    url: 'admin/user/delete',
    data: data,
  })
}
