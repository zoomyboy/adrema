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

// ---------------------------------- Plugins ----------------------------------
Vue.use(plugin);
Vue.use(PortalVue);
Vue.use(VTooltip);
Vue.use(Toasted);
Vue.use(VueAxios, axios);
Vue.use(PiniaVuePlugin);

Vue.component('SvgSprite', () => import('./components/SvgSprite.js'));
Vue.component('ILink', ILink);

// ---------------------------------- mixins -----------------------------------
Vue.mixin(hasModule);
Vue.mixin(hasFlash);

// -------------------------------- Components ---------------------------------
requireModules(require.context('./components/form', false, /\.vue$/, 'lazy'), Vue, 'f');
requireModules(require.context('./components/ui', false, /\.vue$/, 'lazy'), Vue, 'ui');
requireModules(require.context('./components/page', false, /\.vue$/, 'lazy'), Vue, 'page');

// ----------------------------------- init ------------------------------------
const el = document.getElementById('app');
const pinia = createPinia();

Inertia.on('start', (event) => window.dispatchEvent(new Event('inertiaStart')));

new Vue({
    pinia,
    render: (h) =>
        h(InertiaApp, {
            props: {
                initialPage: JSON.parse(el.dataset.page),
                resolveComponent: async (name) => {
                    var page = (await import(`./views/${name}`)).default;

                    if (page.layout === undefined) {
                        page.layout = AppLayout;
                    }
                    return page;
                },
            },
        }),
}).$mount(el);
