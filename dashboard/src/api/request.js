import axios from "axios"

export const API_TOKEN_STRAGE_KEY = 'lijipeisong_admin_token'

export default function (config) {
  return new Promise((resolve, reject) => {
    config.baseURL = process.env.VUE_APP_API_BASE_URL
    const token = localStorage.getItem(API_TOKEN_STRAGE_KEY)
    if (!token) {
      reject({code:10001,msg:'登录校验失败'})
    }
    config.headers = {
      'Authorization': token,
      'Accept': 'application/json'
    }
    axios(config).then(resp => {
      console.log(resp)
      if (resp.data) {
        if (resp.data.code == 1) {
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