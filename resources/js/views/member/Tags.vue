<template>
    <div class="bool-row">
        <ui-bool true-comment="In NaMi eingetragen" false-comment="Nicht in NaMi eingetragen" :value="member.has_nami">N</ui-bool>
        <ui-bool true-comment="Mittendrin abonniert" false-comment="Mittendrin nicht abonníert" :value="member.send_newspaper">M</ui-bool>
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
