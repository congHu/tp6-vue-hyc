<template>
	<view>
		<button v-if="user == null" open-type="getPhoneNumber" @getphonenumber="getPhone">授权登录</button>
    <view v-if="user != null">
      <image style="width: 100px;height: 100px;" mode="aspectFill" :src="user.avatar" />
      <view>欢迎您，{{user.nickname ? user.nickname : user.phone}}</view>
      <button @click="getUserInfo">授权个人信息</button>
    </view>
	</view>
</template>

<script>
	import { getUserData, setUserData } from '@/utils'
	import { mpLogin, profile, mpUserInfo } from '@/api/my'
	export default {
		data() {
			return {
				user: null,
			}
		},
		onShow() {
      this.checkLogin()
		},
		methods: {
      async checkLogin() {
        this.user = getUserData()
        try {
          const info = await profile()
          for (const key in info.data) {
            this.user[key] = info.data[key]
          }
          setUserData(this.user)
        }catch(checkLogin) {
          console.log('checkLogin', checkLogin)
          this.user = null
        }
        
        
      },
		  getPhone(e) {
        console.log(e)
        if (e.detail.encryptedData && e.detail.iv) {
          uni.showLoading()
          let that = this
          uni.login({
            success(loginRes) {
              console.log('uniLogin', loginRes)
              mpLogin({
                code: loginRes.code,
                encrypted_data: e.detail.encryptedData,
                iv: e.detail.iv,
              }).then(mpLoginRes => {
                console.log('mpLoginRes', mpLoginRes)
                that.user = mpLoginRes.data
                setUserData(that.user)
                uni.hideLoading()
              }).catch(mpLoginErr => {
                console.log('mpLoginErr', mpLoginErr)
              })
            },
            fail(loginErr) {
              console.log('uniLogin', loginErr)
            }
          })
        }
      },
      getUserInfo() {
        let that = this
        uni.getUserProfile({
        	desc: '授权个人信息',
        	success(user) {
            console.log(user)
            uni.showLoading()
            mpUserInfo({
              nickname: user.userInfo.nickName,
              avatar: user.userInfo.avatarUrl
            }).then(userInfoRes => {
              console.log('userInfoRes', userInfoRes)
              for (const key in userInfoRes.data) {
                that.user[key] = userInfoRes.data[key]
              }
              setUserData(that.user)
              uni.hideLoading()
            }).catch(userInfoErr => {
              console.log('userInfoErr', userInfoErr)
            })
          },
          fail(err) {
            console.log(err)
          }
        })
      },
		}
	}
</script>

<style>
  
</style>
