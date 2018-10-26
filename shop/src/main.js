import Vue from 'vue'

import 'lib-flexible'
import Mint from 'mint-ui';
import 'mint-ui/lib/style.css';
Vue.use(Mint);

import Ydui from 'vue-ydui';
import 'vue-ydui/dist/ydui.base.css';
import 'vue-ydui/dist/ydui.px.css';
Vue.use(Ydui);

import Vuex from 'vuex'
Vue.use(Vuex);

import App from './App'
import router from './router'



Vue.config.productionTip = false

//全局变量title
const title = '11 '
Vue.prototype.$title = title

//vuex
import store from './store';
Vue.prototype.$store = store

router.beforeEach((to, from, next) => {
    if (to.meta.title) {
        document.title = to.meta.title
        store.commit('title',to.meta.title)
    }
    next()
})
/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  components: { App },
  template: '<App/>'
})
