<template>
  <a-spin :spinning="loading">
    <a-form-model ref="form" :model="form" :label-col="labelCol" :wrapper-col="wrapperCol" :rules="rules">
      <a-form-model-item label="用户名" prop="username">
        <a-input v-model="form.username" />
      </a-form-model-item>
      <a-form-model-item label="昵称">
        <a-input v-model="form.nickname" />
      </a-form-model-item>
      <a-form-model-item label="密码">
        <a-input v-model="form.password" placeholder="输入密码以修改" />
      </a-form-model-item>

      <a-form-model-item label="角色" prop="role_id">
        <a-select v-model="form.role_id" placeholder="请选择">
          <a-select-option v-for="item in roles" :key="item.id" :value="item.id">
            {{item.name}}
          </a-select-option>
        </a-select>
      </a-form-model-item>

      <a-form-model-item label="状态">
        <a-radio-group v-model="form.status">
          <a-radio :value="0">
            禁用
          </a-radio>
          <a-radio :value="1">
            启用
          </a-radio>
        </a-radio-group>
      </a-form-model-item>

      <a-form-model-item :wrapper-col="{ span: 14, offset: 4 }">
        <a-button type="primary" @click="onSubmit" :loading="saving">
          提交
        </a-button>
        <a-button style="margin-left: 10px;" @click="$router.push('.')">
          取消
        </a-button>
      </a-form-model-item>
    </a-form-model>
  </a-spin>
</template>

<script>
import { info, save } from '@/api/account'
import { list as roleList } from '@/api/role'

export default {
  data() {
    return {
      loading: true,
      saving: false,
      labelCol: { span: 4 },
      wrapperCol: { span: 14 },
      form: {
        username: '',
        nickname: '',
        password: '',
        role_id: undefined,
        status: 1,
      },
      rules: {
        username: [{required: true, message: '标题不能为空'}],
        role_id: [{required: true, message: '请选择'}],
      },
      roles: []
    };
  },
  mounted() {
    roleList({
      page_size: -1
    }).then(res => {
      this.roles = res
    }).catch(err => {
      this.$message.error(err.msg ? err.msg : err.message)
      if (err.code == 10001) {
        this.$router.push('/login')
        return
      }
    })

    if (this.$route.query.id) {
      info({
        id: this.$route.query.id
      }).then(res => {
        console.log(res)
        this.form.username = res.username
        this.form.nickname = res.nickname
        this.form.role_id = res.role_id ? res.role_id : undefined
        this.form.status = res.status

      }).catch(err => {
        this.$message.error(err.msg ? err.msg : err.message)
        if (err.code == 10001) {
          this.$router.push('/login')
          return
        }
      }).finally(() => {
        this.loading = false
      })
    }else{
      this.loading = false
    }
  },
  methods: {
    onSubmit() {
      this.$refs.form.validate(valid => {
        if (valid) {
          console.log('submit!');
          let data = Object.assign({}, this.form)
          if (this.$route.query.id) {
            data.id = this.$route.query.id
          }

          this.saving = true
          save(data).then(() => {
            this.$message.success('提交成功')
            this.$router.push('.')
            this.saving = false
          }).catch(err => {
            console.log(err)
            this.$message.error(err.msg ? err.msg : err.message)
            this.saving = false
            if (err.code == 10001) {
              this.$router.push('/login')
              return
            }
          })
        } else {
          console.log('error submit!!');
          return false;
        }
      });

    },
  },
}
</script>

<style>

</style>