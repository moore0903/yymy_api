import Vue from 'vue'
import Router from 'vue-router'
import home from '@/components/home'
// import header from '@/components/header'
import hero_section from '@/components/hero_section'
// import service from '@/components/service'
// import aboutUs from '@/components/aboutUs'
// import portfolio from '@/components/portfolio'
// import clients from '@/components/clients'
// import team from '@/components/team'
// import contact from '@/components/contact'
import service_list from '@/components/service_list'
import service_detail from '@/components/service_detail'
import team_detail from '@/components/team_detail'

Vue.use(Router)

export default new Router({
  mode:'history',
  routes: [
    {
      path: '/',
      name: 'home',
      component: home
    },
    // {
    //   path: '/header',
    //   name: 'header',
    //   component: header
    // },
    {
      path: '/hero_section',
      name: 'hero_section',
      component: hero_section
    },
    // {
    //   path: '/service',
    //   name: 'service',
    //   component: service
    // },
    // {
    //   path: '/aboutUs',
    //   name: 'aboutUs',
    //   component: aboutUs
    // },
    // {
    //   path: '/portfolio',
    //   name: 'portfolio',
    //   component: portfolio
    // },
    // {
    //   path: '/clients',
    //   name: 'clients',
    //   component: clients
    // },
    // {
    //   path: '/team',
    //   name: 'team',
    //   component: team
    // },
    // {
    //   path: '/contact',
    //   name: 'contact',
    //   component: contact
    // },
    {
      path: '/service_list',
      name: 'service_list',
      component: service_list
    },
    {
      path: '/service_detail',
      name: 'service_detail',
      component: service_detail
    },
    {
      path: '/team_detail',
      name: 'team_detail',
      component: team_detail
    },
  ]
})
