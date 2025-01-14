import {ref, inject, onBeforeUnmount} from 'vue';
import {router} from '@inertiajs/vue3';
import useQueueEvents from './useQueueEvents.js';

export function useApiIndex(firstUrl, siteName = null) {
    const axios = inject('axios');

    if (siteName !== null) {
        var {startListener, stopListener} = useQueueEvents(siteName, () => reload());
    }

    const single = ref(null);

    const url = ref(firstUrl);
    const inner = {
        data: ref([]),
        meta: ref({}),
    };

    async function reload(resetPage = true, p = {}) {
        var params = {
            page: resetPage ? 1 : inner.meta.value.current_page,
            ...p,
        };

        var response = (await axios.get(url.value, {params})).data;
        inner.data.value = response.data;
        inner.meta.value = response.meta;
    }

    async function reloadPage(page, p = {}) {
        inner.meta.value.current_page = page;
        await reload(false, p);
    }

    function create() {
        single.value = JSON.parse(JSON.stringify(inner.meta.value.default));
    }

    function edit(model) {
        single.value = JSON.parse(JSON.stringify(model));
    }

    async function submit() {
        single.value.id ? await axios.patch(single.value.links.update, single.value) : await axios.post(inner.meta.value.links.store, single.value);
        await reload();
        single.value = null;
    }

    async function remove(model) {
        await axios.delete(model.links.destroy);
        await reload();
    }

    function can(permission) {
        return inner.meta.value.can[permission];
    }

    function toFilterString(data) {
        return btoa(encodeURIComponent(JSON.stringify(data)));
    }

    function requestCallback(successMessage, failureMessage) {
        return {
            onSuccess: () => {
                this.$success(successMessage);
                reload(false);
            },
            onFailure: () => {
                this.$error(failureMessage);
                reload(false);
            },
            preserveState: true,
        };
    }

    function cancel() {
        single.value = null;
    }

    function updateUrl(newUrl) {
        url.value = newUrl;
    }

    if (siteName !== null) {
        startListener();
        onBeforeUnmount(() => stopListener());
    }

    return {
        data: inner.data,
        meta: inner.meta,
        single,
        create,
        edit,
        reload,
        reloadPage,
        can,
        requestCallback,
        router,
        submit,
        remove,
        cancel,
        axios,
        toFilterString,
        updateUrl,
        url,
    };
}
