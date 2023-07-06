<template>
    <svg v-bind="$attrs" class="fill-current"><use :xlink:href="`/sprite.svg#${$attrs.src}`" /></svg>
</template>

<script>
export default {};
</script>
