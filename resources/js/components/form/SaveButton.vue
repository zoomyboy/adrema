<template>
    <portal to="toolbar-right">
        <button
            :form="form"
            type="submit"
            class="flex items-center transition-all justify-center w-8 h-8 bg-primary-700 hover:bg-primary-600 rounded"
            v-tooltip="`speichern`"
        >
            <svg-sprite class="w-4 h-4 text-white" src="save"></svg-sprite>
        </button>
    </portal>
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
