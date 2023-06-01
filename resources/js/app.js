import Vue from 'vue';
import {App as InertiaApp, plugin, Link as ILink} from '@inertiajs/inertia-vue';
import {Inertia} from '@inertiajs/inertia';
import PortalVue from 'portal-vue';
import axios from 'axios';
import VueAxios from 'vue-axios';
import Toasted from 'vue-toasted';
import VTooltip from 'v-tooltip';
import {createPinia, PiniaVuePlugin} from 'pinia';
import requireModules from './lib/requireModules.js';

import AppLayout from './layouts/AppLayout.vue';
import hasModule from './mixins/hasModule.js';
import hasFlash from './mixins/hasFlash.js';

import '../css/app.css';

// ---------------------------------- Assets -----------------------------------
import.meta.glob(['../img/**']);

// ---------------------------------- Plugins ----------------------------------
Vue.use(plugin);
Vue.use(PortalVue);
Vue.use(VTooltip);
Vue.use(Toasted);
Vue.use(VueAxios, axios);
Vue.use(PiniaVuePlugin);

Vue.component('SvgSprite', () => import('./components/SvgSprite.js'));
Vue.component('ILink', ILink);

// -------------------------------- Components ---------------------------------
requireModules(import.meta.glob('./components/form/*.vue'), Vue, 'f');
requireModules(import.meta.glob('./components/ui/*.vue'), Vue, 'ui');
requireModules(import.meta.glob('./components/page/*.vue', {eager: true}), Vue, 'page');

// ---------------------------------- mixins -----------------------------------
Vue.mixin(hasModule);
Vue.mixin(hasFlash);

// ----------------------------------- init ------------------------------------
const el = document.getElementById('app');
const pinia = createPinia();

Inertia.on('start', (event) => window.dispatchEvent(new Event('inertiaStart')));

let views = import.meta.glob('./views/**/*.vue');
new Vue({
    pinia,
    render: (h) =>
        h(InertiaApp, {
            props: {
                initialPage: JSON.parse(el.dataset.page),
                resolveComponent: async (name) => {
                    var page = (await views[`./views/${name}.vue`]()).default;

                    if (page.layout === undefined) {
                        page.layout = AppLayout;
                    }
                    return page;
                },
            },
        }),
}).$mount(el);
