<template>
    <i-link class="flex text-white py-2 px-3 rounded-lg hover:bg-gray-600" :method="method" v-bind="$attrs" :href="href" :class="{'bg-gray-700': $page.props.menu == menu}">
        <ui-sprite class="text-white w-6 h-6 mr-4" :src="icon"></ui-sprite>
        <span class="font-semibold">
            <slot></slot>
        </span>
    </i-link>
</template>

<script>
export default {
    props: {
        href: {},
        icon: {},
        menu: {},
        method: {
            default: 'GET',
        },
    },
};
</script>
