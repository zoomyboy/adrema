import {ref, inject, computed, onBeforeUnmount} from 'vue';
import {router} from '@inertiajs/vue3';
import useQueueEvents from './useQueueEvents.js';

export function useIndex(props, siteName) {
    const axios = inject('axios');
    const {startListener, stopListener} = useQueueEvents(siteName, () => reload(false));
    const rawProps = JSON.parse(JSON.stringify(props));
    const inner = {
        data: ref(rawProps.data),
        meta: ref(rawProps.meta),
    };

    function toFilterString(data) {
        return btoa(encodeURIComponent(JSON.stringify(data)));
    }

    const filterString = computed(() => toFilterString(inner.meta.value.filter));

    function reload(resetPage = true, withMeta = true, data) {
        var data = {
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

    function getFilter(value) {
        return inner.meta.value.filter[value];
    }

    function setFilter(key, value) {
        inner.meta.value.filter[key] = value;
        reload(true, false);
    }

    startListener();
    onBeforeUnmount(() => stopListener());

    return {
        data: inner.data,
        reload,
        can,
        getFilter,
        setFilter,
        meta: inner.meta,
        filterString,
        router,
        toFilterString,
        reloadPage,
        axios,
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
