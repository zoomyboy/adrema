import Vue from 'vue';
import { modules, init } from 'agnoster';
import { Checkbox } from 'js-modules';
import { InertiaApp } from '@inertiajs/inertia-vue'
import store from './store.js';
import 'font-awesome/css/font-awesome.css';

Vue.use(modules);
Vue.use(init);
Vue.use(InertiaApp);
Vue.component('checkbox', Checkbox);

const app = document.getElementById('app')

new Vue({
    render: h => h(InertiaApp, {
        props: {
            initialPage: JSON.parse(app.dataset.page),
            resolveComponent: name => require(`./views/${name}`).default,
        },
    }),
    store
}).$mount(app)
