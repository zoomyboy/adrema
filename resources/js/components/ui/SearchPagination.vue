<template>
    <div class="flex flex-col md:flex-row justify-between items-center space-y-3 md:space-y-0">
        <div class="text-sm text-gray-500" v-text="desc"></div>
        <div v-if="value.totalPages > 1" class="items-center flex space-x-2">
            <div class="hidden sm:flex text-gray-500 text-sm" v-text="pages"></div>
            <button
                v-if="value.page !== 1"
                href="#"
                class="rounded hidden sm:flex w-8 h-8 text-primary-100 items-center justify-center leading-none shadow bg-primary-900 hover:bg-primary-800 items-center justify-center"
                @click.prevent="goto(1)"
            >
                <ui-sprite class="w-3 h-3 fill-current rotate-90" src="chevron-double"></ui-sprite>
            </button>
            <button
                v-if="value.page !== 1"
                href="#"
                class="rounded !ml-0 sm:!ml-2 flex w-8 h-8 text-primary-100 items-center justify-center leading-none shadow bg-primary-900 hover:bg-primary-800 items-center justify-center"
                @click.prevent="goto(value.page - 1)"
            >
                <ui-sprite class="w-3 h-3 fill-current rotate-90" src="chevron"></ui-sprite>
            </button>
            <button
                v-for="(button, index) in pageButtons"
                :key="index"
                href="#"
                class="rounded text-sm w-8 h-8 text-primary-100 flex items-center justify-center leading-none shadow"
                :class="{'bg-primary-700': button.current, 'bg-primary-900 hover:bg-primary-800': !button.current}"
                @click.prevent="goto(button.page)"
                v-text="button.page"
            ></button>
            <button
                v-if="value.page !== value.totalPages"
                href="#"
                class="flex rounded text-sm w-8 h-8 text-primary-100 items-center justify-center leading-none shadow bg-primary-900 hover:bg-primary-800 items-center justify-center"
                @click.prevent="goto(value.page + 1)"
            >
                <ui-sprite class="w-3 h-3 fill-current -rotate-90" src="chevron"></ui-sprite>
            </button>
            <button
                v-if="value.page !== value.totalPages"
                href="#"
                class="hidden sm:flex rounded text-sm w-8 h-8 text-primary-100 items-center justify-center leading-none shadow bg-primary-900 hover:bg-primary-800 items-center justify-center"
                @click.prevent="goto(value.totalPages)"
            >
                <ui-sprite class="w-3 h-3 fill-current -rotate-90" src="chevron-double"></ui-sprite>
            </button>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        value: {
            required: true,
        },
    },

    computed: {
        pageButtons() {
            var buttons = [];

            var from = Math.max(1, this.value.page - 2);
            var to = Math.min(this.value.totalPages, this.value.page + 2);

            for (var i = from; i <= to; i++) {
                buttons.push({
                    page: i,
                    current: i === this.value.page,
                });
            }

            return buttons;
        },
        pages() {
            return `Seite ${this.value.page} von ${this.value.totalPages}`;
        },
        desc() {
            return `${this.value.totalHits} Einträge`;
        },
    },
    methods: {
        goto(page) {
            this.$emit('reload', page);
        },
    },
};
</script>
