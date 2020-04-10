import Vue from 'vue';
import Vuex from 'vuex';
import nav from 'agnoster/store/nav.js';
import links from './store/links.js';
import user from './store/user.js';

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        nav: nav(links, function(link, { rootGetters }) {
            return link.right !== undefined && rootGetters['user/hasRight'](link.right);
        }),
        user,
    }
});
