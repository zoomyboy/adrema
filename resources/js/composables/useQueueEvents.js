export default function (siteName, reloadCallback) {
    return {
        startListener: function () {
            window.Echo.channel(siteName).listen('\\App\\Lib\\Events\\ReloadTriggered', () => reloadCallback());
        },
        stopListener() {
            window.Echo.leave(siteName);
        },
    };
}
