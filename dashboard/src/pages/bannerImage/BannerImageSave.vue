<template>
  <a-spin :spinning="loading">
    <a-form-model ref="form" :model="form" :label-col="labelCol" :wrapper-col="wrapperCol" :rules="rules">
      
      <a-form-model-item label="所属banner" prop="banner_id">
        <a-select v-model="form.banner_id" placeholder="请选择">
          <a-select-option v-for="item in banners" :key="item.id" :value="item.id">
            <span>{{item.name}}</span>
          </a-select-option>
        </a-select>
      </a-form-model-item>
      
      <a-form-model-item label="图片" prop="image">
        <image-list v-model="images" :max="1" @change="imgChange" />
      </a-form-model-item>
      <a-form-model-item label="标题">
        <a-input v-model="form.title" />
      </a-form-model-item>
      <a-form-model-item label="url" prop="url">
        <a-input v-model="form.url" />
      </a-form-model-item>

      <a-form-model-item label="排序">
        <a-input-number v-model="form.sorting" :min="0" />
      </a-form-model-item>

      <a-form-model-item label="状态">
        <a-radio-group v-model="form.status">
          <a-radio :value="0">
            未发布
          </a-radio>
          <a-radio :value="1">
            已发布
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
import { list as banners } from "@/api/banner"
import { info, save } from '@/api/bannerImage'
import ImageList from '@/components/ImageList.vue'

export default {
  components: { ImageList },
  data() {
    return {
      loading: true,
      saving: false,
      labelCol: { span: 4 },
      wrapperCol: { span: 14 },
      form: {
        banner_id: undefined,
        title: '',
        image: '',
        sorting : 0,
        status: 1,
      },
      rules: {
        image: [{required: true, message: '请选择图片'}],
        banner_id: [{required: true, message: '请选择所属banner'}],
      },
      banners: [],
      images: []
    };
  },
  mounted() {
    banners().then(res => {
      this.banners = res
    }).catch((err) => {
      this.$message.error(err.msg ? err.msg : err.message);
      if (err.code == 10001) {
        this.$router.push("/login");
        return;
      }
    })

    if (this.$route.query.id) {
      info({
        id: this.$route.query.id
      }).then(res => {
        console.log(res)
        this.form.banner_id = res.banner_id
        this.form.title = res.title
        this.form.url = res.url
        this.form.sorting = res.sorting
        this.form.status = res.status

        this.form.image = res.image
        this.images = [res.image]

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
          data.logo = this.images.length > 0 ? this.images[0] : ''

          this.saving = true
          save(data).then(() => {
            this.$message.success('提交成功')
            this.$router.go(-1)
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
    imgChange() {
      this.form.image = this.images.length > 0 ? this.images[0] : ''
    }
  },
}
</script>

<style>

</style>