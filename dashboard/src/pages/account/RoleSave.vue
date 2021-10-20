<template>
  <a-spin :spinning="loading">
    <a-form-model ref="form" :model="form" :label-col="labelCol" :wrapper-col="wrapperCol" :rules="rules">
      <a-form-model-item label="名称" prop="name">
        <a-input v-model="form.name" />
      </a-form-model-item>

      <a-form-model-item label="菜单">
        <a-select v-model="form.menu_ids" placeholder="请选择" mode="multiple">
          <a-select-option v-for="item in menus" :key="item.id" :value="item.id">
            {{item.name}}
          </a-select-option>
        </a-select>
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
import { info, save, menu } from '@/api/role'

export default {
  data() {
    return {
      loading: true,
      saving: false,
      labelCol: { span: 4 },
      wrapperCol: { span: 14 },
      form: {
        name: '',
        menu_ids: []
      },
      rules: {
        username: [{required: true, message: '标题不能为空'}],
        role_id: [{required: true, message: '请选择'}],
      },
      menus: []
    };
  },
  mounted() {
    menu({
      page_size: -1
    }).then(res => {
      console.log(res)
      this.menus = res
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
        this.form.name = res.name
        this.form.menu_ids = res.menus.map(m=>m.id)
      }).catch(err => {
        console.log(err)
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