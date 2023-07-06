<template>
    <button :form="form" type="submit" class="flex items-center transition-all justify-center w-8 h-8 bg-primary-700 hover:bg-primary-600 rounded" v-tooltip="`speichern`">
        <ui-sprite class="w-4 h-4 text-white" src="save"></ui-sprite>
    </button>
</template>

<script>
export default {
    props: {
        form: {
            required: true,
        },
    },
};
</script>
