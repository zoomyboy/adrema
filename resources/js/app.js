import Vue from 'vue';
import {App as InertiaApp, plugin, Link as ILink} from '@inertiajs/inertia-vue';
import {Inertia} from '@inertiajs/inertia';

import SvgSprite from './components/SvgSprite.js';
import VPages from './components/VPages.vue';
import VLabel from './components/VLabel.vue';
import VBool from './components/VBool.vue';
import Box from './components/Box.vue';
import Heading from './components/Heading.vue';
import IconButton from './components/Ui/IconButton.vue';
import ToolbarButton from './components/Ui/ToolbarButton.vue';
import PageLayout from './components/Page/Layout.vue';
import AppLayout from './layouts/AppLayout.vue';
import VTooltip from 'v-tooltip';
import hasModule from './mixins/hasModule.js';
import hasFlash from './mixins/hasFlash.js';
import PortalVue from 'portal-vue';
import axios from 'axios';
import VueAxios from 'vue-axios';
import Toasted from 'vue-toasted';
import {createPinia, PiniaVuePlugin} from 'pinia';

Vue.use(plugin);
Vue.use(PortalVue);
Vue.use(VTooltip);
Vue.use(Toasted);
Vue.use(VueAxios, axios);
Vue.use(PiniaVuePlugin);
Vue.component('f-text', () => import(/* webpackChunkName: "form" */ './components/FText'));
Vue.component('f-switch', () => import(/* webpackChunkName: "form" */ './components/FSwitch'));
Vue.component('f-select', () => import(/* webpackChunkName: "form" */ './components/FSelect'));
Vue.component('f-textarea', () => import(/* webpackChunkName: "form" */ './components/FTextarea'));
Vue.component('SvgSprite', SvgSprite);
Vue.component('VPages', VPages);
Vue.component('v-bool', VBool);
Vue.component('v-label', VLabel);
Vue.component('box', Box);
Vue.component('heading', Heading);
Vue.component('icon-button', IconButton);
Vue.component('toolbar-button', ToolbarButton);
Vue.component('page-layout', PageLayout);
Vue.component('save-button', () => import(/* webpackChunkName: "form" */ './components/SaveButton'));

// ------------------------------ Full components ------------------------------
Vue.component('full-page-heading', () => import(/* webpackChunkName: "full" */ './components/Full/PageHeading.vue'));

// ------------------------------- UI Components -------------------------------
Vue.component('ui-button', () => import(/* webpackChunkName: "ui" */ './components/Ui/Button.vue'));
Vue.component('ui-spinner', () => import(/* webpackChunkName: "ui" */ './components/Ui/Spinner.vue'));

// ----------------------------------- init ------------------------------------
const el = document.getElementById('app');
const pinia = createPinia();

Vue.mixin(hasModule);
Vue.mixin(hasFlash);
Vue.component('ILink', ILink);

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
