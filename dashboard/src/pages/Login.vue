<template>
  <a-layout id="login">
    <a-form
      id="components-form-demo-normal-login"
      :form="form"
      class="login-form"
      @submit="handleSubmit"
    >
      <a-form-item>
        <a-input
          v-decorator="[
            'username',
            { rules: [{ required: true, message: '用户名不能为空' }] },
          ]"
          placeholder="用户名"
        >
          <a-icon type="user" style="color: rgba(0,0,0,.25)" />
        </a-input>
      </a-form-item>
      <a-form-item>
        <a-input-password
          v-decorator="[
            'password',
            { rules: [{ required: true, message: '密码不能为空' }] },
          ]"
          placeholder="密码"
        >
          <a-icon type="lock" style="color: rgba(0,0,0,.25)" />
        </a-input-password>
      </a-form-item>
      <a-form-item style="text-align: center">
        <a-button type="primary" html-type="submit" class="login-form-button" :loading="loading">
          登录
        </a-button>
      </a-form-item>
    </a-form>
  </a-layout>
</template>

<script>
import { login } from '../api/user'
export default {
  name: 'Login',
  data() {
    return {
      loading: false,
    }
  },
  beforeCreate() {
    this.form = this.$form.createForm(this, { name: 'normal_login' });
    console.log('env', process.env.VUE_APP_API_BASE_URL)
  },
  methods: {
    handleSubmit(e) {
      e.preventDefault();
      this.form.validateFields((err, values) => {
        if (!err) {
          console.log('Received values of form: ', values)
          this.loading = true

          login(values).then(res => {
            console.log(res)
            this.$router.push('/admin')
          }).catch(err => {
            this.$message.error(err.msg ? err.msg : err.message)
          }).finally(() => {
            this.loading = false
          })
        }
      });
    },
  },
}
</script>

<style>
#login {
  height: 100%;
  justify-content: center;
}
.login-form {
  height: 50%;
  width: 400px;
  margin-left: auto;
  margin-right: auto;
}
</style>