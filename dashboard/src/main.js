import Vue from 'vue'
import VueRouter from 'vue-router'
import Antd from 'ant-design-vue'
import 'ant-design-vue/dist/antd.css'
import axios from 'axios'
import VueAxios from 'vue-axios'

import App from './App.vue'
import Main from './Main.vue'
import Login from './pages/Login.vue'

Vue.config.productionTip = false

Vue.use(Antd)
Vue.use(VueRouter)

Vue.use(VueAxios, axios)

const router = new VueRouter({
  mode: 'history',
  routes: [
    {
      path: '/login',
      component: Login
    },
    {
      path: '/admin',
      name: 'main',
      component: Main,
    }
  ]
})

router.beforeEach((to, from, next) => {
  document.title = (to.name ? (to.name + ' - ') : '') + '数据后台管理'

  if (!router.getRoutes().find(r => r.path == to.path) && to.path.indexOf('/admin/')==0 && to.path.length > '/admin/'.length) {
    if (from.path == '/') {
      next('/admin?p='+to.path+'&f='+to.fullPath)
    }else{
      next(false)
    }
  }
  next()
})

new Vue({
  router,
  render: h => h(App),
}).$mount('#app')
