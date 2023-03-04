export default {
    methods: {
        ['$success'](message) {
            this.$toasted.show(message, {
                position: 'bottom-right',
                duration: 2000,
                type: 'success',
            });
        },
        ['$error'](message) {
            this.$toasted.show(message, {
                position: 'bottom-right',
                duration: 2000,
                type: 'error',
            });
        },
    },
};
