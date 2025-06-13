import { ref, inject, computed, onBeforeUnmount } from 'vue';
import { router } from '@inertiajs/vue3';
import useQueueEvents from './useQueueEvents.js';

export function useIndex(props, siteName) {
    const axios = inject('axios');
    const { startListener, stopListener } = useQueueEvents(siteName, () => reload(false));
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

    function reload(resetPage = true, data) {
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
                inner.meta.value = {
                    ...inner.meta.value,
                    ...page.props.data.meta,
                };
            },
        });
    }

    function reloadPage(page) {
        reload(false, { page: page });
    }

    function can(permission) {
        return inner.meta.value.can[permission];
    }

    function getFilter(value) {
        return inner.filter.value[value];
    }

    function setFilter(key, value) {
        inner.filter.value[key] = value;
        reload(true);
    }

    function setFilterObject(o) {
        inner.filter.value = o;
        reload(true);
    }

    startListener();
    onBeforeUnmount(() => stopListener());

    return {
        data: inner.data,
        can,
        getFilter,
        setFilter,
        meta: inner.meta,
        filterString,
        router,
        toFilterString,
        reloadPage,
        axios,
        filter: inner.filter,
        setFilterObject,
    };
}

const indexProps = {
    data: {
        default: () => {
            return { data: [], meta: {} };
        },
        type: Object,
    },
};

export { indexProps };
