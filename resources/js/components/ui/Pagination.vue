<template>
    <div class="flex flex-col md:flex-row justify-between items-center space-y-3 md:space-y-0">
        <div class="text-sm text-gray-500" v-text="desc"></div>
        <div v-if="value.last_page > 1" class="items-center flex space-x-2">
            <div class="hidden sm:flex text-gray-500 text-sm" v-text="pages"></div>
            <button v-if="value.current_page !== 1" href="#"
                class="rounded hidden sm:flex w-8 h-8 text-primary-100 items-center justify-center leading-none shadow bg-primary-900 hover:bg-primary-800 items-center justify-center"
                @click.prevent="goto(1)">
                <ui-sprite class="w-3 h-3 fill-current rotate-90" src="chevron-double"></ui-sprite>
            </button>
            <button v-if="value.current_page !== 1" href="#"
                class="rounded !ml-0 sm:!ml-2 flex w-8 h-8 text-primary-100 items-center justify-center leading-none shadow bg-primary-900 hover:bg-primary-800 items-center justify-center"
                @click.prevent="goto(value.current_page - 1)">
                <ui-sprite class="w-3 h-3 fill-current rotate-90" src="chevron"></ui-sprite>
            </button>
            <button v-for="(button, index) in pageButtons" :key="index" href="#"
                class="rounded text-sm w-8 h-8 text-primary-100 flex items-center justify-center leading-none shadow"
                :class="{ 'bg-primary-700': button.current, 'bg-primary-900 hover:bg-primary-800': !button.current }"
                @click.prevent="goto(button.page)" v-text="button.page"></button>
            <button v-if="value.current_page !== value.last_page" href="#"
                class="flex rounded text-sm w-8 h-8 text-primary-100 items-center justify-center leading-none shadow bg-primary-900 hover:bg-primary-800 items-center justify-center"
                @click.prevent="goto(value.current_page + 1)">
                <ui-sprite class="w-3 h-3 fill-current -rotate-90" src="chevron"></ui-sprite>
            </button>
            <button v-if="value.current_page !== value.last_page" href="#"
                class="hidden sm:flex rounded text-sm w-8 h-8 text-primary-100 items-center justify-center leading-none shadow bg-primary-900 hover:bg-primary-800 items-center justify-center"
                @click.prevent="goto(value.last_page)">
                <ui-sprite class="w-3 h-3 fill-current -rotate-90" src="chevron-double"></ui-sprite>
            </button>
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

    computed: {
        pageButtons() {
            var buttons = [];

            var from = Math.max(1, this.value.current_page - 2);
            var to = Math.min(this.value.last_page, this.value.current_page + 2);

            for (var i = from; i <= to; i++) {
                buttons.push({
                    page: i,
                    current: i === this.value.current_page,
                });
            }

            return buttons;
        },
        pages() {
            return `Seite ${this.value.current_page} von ${this.value.last_page}`;
        },
        desc() {
            return `${this.value.from} - ${this.value.to} von ${this.value.total} Einträgen`;
        },
    },
    methods: {
        goto(page) {
            if (this.$attrs.onReload) {
                this.$emit('reload', page);
                return;
            }

            var params = new URLSearchParams(window.location.search);
            params.set('page', page);

            this.$inertia.visit(window.location.pathname + '?' + params.toString(), {
                only: this.only,
                preserveState: this.preserve,
            });
        },
    },
};
</script>
