import {defineStore} from 'pinia';

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
            var _self = this;

            window.addEventListener('resize', this.menuListener);
            this.menuListener();

            window.addEventListener('inertiaStart', () => {
                if (!window.matchMedia('(min-width: 1024px)').matches) {
                    _self.visible = false;
                    _self.overflowVisible = false;
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
