/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

import storeX from './store/index'
import Vuex from 'vuex'
Vue.use(Vuex)
const store=new Vuex.Store(storeX)

import VueChatScroll from 'vue-chat-scroll'
Vue.use(VueChatScroll)

Vue.component('main-app', require('./components/MainApp.vue').default);


const app = new Vue({
    el: '#app',
    store,

});
