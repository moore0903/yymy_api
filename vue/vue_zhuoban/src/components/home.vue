<template>
<div id="Home">
  <hero_section></hero_section>
  <service></service>
  <aboutUs></aboutUs>
  <portfolio></portfolio>
  <clients></clients>
  <team></team>
  <contact></contact>
  <bmap></bmap>
  <map></map>

  <!-- <service_detail v-show='false'></service_detail> -->
</div>
</template>

<script>
import hero_section from './hero_section'
import service from './service'
import aboutUs from './aboutUs'
import portfolio from './portfolio'
import clients from './clients'
import team from './team'
import contact from './contact'
import service_list from './service_list'
import service_detail from './service_detail'
import team_detail from './team_detail'
import bmap from './bmap'
import map from './map'

export default {
  name: 'Home',
  components: {
    hero_section,
    service,
    aboutUs,
    portfolio,
    clients,
    team,
    contact,
    service_list,
    service_detail,
    team_detail,
    bmap,
    map
  },
  // methods:{
  //   isShow:function(){
  //     false
  //   }
  // }
}
</script>
