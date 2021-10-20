import request from "./request";

export function list(data) {
  return request({
    method: 'post',
    url: 'admin/role/list',
    data: data,
  })
}
export function info(data) {
  return request({
    method: 'post',
    url: 'admin/role/info',
    data: data,
  })
}
export function save(data) {
  return request({
    method: 'post',
    url: 'admin/role/save',
    data: data,
  })
}
export function deleteOne(data) {
  return request({
    method: 'post',
    url: 'admin/role/delete',
    data: data,
  })
}
export function menu(data) {
  return request({
    method: 'post',
    url: 'admin/role/menu',
    data: data,
  })
}