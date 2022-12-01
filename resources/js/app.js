import Vue from 'vue';
import {App as InertiaApp, plugin, Link as ILink} from '@inertiajs/inertia-vue';

import SvgSprite from './components/SvgSprite.js';
import FText from './components/FText.vue';
import FSwitch from './components/FSwitch.vue';
import FSelect from './components/FSelect.vue';
import FTextarea from './components/FTextarea.vue';
import VPages from './components/VPages.vue';
import VLabel from './components/VLabel.vue';
import VBool from './components/VBool.vue';
import Box from './components/Box.vue';
import Heading from './components/Heading.vue';
import AppLayout from './layouts/AppLayout.vue';
import VTooltip from 'v-tooltip';
import hasModule from './mixins/hasModule.js';

Vue.use(plugin);
Vue.use(VTooltip);
Vue.component('f-text', FText);
Vue.component('f-switch', FSwitch);
Vue.component('f-select', FSelect);
Vue.component('f-textarea', FTextarea);
Vue.component('SvgSprite', SvgSprite);
Vue.component('VPages', VPages);
Vue.component('v-bool', VBool);
Vue.component('v-label', VLabel);
Vue.component('box', Box);
Vue.component('heading', Heading);

const el = document.getElementById('app');

Vue.mixin(hasModule);
Vue.component('ILink', ILink);

new Vue({
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
