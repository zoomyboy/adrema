export default {
    data: function () {
        return {
            inner: {...this.data},
        };
    },
    props: {
        data: {},
    },
    computed: {
        filterString() {
            return this.toFilterString(this.inner.meta.filter);
        },
    },
    methods: {
        toFilterString(data) {
            return btoa(encodeURIComponent(JSON.stringify(data)));
        },
        reload(resetPage = true) {
            var _self = this;
            var data = {
                filter: this.filterString,
                page: 1,
            };

            data['page'] = resetPage ? 1 : this.inner.meta.current_page;

            this.$inertia.visit(window.location.pathname, {
                data,
                preserveState: true,
                onSuccess(page) {
                    _self.inner = page.props.data;
                },
            });
        },
        can(permission) {
            return this.inner.meta.can[permission];
        },
        getFilter(value) {
            return this.inner.meta.filter[value];
        },
        setFilter(key, value) {
            this.inner.meta.filter[key] = value;
            this.reload();
        },
        requestCallback(successMessage, failureMessage) {
            return {
                onSuccess: () => {
                    this.$success(successMessage);
                    this.reload(false);
                },
                onFailure: () => {
                    this.$error(failureMessage);
                    this.reload(false);
                },
                preserveState: true,
            };
        },
    },
};
