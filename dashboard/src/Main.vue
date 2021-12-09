<template>
  <a-layout id="components-layout-demo-fixed-sider">
    <a-layout-sider :style="{ overflow: 'hidden', height: '100vh', position: 'fixed', left: 0 }" v-model="collapsed" collapsible>
      <div class="logo">管理后台</div>
      <a-menu theme="dark" mode="inline" :selected-keys="[$route.path]" :open-keys="currentGroup">
        <a-sub-menu v-for="group in Object.keys(navigateGroups)" :key="group" @titleClick="groupClick(group)" class="menu-group">
          <span slot="title">{{group}}</span>
          <a-menu-item v-for="item in navigateGroups[group]" :key="item.path" @click="menuItemClick(item)">
            <a-icon :type="item.meta.icon" />
            <span class="nav-text">{{item.name}}</span>
          </a-menu-item>
        </a-sub-menu>
        
      </a-menu>
    </a-layout-sider>
    <a-layout :style="{ marginLeft: (collapsed ? 80 : 200) + 'px' }">
      <a-affix :offset-top="0">
        <a-layout-header theme="dark" :style="{ display: 'flex' }">
          <div :style="{ flex: 1 }"></div>
          <a-dropdown :trigger="['click']">
            <a class="ant-dropdown-link" @click="e => e.preventDefault()" :style="{color: 'white'}">
              {{username}} <a-icon type="down" />
            </a>
            <a-menu slot="overlay">
              <a-menu-item @click="logout">
                退出
              </a-menu-item>
            </a-menu>
          </a-dropdown>
        </a-layout-header>
        <a-breadcrumb :style="{ padding: '16px', backgroundColor: '#f0f2f5' }" :routes="breadcrumbRoutes" >
          <template slot="itemRender" slot-scope="{route}">
            <router-link :to="route.path">
              {{route.name}}
            </router-link>
          </template>
        </a-breadcrumb>
      </a-affix>
      <a-layout-content :style="{ overflow: 'initial' }">
        <keep-alive>
          <router-view v-if="$route.meta.keepAlive" class="router-view"></router-view>
        </keep-alive>
        <router-view v-if="!$route.meta.keepAlive" class="router-view"></router-view>

      </a-layout-content>
      <a-layout-footer :style="{ textAlign: 'center' }">
        Ant Design ©2018 Created by Ant UED
      </a-layout-footer>
    </a-layout>
  </a-layout>
</template>

<script>
import { profile } from '@/api/user'

import MemberList from './pages/member/MemberList.vue'

import BannerImageList from './pages/bannerImage/BannerImageList.vue'
import BannerImageSave from './pages/bannerImage/BannerImageSave.vue'

import AccountList from './pages/account/AccountList.vue'
import AccountSave from './pages/account/AccountSave.vue'
import RoleList from './pages/account/RoleList.vue'
import RoleSave from './pages/account/RoleSave.vue'


import p404 from './components/404.vue'

const routeView = {
  'member': MemberList,
  'account': AccountList,
  'account/save': AccountSave,
  'role': RoleList,
  'role/save': RoleSave,
  'bannerImage': BannerImageList,
  'bannerImage/save': BannerImageSave,
}

import { API_TOKEN_STORAGE_KEY } from '@/api/request'

