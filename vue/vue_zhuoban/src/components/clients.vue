<template lang="html">
  <section class="page_section" id="clients" style="position:relative;top: 0;padding-top:60px;">
    <!--<a name="clients" href="javascript:void(0)"></a>-->
    <!--page_section-->
    <!--<h2  class="item-title">
    <img src="static/img/you01_29.jpg"/>
  </h2>-->
  <!--page_section-->
  <!--<div class="wid-hei-100">
  <img src="static/img/bg_03.png"/>
</div>-->
<div class="container">
  <h2 class="center-block" id="jiamenliucheng" style="margin-top:100px;margin-bottom:55px;"> <img src="static/img/w6.png"/> </h2>
  <div class="wh1200 center-block" id="chrh">
    <div class="lis lis-bg1">
      <h1 class="text-center">01</h1>
      <div class="text-center til">专业市场评估</div>
      <p class="cont"> 帮你评估经济风险，让每一位加盟商可以减少投资风险 </p>
    </div>
    <div class="lis lis-bg2">
      <h1 class="text-center">02</h1>
      <div class="text-center til">专业设计绘图</div>
      <p class="cont"> 更快、更准、更精确的量身定做，设计出精美的效果图 </p>
    </div>
    <div class="lis lis-bg1">
      <h1 class="text-center">03</h1>
      <div class="text-center til">顶级按装团队</div>
      <p class="cont"> 结合图纸和实际地进行精细化按爪给你管理，安装质量有保障 </p>
    </div>
    <div class="lis lis-bg2">
      <h1 class="text-center">04</h1>
      <div class="text-center til">专业营销团队</div>
      <p class="cont"> 对店内员工进行安全管理、店内卫生及设备的基本维护维修培训 </p>
    </div>
    <div class="lis lis-bg1">
      <h1 class="text-center">05</h1>
      <div class="text-center til">免费上门带队</div>
      <p class="cont"> 为客户提供专业免费的带店培训，一对一的指导服务，让你开店轻松无忧 </p>
    </div>
  </div>
</div>
<!--</section>-->
<!--<section class="page_section team" id="team" style="position: relative;top:0;">-->
<!--<div class="wid-hei-100">
<img src="static/img/bg01_03.jpg"/>
</div>-->
<!--main-section team-start-->
<!--<div class="team-top">-->
<div class="container wh1200">
  <h2  class="center-block" id="shengqingjiaru" style="width:330px;height: 120px;margin-top: 130px;">
    <!--<img  src="static/img/shenqingjiaru_03.png"/>-->
    <img src="static/img/w8.png"/> </h2>
    <div class="row iaderz">
      <form action="" method="post" class="form-horizontal" role="form">
        <fieldset id="fiel">
          <div class="form-group">
            <label for="name" class="col-lg-2 col-xs-3	text-right	col-sm-2 control-label"><span style="color:red;position:relative;left:-10px;">*</span>姓名：</label>
            <div class="col-lg-9 col-md-8 col-sm-8 col-xs-7">
              <input type="text" class="form-control" name="name" id="name" v-model='name' placeholder="姓名">
            </div>
          </div>
          <div class="form-group">
            <label for="name" class="col-lg-2 col-xs-3	text-right	col-sm-2 control-label"><span style="color:red;position:relative;left:-10px;">*</span>电话：</label>
            <div class="col-lg-9 col-md-8 col-sm-8 col-xs-7">
              <input type="text" class="form-control" name="phone" id="phone" v-model='phone' placeholder="电话">
            </div>
          </div>
          <div class="form-group">
            <label for="name" class="col-lg-2 col-xs-3	text-right	col-sm-2 control-label">备注：</label>
            <div class="col-lg-9 col-md-8 col-sm-8 col-xs-7">
              <input type="text" class="form-control" name="remark" id="remark" v-model='remark' placeholder="留言">
            </div>
          </div>
          <div class="row">
            <div class="font"> 提出申请的商家，茁宝会在三个工作日内与联系人联系，请保持联系电话畅通。 </div>
          </div>
          <div class="row">
            <input type="button" id="tijiao" class="btn center-block tijiao" value="提交" @click='submit'/>
          </div>
        </fieldset>
      </form>
    </div>
  </div>
  <!--</div>-->
</section>

</template>

<script>
export default {
  data() {
    return {
      name: '',
      phone: '',
      remark: ''
    }
  },
  methods:{
    submit:function(){
      this.$http.post('/api/web_site/submit_cooperation', {
        name: this.name,
        phone: this.phone,
        remark: this.remark
      })
      .then(function(response) {
        alert('提交成功!')
      })
      .catch(function(error) {
        console.log(error)
      })
    }
  }
}
</script>

<style lang="css">
</style>
