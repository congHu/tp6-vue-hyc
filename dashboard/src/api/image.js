import request from "./request";

export function list(data) {
  return request({
    method: 'post',
    url: 'admin/image/list',
    data: data,
  })
}
export function deleteMany(data) {
  return request({
    method: 'post',
    url: 'admin/image/deleteMany',
    data: data,
  })
}
