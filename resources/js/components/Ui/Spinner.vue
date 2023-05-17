<template>
    <div :class="`spin-${type}`">
        <div v-if="type === 'ring'"></div>
        <div v-if="type === 'ring'"></div>
        <div v-if="type === 'ring'"></div>
        <div v-if="type === 'ring'"></div>
    </div>
</template>

<script>
export default {
    props: {
        type: {
            type: String,
            default: () => 'ring',
        },
    },
};
</script>

<style>
.spin-ring {
    display: inline-block;
    position: relative;
}
.spin-ring div {
    box-sizing: border-box;
    display: block;
    position: absolute;
    width: 100%;
    height: 100%;
    border: 3px solid #fff;
    border-radius: 50%;
    animation: ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
    border-top-color: inherit;
    border-right-color: transparent;
    border-bottom-color: transparent;
    border-left-color: transparent;
}
.spin-ring div:nth-child(1) {
    animation-delay: -0.45s;
}
.spin-ring div:nth-child(2) {
    animation-delay: -0.3s;
}
.spin-ring div:nth-child(3) {
    animation-delay: -0.15s;
}
@keyframes ring {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}
</style>
