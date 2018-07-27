<template lang="html">
  <div class="hdlb-top  activetop" id="liebiao">
  <section id="hdlb">
    <div class="container">
      <div class="row">
        <div class="huitu">
          <a id="fanh" href="javascript:history.go(-1)" class="huitu-child">会员活动</a>
          <a id="hod" class="huitu-child">活动列表</a>
        </div>
      </div>

      <div class="row" v-for='item in this.list'>
        <router-link :to="{ name: 'service_detail', params: {id:item.id} }">
        <div class="wr">
          <div class="coverwh clearfix">
            <h4>{{ item.title }}</h4>
            <div class="time"> {{ item.time }} </div>
          </div>
          <p> {{ item.detail.substr(0,100)+'<查看全文>' }} </p>
        </div>
        </router-link>
      </div>


    </div>
  </section>
</div>
</template>

<script>
export default {
  data() {
    return {
      list:''
    }
  },
  created() {
    this.$http.post('/api/web_site/get_activites')
      .then(function(response) {
          var result = this.Decrypt(response)
          if (result.code < 0) {
            alert(this.msg)
          } else {
            this.list = result.data.list
          }
        }
        .bind(this))
  }

}
</script>

<style lang="css">
</style>
