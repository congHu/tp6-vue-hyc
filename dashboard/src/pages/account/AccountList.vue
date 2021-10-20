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
      <a-col :span="12">
        <!-- <a-popconfirm :title="'删除选中'+selectedKeys.length+'条?'" :disabled="selectedKeys.length==0" @confirm="deleteMany(selectedKeys)">
          <a-button type="danger" :style="{marginLeft: '8px'}" :disabled="selectedKeys.length==0">删除</a-button>
        </a-popconfirm> -->
      </a-col>
      <a-col :span="12" :style="{ textAlign: 'right' }">
        <a-button @click="$router.push('account/save')">添加</a-button>
      </a-col>
    </a-row>

    <a-spin :spinning="loading">
      <a-table
        :columns="columns"
        :data-source="data"
        rowKey="id"
        :pagination="{total: totalRow, pageSize: pageSize}"
        @change="tableChange"
        :scroll="{x:1000}"
      >
        <span slot="status" slot-scope="item">
          <a-tag :color="item.status==1?'green':'red'">
            <span v-if="item.status == 0">
              禁用
            </span>
            <span v-else-if="item.status == 1">
              启用
            </span>
          </a-tag>
        </span>
        <span slot="action" slot-scope="item">
          <a-button @click="$router.push('account/save?id='+item.id)">编辑</a-button>
          <a-popconfirm title="删除？" @confirm="deleteOne(item.id)">
            <a-button type="danger" :style="{marginLeft: '8px'}">删除</a-button>
          </a-popconfirm>
        </span>
      </a-table>
    </a-spin>
  </a-layout>
</template>

<script>
import { list, deleteOne } from "@/api/account"

export default {
  data() {
    return {
      form: this.$form.createForm(this, { name: "advanced_search" }),
      searchParams: [
        {
          name: "nickname",
          label: "昵称",
        },
      ],
      loading: false,
      data: [],
      columns: [
        {
          title: "用户名",
          dataIndex: "username",
        },
        {
          title: "昵称",
          dataIndex: "nickname",
        },
        {
          title: "角色",
          dataIndex: "role.name",
        },
        {
          title: "创建时间",
          dataIndex: "create_time",
        },
        {
          title: "状态",
          key: "status",
          scopedSlots: { customRender: "status" },
        },
        {
          title: "操作",
          key: "action",
          scopedSlots: { customRender: "action" },
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
    deleteOne(id) {
      deleteOne({id}).then(res => {
        if (res.data) this.$message.success('删除成功')
        this.refreshList()
      }).catch(err => {
        this.$message.error(err.msg ? err.msg : err.message)
        if (err.code == 10001) {
          this.$router.push('/login')
          return
        }
      })
    }
  },
};
</script>

<style>
</style>