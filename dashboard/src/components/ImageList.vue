<template>
  <div class="component">
    <div v-if="showList">
      <div v-for="(item,index) in imageList" :key="item" class="img-item">
        <img :src="getImgUrl(item)" />
        <a-popconfirm
          v-if="confirmDelete"
          title="删除?"
          ok-text="删除"
          cancel-text="取消"
          @confirm="handleDelete(index)"
        >
          <div class="img-delete-btn">
            <a-icon type="delete" />
          </div>
        </a-popconfirm>
        <div v-else @click="handleDelete(index)" class="img-delete-btn">
          <a-icon type="delete" />
        </div>
      </div>
    </div>
    <div v-if="showList && ((max > 0 && imageList.length < max) || max <= 0)" class="img-item" @click="showImgModel">
      <div class="add-img-item">
        <div class="add-icon-holder">
          <a-icon type="plus" class="add-icon" />
        </div>
        <div v-if="max>0">{{imageList.length+1}}/{{max}}</div>
      </div>
    </div>
    <a-button v-if="!showList" @click="showImgModel"><a-icon type="picture" />插入图片</a-button>
    <a-modal v-model="showModel" title="图片" :footer="null">
      <a-spin :spinning="loading || uploading" :style="{ overflow: 'hidden'}">
        <div v-for="item in imageLibrary" :key="item.id" class="img-item">
          <img :src="getImgUrl(item.url)" @click="addImageId(item.url)" />
          <a-popconfirm
            title="删除后不可恢复"
            ok-text="删除"
            cancel-text="取消"
            @confirm="handleImageDelete(item.id)"
          >
            <div class="img-delete-btn">
              <a-icon type="delete" />
            </div>
          </a-popconfirm>
        </div>
        <!-- <a-spin :spinning="uploading"> -->
        <a-upload
          name="file"
          :action="uploadAction"
          :headers="uploadHeader"
          @change="handleUploadChange"
          :showUploadList="false"
          :beforeUpload="beforeUpload"
        >
        <!-- accept=".png,.jpeg,.jpg" -->
          <div class="img-item">
            <div class="add-img-item">
              <div class="add-icon-holder">
                <a-icon type="plus" class="add-icon" />
              </div>
              <div @click.stop="">
                <a-checkbox v-model="isCompressImage">压缩上传</a-checkbox>
              </div>
            </div>
          </div>
        </a-upload>
        <!-- </a-spin> -->
      </a-spin>
      <a-pagination v-model="page" :total="total" :defaultPageSize="pageSize" show-less-items @change="loadList" />
    </a-modal>
  </div>
</template>

<script>
import { list, deleteMany } from '@/api/image'
import { API_TOKEN_STRAGE_KEY } from '@/api/request'

export default {
  name: 'ImageList',
  props: {
    imageList: Array,
    showList: {
      default: true,
      type: Boolean
    },
    max: {
      default: 5,
      type: Number
    },
    confirmDelete: {
      default: false,
      type: Boolean
    },
    compressImage: {
      default: false,
      type: Boolean
    }
  },
  model: {
    prop: 'imageList',
    event: 'change'
  },
  data() {
    return {
      showModel: false,
      imageLibrary: [],
      loading: false,
      page: 1,
      total: 0,
      pageSize: 19,
      uploadAction: process.env.VUE_APP_API_BASE_URL + 'admin/image/upload',
      uploadHeader: {
        Authorization: localStorage.getItem(API_TOKEN_STRAGE_KEY)
      },
      uploading: false,
      isCompressImage: false
    }
  },
  methods: {
    // onchange() {
    //   this.$emit('change', this.imageList);
    // },
    getImgUrl(url) {
      return process.env.VUE_APP_API_BASE_URL + 'storage/' + url
    },
    handleDelete(index) {
      const toDelete = this.imageList[index]
      this.imageList.splice(index, 1)
      this.$emit('change', this.imageList, {remove: toDelete})
    },
    refreshList() {
      this.loadList()
    },
    loadList(page=1) {
      console.log(page)
      this.loading = true
      list({
        page: page,
        page_size: this.pageSize
      }).then(res => {
        this.page = page
        this.total = res.total
        this.imageLibrary = res.data
      }).catch(err => {
        this.$message.error(err.msg)
        if (err.code == 10001) {
          this.$router.push('/login')
          return
        }
      }).finally(() => {
        this.loading = false
      })
    },
    showImgModel() {
      this.showModel = true
      this.isCompressImage = this.compressImage
      this.refreshList()
    },
    handleImageDelete(id) {
      deleteMany({
        ids: [id].join(',')
      }).then(res => {
        if (res.data) this.$message.success('删除成功')
        this.refreshList()
      }).catch(err => {
        this.$message.error(err.msg)
        if (err.code == 10001) {
          this.$router.push('/login')
          return
        }
      })
    },
    beforeUpload(f) {
      console.log(f)
      return new Promise((resolve,reject) => {
        if(!f.type.includes('image')) {
          this.$message.error('图片格式不正确')
          reject()
          return
        }

        if (this.isCompressImage) {
          const fr = new FileReader()
          fr.readAsDataURL(f)
          fr.onload = (e) => {
            console.log(e)
            let img = new Image()
            img.src = e.target.result
            img.onload = () => {
              if (img.width <= 200) {
                resolve(f)
                return
              }
              let canvas = document.createElement('canvas')
              let ctx = canvas.getContext('2d')
              
              const scale = img.height / img.width
              canvas.width = 200
              canvas.height = 200 * scale
              ctx.drawImage(img, 0, 0, canvas.width, canvas.height)

              const base64 = canvas.toDataURL(f.type)

              let arr = base64.split(','), mime = arr[0].match(/:(.*?);/)[1],
                bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n)
              while (n--) {
                u8arr[n] = bstr.charCodeAt(n)
              }
              const outFile = new File([u8arr], 'image.jpg', { type: mime })
              resolve(outFile)
            }
          }
        }else{
          resolve(f)
        }
        
      })
      

    },
    handleUploadChange(e) {
      console.log(e)
      if (e.file.status == 'uploading') {
        this.uploading = true
      }
      if (e.file.status == 'done') {
        this.uploading = false
        if (e.file.response.code == 1) {
          this.$message.success('上传成功')
          this.page = 1
          this.refreshList()
        }else{
          this.$message.error(e.file.response.msg)
        }
      }
    },
    addImageId(url) {
      if (this.$props.max > 0 && this.imageList.length + 1 > this.$props.max) {
        if (!this.showList) {
          this.imageList.shift()
        }else{
          return
        }
      }
      this.imageList.push(url)
      this.$emit('change', this.imageList, {add: url})
      this.showModel = false
    }
  }
}
</script>

<style>
.component {
  overflow: hidden;
}
.img-item {
  width: 100px;
  height: 100px;
  float: left;
  border: 1px solid #fff;
  margin: 2px;
  padding: 2px;
  cursor: pointer;
}
.img-item:hover {
  border-color: #3c91f7;
}
.img-item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.add-img-item {
  width: 100%;
  height: 100%;
  background: #eee;
  text-align: center;
  display: flex;
  flex-direction: column;
}
.add-icon-holder {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
}
.add-icon {
  font-size: 24px;
}
.img-delete-btn {
  position: relative;
  top: -100%;
  color: #fff;
  background: #f50;
  width: 30px;
  height: 30px;
  text-align: center;
  line-height: 30px;
}
</style>