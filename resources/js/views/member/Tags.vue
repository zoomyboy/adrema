<template>
    <div class="bool-row">
        <ui-bool true-comment="Mittendrin abonniert" false-comment="Mittendrin nicht abonníert" v-model="member.send_newspaper">M</ui-bool>
        <ui-bool true-comment="In NaMi eingetragen" false-comment="Nicht in NaMi eingetragen" v-model="member.has_nami">N</ui-bool>
    </div>
</template>

<script>
export default {
    props: {
        member: {
            required: true,
        },
    },
};
</script>
