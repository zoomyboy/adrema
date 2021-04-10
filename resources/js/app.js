import Vue from 'vue';
import { App, plugin } from '@inertiajs/inertia-vue'
import 'font-awesome/css/font-awesome.css';
import Echo from 'laravel-echo';
window.io = require('socket.io-client');

import FText from './components/FText.vue';

Vue.use(plugin)
Vue.component('f-text', FText);

const el = document.getElementById('app')

new Vue({
  render: h => h(App, {
    props: {
      initialPage: JSON.parse(el.dataset.page),
      resolveComponent: name => require(`./views/${name}`).default,
    },
  }),
}).$mount(el);

