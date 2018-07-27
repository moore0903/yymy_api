<template lang="html">
  <div class="hdlb-top hg activetop">
  <section id="hdlb">
    <div class="container">
      <div class="row">
        <div class="huitu"> <a id="fanh" href="javascript:history.go(-1)" class="huitu-child">招贤纳士</a> <a id="hod" class="huitu-child">职位详情</a> </div>
      </div>
      <div class="row">
        <div class="zhilis" style="overflow:hidden;">
          <div class="row">
            <div class="zhiming">{{this.info.position}}<span>发布时间:{{this.info.created_at}}</span></div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
              <div class="xinshuizhi">薪资:{{this.info.salary}}<span class="jiangyan">经验:{{this.info.experience}}</span></div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
              <div class="renshu"> 招聘人数：{{this.info.num}}人 </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
              <div class="xiangadres"> 地点：{{this.info.address}} </div>
            </div>
          </div>
          <div class="row">
            <div class="pr">
              <div class="zhize">岗位职责</div>
                <div style="font-size:18px;" v-html='this.info.duty'>  </div>
              <div class="fenge"></div>
              <div class="renyao">任职要求</div>
                <div style="font-size:18px;" v-html='this.info.need'>  </div>
              <div class="jianshu">（如对职位有兴趣，请将简历发送至：3409169445 @qq.com）</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
</template>

<script>
export default {
  data() {
    return {
      info: ''
    }
  },
  created() {
    this.$http.post('/api/web_site/get_job_info', {
        id: this.$route.params.id
      })
      .then(function(response) {
          var result = this.Decrypt(response)
          if (result.code < 0) {
            alert(this.msg)
          } else {
            this.info = result.data.info
            console.log(this.info)
          }
        }
        .bind(this))
  }

}
</script>

<style lang="css">
</style>
