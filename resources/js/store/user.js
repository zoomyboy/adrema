import collection from 'agnoster/collection.js';

export default {
    namespaced: true,

    modules: {
        collection: collection()
    },

    state: {
        current: {}
    },

    getters: {
        model() {
            return 'user';
        },
        hasRight: (state) => (right) => {
            return state.current.cans[right] === 1;
        },
        messages() {
            return {
                stored: 'Benutzer erstellt.',
                updated: 'Benutzer bearbeitet.',
                destroyed: 'Benutzer gelöscht.'
            };
        }
    },

    mutations: {
        set: (state, data) => {
            state.current = data;
        }
    }
};
