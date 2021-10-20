<template>
  <div></div>
</template>

<script>
export default {
  mounted() {
    let that = this
    this.$warning({
      title: '抱歉，无法访问该页面',
      onOk() {
        that.$router.push('.')
      }
    })
  }
}
</script>

<style>

</style>