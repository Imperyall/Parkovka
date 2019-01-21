import Vue from 'vue'
import Router from 'vue-router'
import Home from '@/views/Home'
import Admin from '@/views/Admin'
import Trash from '@/views/Trash'
import store from '@/store'

Vue.use(Router)

const router = new Router({
  mode: 'history',
  base: '/parkovka/',
  routes: [
    {
      path: '/',
      name: 'home',
      component: Home
    },
    {
      path: '/admin',
      name: 'admin',
      component: Admin
    },
    {
      path: '/trash',
      name: 'trash',
      component: Trash
    }
  ],
  scrollBehavior: (to, from, savedPosition) => to.hash ? { selector: to.hash } : { x: 0, y: 0 }
})

router.beforeEach((to, from, next) => (to.path == '/admin' || to.path == '/trash') && store.getters.PERMISSION < 2 ? next('/') : next())

export default router