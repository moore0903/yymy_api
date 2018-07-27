<template lang="html">
  <section id="aboutUs" class="clearfix" style="position: relative;top: 0;padding-top:80px;">
    <!--<a name="aboutUs" href="javascript:void(0)"></a>-->
    <!--<a name="aboutUs" href="#"></a>-->
    <!--Aboutus-->
    <!--<div class="container">-->
    <!--<div class="row">-->
    <div id="newwidth" class="clearfix">
      <div class="wh42" style="float:left;display:inline-block;">
        <div class="sty1 bg1 tu"> <ins></ins> </div>
      </div>
      <div class="wh58" style="float:left;display:inline-block;">
        <div class="sty1 bg2">
          <p v-html="introduce"></p>
        </div>
      </div>
      <!--</div>-->
      <!--<div class="row">-->
      <div class="wh42 homecont" style="float:left;">
        <div class="sty1 bg4 tu"> <ins class="w2"></ins> </div>
      </div>
      <div class="wh58" style="float:left;display:inline-block;">
        <div class="sty1 bg3">
          <p v-html="hope"></p>
        </div>
      </div>
      <div class="wh42 homecontbox" style="float:left;">
        <div class="sty1 bg4 tu"> <ins class="w2"></ins> </div>
      </div>
      <!--</div>-->
      <!--<div class="row">-->
      <div class="wh42"  style="float:left;display:inline-block;">
        <div class="sty1 bg5 tu"> <ins class="w3"></ins> </div>
      </div>
      <div class="wh58" style="float:left;display:inline-block;">
        <div class="sty1 bg6">
          <p v-html="business"></p>
        </div>
      </div>
      <!--</div>-->
      <!--</div>-->
    </div>
  </section>

</template>

<script>
export default {
  data() {
    return {
      introduce: '<span><span>武汉茁伴乐园科技有限公司</span>是一家专注于亲子活动，儿童游乐，儿童成长服务领域，提供优质本土化儿童成长陪伴服务的公司。公司总部位于湖北武汉，正式成立于2018年2月，注册资本金1000万元，致力于专注0-12岁儿童的健康成长，以多元化的儿童游乐，成长陪伴为核心，以现在自营店，加盟店，合作上为依托，以线上App为扭带，为家长提供更全面、更及时、更优质的亲子成长陪伴服务。</span>',
      hope: ' <span>我们倾力于儿童成长服务领域，为0-12岁儿童的家庭提供即儿童游乐场所、亲子活动、幼儿教育、主题餐饮、儿童游泳馆及母婴用品店于一体的亲子服务，致力于让中国孩子享受健康快乐的成长，引领儿童消费服务与陪伴成长新观念。</span> ',
      business: ' <span>第一家茁伴乐园位于湖北省十堰市，打造成为集时尚简餐为一体的超700㎡梦幻游乐空间，带来最炫酷的快乐体验!定于6月1日正式开业，此外，在武汉、十堰、孝感等十余家店正是筹备策划中，力求打造成为一个高品质的游玩场所。</span> '
    }
  }
}
</script>

<style lang="css">
</style>
