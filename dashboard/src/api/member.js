import request from "./request";

export function list(data) {
  return request({
    method: 'post',
    url: 'admin/member/list',
    data: data,
  })
}

export function edit(data) {
  return request({
    method: 'post',
    url: 'admin/member/edit',
    data: data,
  })
}