import {computed, ref, inject, onBeforeUnmount} from 'vue';
import {router} from '@inertiajs/vue3';
import useQueueEvents from './useQueueEvents.js';

export function useIndex(props, siteName) {
    const axios = inject('axios');
    const {startListener, stopListener} = useQueueEvents(siteName, () => reload(false));
    const single = ref(null);
    const rawProps = JSON.parse(JSON.stringify(props));
    const inner = {
        data: ref(rawProps.data),
        meta: ref(rawProps.meta),
        filter: ref(rawProps.meta.filter ? rawProps.meta.filter : {}),
    };

    function toFilterString(data) {
        return btoa(encodeURIComponent(JSON.stringify(data)));
    }

    const filterString = computed(() => toFilterString(inner.filter.value));

    function reload(resetPage = true, withMeta = true, data) {
        data = {
            filter: filterString.value,
            page: resetPage ? 1 : inner.meta.value.current_page,
            ...data,
        };

        router.visit(window.location.pathname, {
            data,
            preserveState: true,
            only: ['data'],
            onSuccess: (page) => {
                inner.data.value = page.props.data.data;
                if (withMeta) {
                    inner.meta.value = {
                        ...inner.meta.value,
                        ...page.props.data.meta,
                    };
                }
            },
        });
    }

    function reloadPage(page) {
        reload(false, true, {page: page});
    }

    function can(permission) {
        return inner.meta.value.can[permission];
    }

    function create() {
        single.value = JSON.parse(JSON.stringify(inner.meta.value.default));
    }

    function edit(model) {
        single.value = JSON.parse(JSON.stringify(model));
    }

    async function submit() {
        single.value.id ? await axios.patch(single.value.links.update, single.value) : await axios.post(inner.meta.value.links.store, single.value);
        reload();
        single.value = null;
    }

    async function remove(model) {
        await axios.delete(model.links.destroy);
        reload();
    }

    function can(permission) {
        return inner.meta.value.can[permission];
    }

    function cancel() {
        single.value = null;
    }

    function getFilter(value) {
        return inner.filter.value[value];
    }

    function setFilter(key, value) {
        inner.filter.value[key] = value;
        reload(true);
    }

    startListener();
    onBeforeUnmount(() => stopListener());

    return {
        data: inner.data,
        meta: inner.meta,
        single,
        create,
        edit,
        reload,
        reloadPage,
        can,
        router,
        submit,
        remove,
        cancel,
        axios,
        setFilter,
        getFilter,
    };
}

const indexProps = {
    data: {
        default: () => {
            return {data: [], meta: {}};
        },
        type: Object,
    },
};

export {indexProps};
