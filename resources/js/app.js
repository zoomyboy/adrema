import Vue from 'vue';
import { App as InertiaApp, plugin } from '@inertiajs/inertia-vue'
import 'font-awesome/css/font-awesome.css';
import Echo from 'laravel-echo';
window.io = require('socket.io-client');
import Sprite from './components/Sprite.js';

import FText from './components/FText.vue';
import FSelect from './components/FSelect.vue';
import FTextarea from './components/FTextarea.vue';
import Pages from './components/Pages.vue';
import VBool from './components/VBool.vue';
import App from './layouts/App.vue';

Vue.use(plugin)
Vue.component('f-text', FText);
Vue.component('f-select', FSelect);
Vue.component('f-textarea', FTextarea);
Vue.component('sprite', Sprite);
Vue.component('pages', Pages);
Vue.component('v-bool', VBool);

const el = document.getElementById('app')

new Vue({
    render: h => h(InertiaApp, {
        props: {
            initialPage: JSON.parse(el.dataset.page),
            resolveComponent: name => {
                var page = require(`./views/${name}`).default;

                if (page.layout === undefined) {
                    page.layout = App;
                }
                return page;
            }
        },
    }),
}).$mount(el);

