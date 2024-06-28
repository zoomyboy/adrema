import Pusher from 'pusher-js';
import Echo from 'laravel-echo';
import {useToast} from 'vue-toastification';

const toast = useToast();

window.Pusher = Pusher;

function handleJobEvent(event, type = 'success') {
    if (event.message) {
        toast[type](event.message);
    }
}

var echo = new Echo({
    broadcaster: 'pusher',
    key: 'adremakey',
    wsHost: window.location.hostname,
    wsPort: import.meta.env.MODE === 'development' ? 6001 : 80,
    wssPort: import.meta.env.MODE === 'development' ? 6001 : 443,
    forceTLS: false,
    disableStats: true,
    cluster: 'adrema',
    enabledTransports: ['ws', 'wss'],
});

echo.channel('jobs')
    .listen('\\App\\Lib\\Events\\JobStarted', (e) => handleJobEvent(e, 'success'))
    .listen('\\App\\Lib\\Events\\JobFinished', (e) => handleJobEvent(e, 'success'))
    .listen('\\App\\Lib\\Events\\JobFailed', (e) => handleJobEvent(e, 'error'))
    .listen('\\App\\Lib\\Events\\Succeeded', (e) => handleJobEvent(e, 'success'));

export default echo;
