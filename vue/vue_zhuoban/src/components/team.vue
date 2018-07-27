<template lang="html">
  <section id="team" style="padding-top:120px;">
    <!--<a name="team" href="javascript:void(0)"></a>-->
    <div class="team-btm">
      <!--<div class="wid-hei-100">
  				<img src="static/img/wrap_02.jpg" />
  			</div>-->
      <div class="container">
        <h2 class="item-title team-title" style="margin:40px auto 40px;">
          <!--<img src="static/img/you01_37.jpg"/>-->
          <img src="static/img/w7.jpg"/> </h2>
        <p class="wh1160"> 人才是茁伴乐园最宝贵的资源，我们以完善的薪酬福利保障，广阔的职业发展平台，良好的工作环境与每个茁伴人共同成长。 </p>
        <p class="wh1160"> 茁伴乐园诚意征求以下人才，如果你想一展身手，请速与我们联系。 </p>
        <p class="wh1160"> 有意者将简历投递到：3409169445@qq.com。我公司会尽快给予答复，择优录取。 </p>
        <div class="forelement">

          <div class="wh1160 zhaoxiang"  >
            <div class="row" v-for='item in list'>
              <!-- <a class="clearfix" onclick="javascript:indexdisp=false;"  href='/team_detail'> -->
                <router-link :to="{ name: 'team_detail', params: {id:item.id} }" class="clearfix">
              <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                <div class="zhi">职位:{{item.position}}<span class="fashi">发布时间:{{item.created_at}}</span></div>
                <div class="xinshui">薪资:{{item.salary}}
                  <div class="yaoqiu">经验:{{item.experience}}</div>
                </div>
              </div>
              <div class="col-lg-3 col-md-3 col-sm-3 col-xs-4">
                <div class="adres"> 工作地点：{{item.address}} </div>
              </div>
              <div class="col-lg-5 col-md-4 col-sm-5 col-xs-4 pull-right">
                <div class="fuli" v-for='tag in item.tags'>
                  <span class="pull-right">{{tag}}</span>
                </div>
              </div>
              <!-- </a> -->
              </router-link>
            </div>
          </div>

          <!--</div>-->
        </div>
        <div class="row">
          <h5 class="text-center xiajiazai">
            <!--<a id="crelt" href="javascript:void(0);">-->
            <div id="crelt"> <img class="ie-texiao" src="static/img/array/arr_04a.png" /> </div>
            <!--</a>-->
          </h5>
        </div>
      </div>
    </div>
  </section>

</template>

<script>
export default {
  data() {
    return {
      list: ''
    }
  },
  created() {
    this.$http.post('/api/web_site/get_jobs', {
        pagesize: 3
      })
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
