import {defineStore} from 'pinia';
import {router} from '@inertiajs/vue3';

export const menuStore = defineStore('menu', {
    state: () => ({
        visible: false,
        overflowVisible: false,
        tooltipsVisible: false,
    }),
    getters: {
        isShifted: (state) => state.visible || state.overflowVisible,
        hideable: (state) => state.overflowVisible && !state.visible,
    },
    actions: {
        menuListener() {
            var x = window.matchMedia('(min-width: 1024px)');

            if (x.matches && !this.visible) {
                this.visible = true;
                this.overflowVisible = false;
                return;
            }
            if (!x.matches && this.visible) {
                this.visible = false;
                this.overflowVisible = false;
                return;
            }

            this.tooltipsVisible = !window.matchMedia('(min-width: 1280px)').matches;
        },
        startInertiaListener() {
            window.addEventListener('resize', this.menuListener);
            this.menuListener();

            router.on('before', () => {
                if (!window.matchMedia('(min-width: 1024px)').matches) {
                    this.visible = false;
                    this.overflowVisible = false;
                }
            });
        },
        toggle() {
            this.overflowVisible = !this.overflowVisible;
        },
        hide() {
            this.overflowVisible = false;
        },
    },
});
