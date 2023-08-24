import {useToast} from 'vue-toastification';
const toast = useToast();

function handleJobEvent(event, type = 'success', reloadCallback) {
    if (event.message) {
        toast[type](event.message);
    }
    if (event.reload) {
        reloadCallback();
    }
}

export default function (siteName, reloadCallback) {
    return {
        startListener: function () {
            window.Echo.channel('jobs').listen('\\App\\Lib\\Events\\ClientMessage', (e) => handleJobEvent(e, 'success', reloadCallback));
            window.Echo.channel(siteName)
                .listen('\\App\\Lib\\Events\\JobStarted', (e) => handleJobEvent(e, 'success', reloadCallback))
                .listen('\\App\\Lib\\Events\\JobFinished', (e) => handleJobEvent(e, 'success', reloadCallback))
                .listen('\\App\\Lib\\Events\\JobFailed', (e) => handleJobEvent(e, 'error', reloadCallback));
        },
        stopListener() {
            window.Echo.leave(siteName);
            window.Echo.leave('jobs');
        },
    };
}
