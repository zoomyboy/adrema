import {createApp, h, defineAsyncComponent} from 'vue';
import {createInertiaApp, Link as ILink} from '@inertiajs/vue3';
import axios from 'axios';
import VueAxios from 'vue-axios';
import {Plugin as FloatingVue, options as FloatingVueOptions} from './lib/floatingVue.js';
import {createPinia, PiniaVuePlugin} from 'pinia';
import requireModules from './lib/requireModules.js';

import AppLayout from './layouts/AppLayout.vue';
import hasModule from './mixins/hasModule.js';
import hasFlash from './mixins/hasFlash.js';
import {Toast, options as toastOptions, interceptor as toastInterceptor} from './lib/toast.js';

// ---------------------------------- Assets -----------------------------------
import '../css/app.css';
import 'vue-toastification/dist/index.css';
import.meta.glob(['../img/**']);

// ----------------------------------- init ------------------------------------
const pinia = createPinia();
var views = import.meta.glob('./views/**/*.vue');

axios.interceptors.response.use(...toastInterceptor);

createInertiaApp({
    title: (title) => `${title} | Adrema`,
    resolve: async (name) => {
        var page = (await views[`./views/${name}.vue`]()).default;

        if (page.layout === undefined) {
            page.layout = AppLayout;
        }

        return page;
    },
    setup({el, App, props, plugin}) {
        var app = createApp({pinia, render: () => h(App, props)})
            .use(plugin)
            .use(FloatingVue, FloatingVueOptions)
            .use(Toast, toastOptions)
            .use(VueAxios, axios)
            .use(PiniaVuePlugin)
            .component('ILink', ILink)
            .mixin(hasModule)
            .mixin(hasFlash);

        requireModules(import.meta.glob('./components/form/*.vue'), app, 'f');
        requireModules(import.meta.glob('./components/ui/*.vue'), app, 'ui');
        requireModules(import.meta.glob('./components/page/*.vue', {eager: true}), app, 'page');

        app.mount(el);
    },
});
