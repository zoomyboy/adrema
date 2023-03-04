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
        errorsFromException(e) {
            if (e.response?.status !== 422 || !e.response?.data?.errors) {
                throw e;
            }

            var errors = e.response.data.errors;

            Object.keys(errors).forEach((field) => {
                errors[field].forEach((message) => {
                    this.$error(message);
                });
            });
        },
    },
};
