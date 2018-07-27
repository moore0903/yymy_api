<template lang="html">
  <div class="hdlb-top daitu activetop">
  <section id="hdlb">
    <div class="container">
      <div class="row">
        <div class="huitu"> <a id="fanh" href="javascript:history.go(-1)" class="huitu-child">会员活动</a> <a id="hod" class="huitu-child">活动详情</a> </div>
      </div>
      <div class="row">
        <h3 class="xiangtop" v-html='this.info.title'></h3>
      </div>
      <div class="row">
        <div class="xiangfa"> 发布者：管理员|发布时间：{{this.info.created_at}} </div>
      </div>

      <div class="row">
        <p class="xiangbt-p" v-html='this.info.detail'>  </p>
      </div>

    </div>
  </section>
</div>
</template>

<script>
export default {
  data(){
    return{
      info:''
    }
  },
  created() {
    this.$http.post('/api/web_site/get_activity_info',{id:this.$route.params.id})
      .then(function(response) {
          var result = this.Decrypt(response)
          console.log(result)
          if (result.code < 0) {
            alert(this.msg)
          } else {
            this.info = result.data.info
          }
        }
        .bind(this))
  }

}
</script>

<style lang="css">
</style>
