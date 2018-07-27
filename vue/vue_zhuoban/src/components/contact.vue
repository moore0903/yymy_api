<template lang="html">
  <div class="footer_wrapper" id="contact" style="position:relative;top:0;">
    <!--<a name="contact" href="javascript:void(0)"></a>-->
    <!--<div class="wid-hei-100">
  				<img src="static/img/cai00_62.jpg" />
  			</div>-->
    <div class="container">
      <h2 class="item-title text-center" style="margin-top:150px;"> <img src="static/img/lianxi_05.jpg"> </h2>
      <section class="page_section contact">
        <div class="contact_section">
          <div class="row">
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
              <div class="lis-us text-center">
                <img  class="img-responsive"  src="static/img/y01_45.jpg"/> <span>{{tel}}</span>
               </div>
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
              <div class="lis-us text-center"> <img  class="img-responsive"  src="static/img/y01_48.jpg"/> <span>{{qq}}</span> </div>
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
              <div class="lis-us text-center"> <img  class="img-responsive"  src="static/img/y01_51.jpg"/> <span>{{phone}}</span> </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="tunewwh">
          <div style="width:100%;height:600px;border:#ccc solid 1px;font-size:12px" id="XSDFXPage"></div>
          <!-- <img  class="img-responsive"  src="static/img/diru.png"/> -->
          </div>
        </div>
        <div class="row">
          <div class="wh1160">
            <div class="btm-adres">
              <!-- <img  class="img-responsive"  class="btm-i" src="static/img/you_00.png"/> -->
              <span>地址: {{address}} </span>
            </div>
          </div>
        </div>
      </section>
    </div>

    <div class="add-btm">
      <div class="container">
        <div class="wh1160">
          <div class="tu-lis"> <img  class="img-responsive"  :src='app_link'/>
            <h2>官方APP</h2>
          </div>
          <div class="tu-lis"> <img  class="img-responsive"  :src='wechat'/>
            <h2>官方微信</h2>
          </div>
        </div>
      </div>
    </div>
    <div id="banquan">
      <div class="container text-center" > &copy;2018茁伴公司版权所有&nbsp;&nbsp;苏ICP备16057134号-4&nbsp;&nbsp;苏公网安备&nbsp;&nbsp;32010202020077号 </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      tel: '027-50431588',
      qq: '3409169445',
      phone: '15827386239',
      address: '武汉市东湖新技术开发区高新二路22号中国光谷云计算海外高新企业孵化中心2栋16楼',
      app_link: 'static/img/you_0000s_00.png',
      wechat: 'static/img/wechat.jpg'

    }
  }
}
</script>

<style lang="css">
</style>
