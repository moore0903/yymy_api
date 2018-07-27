<template lang="html">
  <section id="Portfolio" class="content" style="position:relative;top:0;">
    <!--<a name="Portfolio" href="javascript:void(0)"></a>-->
    <!-- Container -->
    <div class="pro-t">
      <!--<div class="wid-hei-100">
  					<img src="static/img/images/000_05.jpg"/>
  				</div>-->
      <div class="container portfolio_title">

        <!-- Title -->
        <h2 class="center-block" id="mendianchaxun"> <img src="static/img/w5.png"/> </h2>
        <!--/Title -->

      </div>
      <div class="container" id="address">
        <div class="wh1200">

          <div class="wh50" id='first' @click='changeMap1'>
            <h4 class="text-left">门店一：</h4>
            <div class="address">地址：十堰市白浪经济开发区马路小学斜对面 &nbsp;小天才儿童乐园内</div>
            <div class="tel">电话：0719-*******</div>
          </div>

          <div class="wh50" id='second' @click='changeMap2'>
            <h4 class="text-left">门店二：</h4>
            <div class="address">地址：十堰市白浪经济开发区马路小学斜对面 &nbsp;小天才儿童乐园内</div>
            <div class="tel">电话：0719-*******</div>
          </div>

        </div>
      </div>
    </div>
    <div class="container">
      <div style="width:100%;height:600px;border:#ccc solid 1px;font-size:12px" id="map1" :lon='lon' :lat='lat'></div>
    </div>
  </section>

</template>

<script>
export default {
  data(){
    return{
      lon:'',
      lat:''
    }
  },
  methods:{
    changeMap1:function(lon,lat){
      this.lon='114.428696'
      this.lat='30.48317'
      // console.log(this.lon,this.lat)
    },
    changeMap2:function(lon,lat){
      this.lon='124.428696'
      this.lat='40.48317'
      // console.log(this.lon,this.lat)

    }
  }

}
</script>

<style lang="css">
</style>
