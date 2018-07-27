<template lang="html">
  <section id="hero_section"  class="top_cont_outer clearfix activetop" style="position:relative;top:0;">
  <!--<a name="hero_section" href="javascript:void(0)"></a>-->
  <div class="hero_wrapper">
    <!--<div class="container">-->
    <!--<div class="hero_section">-->
    <!--<img src="static/img/y01_04.jpg" />-->
    <div id="myCarousel" class="carousel slide">
      <ol class="carousel-indicators">
        <li data-target="#myCarousel" data-slide-to="0" class="active"> </li>
        <li data-target="#myCarousel" data-slide-to="1"> </li>
        <li data-target="#myCarousel" data-slide-to="2"> </li>
      </ol>
      <!-- Carousel items -->
      <div class="carousel-inner text-center">
        <div class="active item"><img class="img-responsive" :src='src[0]' /></div>
        <div class="item"  v-for='item in src'><img class="img-responsive"  :src='item'  /></div>
      </div>
      <!-- Carousel nav -->
      <!--<a class="carousel-control left" href="#myCarousel" data-slide="prev">‹</a>
						    <a class="carousel-control right" href="#myCarousel" data-slide="next">›</a>-->
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
      src: [
        'static/img/banner_01.jpg',
        'static/img/banner_02.jpg',
        'static/img/banner_01.jpg',
      ]
    }
  }
}
</script>

<style lang="css">
</style>
