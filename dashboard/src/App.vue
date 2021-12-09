<template>
  <div id="app">
    <a-config-provider :locale="locale">
      <router-view></router-view>
    </a-config-provider>
  </div>
</template>

<script>
import zhCN from 'ant-design-vue/lib/locale-provider/zh_CN';
import { API_TOKEN_STORAGE_KEY } from '@/api/request'

export default {
  name: 'App',
  components: {
  },
  data() {
    return {
      locale: zhCN,
    };
  },
  beforeCreate() {
    if (!localStorage.getItem(API_TOKEN_STORAGE_KEY)) {
      this.$router.push('/login')
      return
    }
    if (this.$route.path == '/') {
      this.$router.push('/admin')
    }
  }
}
</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  height: 100%;
}
</style>