export default {
  data() {
    return {
      username: 'admin',
      currentGroup: [],
      navigateGroups: {},
      collapsed: false,
    }
  },
  beforeCreate() {
    profile().then(res => {
      if (res.status == 0) {
        this.$router.push('/login')
        return
      }
      this.username = res.username

      console.log(this.$router)
      res.role.menus.forEach(m => {
        if (!this.$router.getRoutes().find(r=>r.path=='/admin/'+m.path)) {
          if (Object.prototype.hasOwnProperty.call(routeView, m.path)) {
            this.$router.addRoute('main', {
              component: routeView[m.path],
              path: m.path,
              name: m.name,
              meta: {
                group: m.group,
                icon: m.icon,
                keepAlive: m.path == 'order'
              }
            })
          }
        }
      })
      this.$router.addRoute({
        path: '*',
        name: '404',
        component: p404
      })
      this.loadNavigateGroups()
      
      if (this.$route.query.p && this.$route.query.f) {
        console.log(this.$route.query.f)
        const r = this.$router.getRoutes().find(r => this.$route.query.p == r.path)
        if (r) {
          console.log(r)
          this.$router.push(this.$route.query.f)
          if (!this.collapsed)
            this.currentGroup.push(r.meta.group ? r.meta.group : '其他设置')
        }
      }else if (this.$route.path == '/admin') {
        let routes = this.$router.getRoutes().filter(r => {
          return r.path.split('/').length > 2
        })
        if (routes.length > 0) {
          this.$router.push(routes[0].path)
          if (!this.collapsed)
            this.currentGroup.push(routes[0].meta.group ? routes[0].meta.group : '其他设置')
        }
        
      }
    }).catch(err => {
      if (err.code == 10001) {
        this.$router.push('/login')
        return
      }
    })
  },
  mounted() {
    if (window.innerWidth <= 768) {
      this.collapsed = true
      this.currentGroup = []
    }
    let that = this
    window.onresize = () => {
      console.log(window.innerWidth)
      if (window.innerWidth <= 768) {
        that.collapsed = true
        that.currentGroup = []
      }
    }
  },
  methods: {
    logout() {
      localStorage.removeItem(API_TOKEN_STORAGE_KEY)
      // this.$router.push('/login')
      window.location.reload()
    },
    groupClick(group) {
      let set = new Set(this.currentGroup)
      if (set.has(group)) set.delete(group)
      else {
        if (this.collapsed) {
          set.clear()
        }
        set.add(group)
      }
      this.currentGroup = Array.from(set)
    },
    menuItemClick(item) {
      this.$router.push(item.path)
      if (this.collapsed) {
        this.currentGroup = []
      }
    },
    loadNavigateGroups() {
      let groups = {
      }
      let routes = this.$router.getRoutes().filter(r => {
        return r.path.split('/').length > 2
      })
      routes.forEach(item => {
        if (item.path.split('/').length  == 3) {
          if (item.meta.group) {
            if (groups[item.meta.group]) {
              groups[item.meta.group].push(item)
            }else{
              groups[item.meta.group] = [item]
            }
          }else{
            if (groups['其他设置']) {
              groups['其他设置'].push(item)
            }else{
              groups['其他设置'] = [item]
            }
          }
        }
      })
      this.navigateGroups = groups
    },
  },
  computed: {
    navigateRoutes() {
      let routes = this.$router.getRoutes().filter(r => {
        return r.path.split('/').length == 3
      })
      return routes
    },
    breadcrumbRoutes() {
      let routes = []
      const paths = this.$route.path.split('/')
      for (let i=3; i < paths.length+1; i++) {
        let path = paths.slice(0,i).join('/')
        let r = this.$router.getRoutes().find(item=>item.path == path)
        routes.push({
          path,
          name: r ? r.name : ''
        })
      }
      return routes
    },
  }
}
</script>

<style>
#components-layout-demo-fixed-sider .logo {
  height: 32px;
  /* background: rgba(255, 255, 255, 0.2); */
  margin: 16px;
  font-size: 24px;
  color: white;
  overflow: hidden;
}
.router-view {
  background: white;
  padding: 16px;
}
.refund-item {
  padding:16px;
  border-bottom: 1px solid #ccc;
  cursor: pointer;
}
.refund-item:hover {
  background: #e9f6fe;
}
.ant-menu-inline-collapsed > .ant-menu-item, .ant-menu-inline-collapsed > .ant-menu-item-group > .ant-menu-item-group-list > .ant-menu-item, .ant-menu-inline-collapsed > .ant-menu-item-group > .ant-menu-item-group-list > .ant-menu-submenu > .ant-menu-submenu-title, .ant-menu-inline-collapsed > .ant-menu-submenu > .ant-menu-submenu-title {
    padding: 0px 8px !important;
  }
</style>