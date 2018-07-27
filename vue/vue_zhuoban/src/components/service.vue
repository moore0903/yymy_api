<template lang="html">
  <section id="service" style="position:relative;top:0;padding-top:50px;">
    <!--<a name="service" href="javascript:void(0)"></a>-->
    <!--<div class="wid-hei-100">
  				<img src=""/>
  			</div>-->
    <div class="container">
      <h2  id="huiyuanhuodong" class="ie-st center-block">
        <!--<img src="static/img/huihol_03.png"/>-->
        <img src="static/img/w1.png"/> </h2>

      <div class="service_wrapper" v-for='item in this.list'>

        <div class="row pointer">
          <div class="iehover">
            <!-- <a class="clearfix" onclick="javascript:indexdisp=false;"  href="/service_detail"> -->
              <router-link :to="{ name: 'service_detail', params: {id:item.id} }" class="clearfix">
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
              <img class="img-responsive"  src="static/img/two2.png" />
            </div>
            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
              <h3> {{ item.title}} </h3>
                <p> {{ item.detail.substr(0,110)+'<查看详情>' }} </p>
            </div>
            </router-link>
          <!-- </a> -->
         </div>
        </div>

        <!-- <div class="row pointer">
          <div class="iehover">
            <a class="clearfix" onclick="javascript:indexdisp=false;"  href="/service_detail">
            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 text-right">
              <h3> {{ item.title }} </h3>
              <p> {{ item.detail }} </p>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3"> <img  class="img-responsive"  src="static/img/once2.png" /> </div>
            </a>
          </div>
        </div> -->

        <!-- <div class="row ">
          <div class="iehover">
            <a class="clearfix" onclick="javascript:indexdisp=false;"  href="/service_detail">
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 pointer"> <img  class="img-responsive"  src="static/img/three2.png" /> </div>
            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
              <h3> {{ item.title }} </h3>
              <p> {{ item.detail }} </p>
            </div>
            </a>
          </div>
        </div> -->

      </div>
      <div class="row">
        <!-- <a href="demo1_icon.html">
        <button  onclick="javascript:indexdisp=false;"   class="btn center-block btn-radius">了解更多</button>
      </a> -->
      <router-link to="service_list"><button  onclick="javascript:indexdisp=false;"   class="btn center-block btn-radius">了解更多</button></router-link>
    </div>
    </div>
  </section>

</template>

<script>
export default {
  data() {
    return {
      list:''
    }
  },
  created() {
    this.$http.post('/api/web_site/get_home_activites')
      .then(function(response) {
          var result = this.Decrypt(response)
          if (result.code < 0) {
            alert(this.msg)
          } else {
            this.list = result.data.list
            console.log(this.list)
          }
        }
        .bind(this))
  }

}
</script>

<style lang="css">
</style>
