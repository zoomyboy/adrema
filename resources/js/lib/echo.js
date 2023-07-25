import Pusher from 'pusher-js';
import Echo from 'laravel-echo';

window.Pusher = Pusher;
export default new Echo({
    broadcaster: 'pusher',
    key: 'adremakey',
    wsHost: window.location.hostname,
    wsPort: 6001,
    forceTLS: false,
    disableStats: true,
    cluster: 'adrema',
    enabledTransports: ['ws', 'wss'],
});
