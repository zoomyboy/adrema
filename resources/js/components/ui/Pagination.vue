<template>
    <div class="justify-between flex items-baseline">
        <div class="text-sm text-gray-500" v-html="desc"></div>
        <div class="-mx-1 items-baseline" :class="{hidden: value.last_page == 1, flex: value.last_page > 1}">
            <div class="pl-1 pr-3 text-gray-500 text-sm">Seite:</div>
            <div class="px-1" v-for="(link, index) in links" :key="index">
                <button
                    href="#"
                    @click.prevent="goto(link)"
                    class="rounded text-sm w-8 h-8 text-primary-100 flex items-center justify-center leading-none shadow"
                    :key="index"
                    v-text="link.page"
                    :class="{'bg-primary-700': link.current, 'bg-primary-900': !link.current}"
                ></button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        only: {
            default: null,
            required: false,
        },
        value: {
            required: true,
        },
        preserve: {
            default: false,
            type: Boolean,
        },
    },
    methods: {
        goto(page) {
            if (this.$attrs.onReload) {
                this.$emit('reload', page.page);
                return;
            }

            var params = new URLSearchParams(window.location.search);
            params.set('page', page.page);

            this.$inertia.visit(window.location.pathname + '?' + params.toString(), {
                only: this.only,
                preserveState: this.preserve,
            });
        },
    },

    computed: {
        links() {
            var links = [];

            var from = Math.max(1, this.value.current_page - 3);
            var to = Math.min(this.value.last_page, this.value.current_page + 3);

            for (var i = from; i <= to; i++) {
                links.push({
                    page: i,
                    current: i === this.value.current_page,
                });
            }

            return links;
        },
        desc() {
            return `${this.value.from} - ${this.value.to} von ${this.value.total} Einträgen`;
        },
    },
};
</script>
