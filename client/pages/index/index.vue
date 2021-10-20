<template>
	<view class="body">
		<swiper style="height: 240px;border-bottom: 1px solid #ccc;">
			<swiper-item v-for="item in homeBanner" :key="item.id" @click="swpierClick(item)">
				<image style="width: 100%; height: 100%;" :src="getImgFullUrl(item.image)" mode="aspectFill"></image>
			</swiper-item>
		</swiper>
    
    <view>AD</view>
		<swiper style="height: 160px;padding: 16px;">
			<swiper-item v-for="item in adBanner" :key="item.id" @click="swpierClick(item)">
				<image style="width: 100%; height: 100%;border-radius: 10px;" :src="getImgFullUrl(item.image)" mode="aspectFill"></image>
			</swiper-item>
		</swiper>
	</view>
</template>

<script>
	import { homeBanner, adBanner } from '@/api/banner'
	import { getImgFullUrl } from '@/utils'
	export default {
		data() {
			return {
				homeBanner: [],
				adBanner: []
			}
		},
		onLoad() {
			this.loadBanner()
		},
		methods: {
			loadBanner() {
				homeBanner().then(res => {
					this.homeBanner = res.data
				})
				adBanner().then(res => {
					this.adBanner = res.data
				})
			},
			getImgFullUrl(url) {
				return getImgFullUrl(url)
			},
			swpierClick(item) {
				if (item.url) {
					if (item.url.indexOf('http') == 0) {
						// uni.navigateTo({
						// 	url: '/pages/index/webview?src=' + item.url
						// })
					}else if (item.url.indexOf('/pages/') == 0) {
						uni.navigateTo({
							url: item.url
						})
					}
				}
      }
		}
	}
</script>

<style>
  
</style>
