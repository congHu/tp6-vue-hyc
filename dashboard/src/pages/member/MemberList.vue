<template>
  <a-layout>
    <a-form
      :labelCol="{ span: 8 }"
      :wrapperCol="{ span: 16 }"
      class="ant-advanced-search-form"
      :form="form"
      @submit="handleSearch"
    >
      <a-row :gutter="24">
        <a-col :md="20" :xs="24">
          <a-row :gutter="24">
            <a-col v-for="item in searchParams" :key="item.name" :md="8" :xs="24">
              <a-form-item :label="item.label">
                <a-cascader v-if="item.name == 'category'"
                  v-decorator="[item.name]"
                  :options="categories"
                  :field-names="{ label: 'category_name', value: 'id', children: 'children' }"
                  placeholder="请选择"
                />
                <!-- v-model="selectCategory" -->
                  <!-- @change="handleCascaderChange" -->
                <a-range-picker v-else-if="item.name == 'datepicker'"
                  v-decorator="[
                    'datepicker',
                    {
                      rules: [{ type: 'array'}]
                    }
                  ]"
                  :placeholder="['开始日期','结束日期']"
                />
                <a-select v-else-if="item.name == 'status'"
                  v-decorator="[item.name]"
                  :allowClear="true"
                  :placeholder="item.label"
                >
                  <a-select-option :value="0">
                    未发布
                  </a-select-option>
                  <a-select-option :value="1">
                    已发布
                  </a-select-option>
                </a-select>
                <a-input v-else
                  v-decorator="[
                    item.name,
                    {
                      rules: [],
                    },
                  ]"
                  :placeholder="item.label"
                />
              </a-form-item>
            </a-col>
          </a-row>
        </a-col>

        <a-col :md="4" :xs="24" :style="{ textAlign: 'right' }">
          <a-button type="primary" html-type="submit"> 搜索 </a-button>
          <a-button :style="{ marginLeft: '8px' }" @click="handleReset">
            重置
          </a-button>
          <!-- <a :style="{ marginLeft: '8px', fontSize: '12px' }" @click="toggle">
            Collapse <a-icon :type="expand ? 'up' : 'down'" />
          </a> -->
        </a-col>
      </a-row>
    </a-form>

    <a-row :gutter="24" :style="{ margin: '8px 0' }">
      <a-col :md="12" :xs="24">

      </a-col>
      <a-col :md="12" :xs="24" :style="{ textAlign: 'right' }">
        <span>筛选结果共{{totalRow}}条</span>
        <a-button style="margin-left:8px" @click="exportExcel" :loading="exportLoading">导出</a-button>
      </a-col>
    </a-row>

    <a-spin :spinning="loading">
      <a-table
        :columns="columns"
        :data-source="data"
        rowKey="id"
        :pagination="{current:page, total: totalRow, pageSize: pageSize}"
        @change="tableChange"
        :scroll="{x:800}"
      >
        <span slot="avatar" slot-scope="item">
          <div :style="{ width: '100px', height: '100px', background: '#eee' }">
            <img v-if="item.avatar != ''" :src="item.avatar" :style="{ width: '100%', height: '100%', objectFit: 'cover' }">
          </div>
        </span>
      </a-table>
    </a-spin>
  </a-layout>
</template>

<script>
import { list, edit } from "@/api/member"

export default {
  data() {
    return {
      form: this.$form.createForm(this, { name: "advanced_search" }),
      searchParams: [
        {
          name: "nickname",
          label: "昵称",
        },
        {
          name: "phone",
          label: "手机号",
        },
      ],
      loading: false,
      data: [],
      columns: [
        {
          title: "id",
          dataIndex: "id",
        },
        {
          title: "头像",
          key: "avatar",
          scopedSlots: { customRender: "avatar" },
        },
        {
          title: "昵称",
          dataIndex: "nickname",
        },
        {
          title: "手机号",
          dataIndex: "phone",
        },
        {
          title: "注册时间",
          dataIndex: "create_time",
        },
      ],
      rowSelection: {
        onChange: this.onTableSelectChange,
      },
      selectedKeys: [],
      totalRow: 0,
      pageSize: 20,
      page: 1,
      formValues: {},
      categories: [],
      selectCategory: [],
      exportLoading: false,
    };
  },
  mounted() {
    this.refreshList();
  },
  methods: {
    handleSearch(e) {
      e.preventDefault();
      this.form.validateFields((error, values) => {
        console.log("error", error);
        console.log("Received values of form: ", values);
        if (!error) {
          if (values.datepicker) {
            values.start_time = values.datepicker[0].format("YYYY-MM-DD");
            values.end_time = values.datepicker[1].format("YYYY-MM-DD");
          }

          this.page = 1
          this.formValues = values
          this.refreshList()
        }
      });
    },
    handleReset() {
      this.form.resetFields();
    },
    refreshList() {
      this.loading = true;
      let params = Object.assign({}, this.formValues)
      params.page = this.page
      params.page_size = this.pageSize
      list(params)
        .then((res) => {
          console.log(res);
          // this.data = res
          this.data = res.data;
          this.totalRow = res.total
        })
        .catch((err) => {
          this.$message.error(err.msg ? err.msg : err.message);
          if (err.code == 10001) {
            this.$router.push("/login");
            return;
          }
        })
        .finally(() => {
          this.loading = false;
        });
    },
    onTableSelectChange(selectedKeys, selectedRows) {
      console.log(selectedKeys, selectedRows);
      this.selectedKeys = selectedKeys;
    },
    tableChange(p) {
      this.page = p.current
      this.refreshList()
    },
    getImgUrl(url) {
      return process.env.VUE_APP_API_BASE_URL + 'storage/' + url
    },
    editDelivery(item,status) {
      edit({
        user_id: item.id,
        is_delivery: status
      }).then(res => {
        console.log(res)
        this.$message.success('成功')
        this.refreshList()
      }).catch((err) => {
        this.$message.error(err.msg ? err.msg : err.message);
        if (err.code == 10001) {
          this.$router.push("/login");
          return;
        }
      })
    },
    exportExcel() {
      this.exportLoading = true
      let params = Object.assign({}, this.formValues)
      params.page_size = -1
      list(params).then(res => {
        import('@/utils/Export2Excel').then(excel => {
          const tHeader = [
            'id', '昵称', 'openid', '手机号', '注册时间'
          ];
          const filterVal = [
            'id', 'nickname', 'openid', 'phone',
            'create_time'
          ];
          let lists = res;
          lists = lists.map(item => {
            return {
              id: item.id,
              nickname: item.nickname,
              openid: item.openid,
              phone: item.phone,
              create_time: item.create_time,
            };
          });
          const data = lists.map(v => filterVal.map(j => {
            return v[j]
          }))
          excel.export_json_to_excel({
            header: tHeader,
            data,
            filename: '用户导出',
            autoWidth: true,
            bookType: 'xlsx'
          });
        })
        
      }).catch(err => {
        this.$message.error(err.msg ? err.msg : err.message);
        if (err.code == 10001) {
          this.$router.push("/login");
          return;
        }
      }).finally(() => {
        this.exportLoading = false
      })
    }
  },
};
</script>

<style>
</style>