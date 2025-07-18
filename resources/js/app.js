import { createApp, h, defineAsyncComponent } from 'vue';
import { Head, createInertiaApp, Link as ILink } from '@inertiajs/vue3';
import axios from 'axios';
import VueAxios from 'vue-axios';
import { Plugin as FloatingVue, options as FloatingVueOptions } from './lib/floatingVue.js';
import { createPinia, PiniaVuePlugin } from 'pinia';
import Echo from './lib/echo.js';

import AppLayout from './layouts/AppLayout.vue';
import hasModule from './mixins/hasModule.js';
import hasFlash from './mixins/hasFlash.js';
import { Toast, options as toastOptions, interceptor as toastInterceptor } from './lib/toast.js';

// ---------------------------------- Assets -----------------------------------
import '../css/app.css';
import 'vue-toastification/dist/index.css';
import.meta.glob(['../img/**']);

// ----------------------------------- init ------------------------------------
const pinia = createPinia();
var views = import.meta.glob('./views/**/*.vue');

axios.interceptors.response.use(...toastInterceptor);
window.Echo = Echo;

createInertiaApp({
    title: (title) => `${title} | Adrema`,
    resolve: async (name) => {
        var page = (await views[`./views/${name}.vue`]()).default;

        if (page.layout === undefined) {
            page.layout = AppLayout;
        }

        return page;
    },
    setup({ el, App, props, plugin }) {
        var app = createApp({ pinia, render: () => h(App, props) })
            .use(plugin)
            .use(FloatingVue, FloatingVueOptions)
            .use(Toast, toastOptions)
            .use(VueAxios, axios)
            .use(PiniaVuePlugin)
            .component('ILink', ILink)
            .component('Head', Head)
            .mixin(hasModule)
            .mixin(hasFlash);

        app.component(
            'FSinglefile',
            defineAsyncComponent(() => import('!/medialibrary-helper/assets/components/SingleFile.vue'))
        );
        app.component(
            'FMultiplefiles',
            defineAsyncComponent(() => import('!/medialibrary-helper/assets/components/MultipleFiles.vue'))
        );

        app.provide('axios', app.config.globalProperties.axios);
        app.mount(el);
    },
});
