import axios from "axios"
import { API_TOKEN_STORAGE_KEY } from './request'

export function login(data) {
  return new Promise((resolve, reject) => {
    axios({
      method: 'post',
      baseURL: process.env.VUE_APP_API_BASE_URL,
      url: 'admin/user/login',
      data: data,
      headers: {
        'Accept': 'application/json'
      }
    }).then(resp => {
      if (resp.data) {
        if (resp.data.code == 1) {
          localStorage.setItem(API_TOKEN_STORAGE_KEY, resp.data.data)
          resolve(resp.data.data)
        } else {
          reject(resp.data)
        }
      } else {
        reject(resp)
      }
    }).catch(err => {
      console.log(err)
      reject(err)
    })
  })
}

export function profile() {
  return new Promise((resolve, reject) => {
    const token = localStorage.getItem(API_TOKEN_STORAGE_KEY)
    if (!token) {
      reject({code:10001,msg:'登录校验失败'})
    }
    axios({
      method: 'post',
      baseURL: process.env.VUE_APP_API_BASE_URL,
      url: 'admin/user/profile',
      headers: {
        'Accept': 'application/json',
        'Authorization': token,
      }
    }).then(resp => {
      if (resp.data) {
        if (resp.data.code == 1) {
          resolve(resp.data.data)
        } else if (resp.data.code == 10001) {
          reject(resp.data)
        }
      }
    }).catch(err => {
      console.log(err)
    })
  })
}