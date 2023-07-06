import { useToast } from 'vue-toastification'
const toast = useToast()

export default {
    methods: {
        ['$success'](message) {
            toast.success(message);
        },
        ['$error'](message) {
            toast.error(message);
        },
        errorsFromException(e) {
            if (e.response?.status !== 422 || !e.response?.data?.errors) {
                throw e;
            }

            var errors = e.response.data.errors;

            Object.keys(errors).forEach((field) => {
                errors[field].forEach((message) => {
                    toast.error(message);
                });
            });
        },
    },
};
