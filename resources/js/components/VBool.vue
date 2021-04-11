<template>
    <div class="rounded-full w-4 h-4 flex items-center justify-center leading-none" :class="{'bg-green-800': value, 'bg-red-800': !value}">
        <sprite :class="{'text-green-100': value, 'text-red-100': !value}" class="w-2 h-2" :src="value ? 'check' : 'close'"></sprite>
    </div>
</template>

<script>
export default {
    props: {
        value: {}
    },

    computed: {
        t() {
            return this.value ? 'Ja' : 'Nein';
        }
    }
};
</script>
