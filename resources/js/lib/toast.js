import Toast, {useToast} from 'vue-toastification';
const toast = useToast();

var interceptor = [
    (config) => {
        return config;
    },
    (err) => {
        if (err.response.status === 422) {
            var errors = err.response.data.errors;
            for (const error in errors) {
                errors[error].forEach((errorMessage) => toast.error(errorMessage));
            }
        }
        return Promise.reject(err);
    },
];

const options = {
    position: 'bottom-right',
};

export {Toast, interceptor, options};
