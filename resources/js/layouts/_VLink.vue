<template>
    <inertia-link class="flex text-white py-2 px-3 rounded-lg hover:bg-gray-600" :href="href" :class="{'bg-gray-700': $page.props.menu == menu}">
        <sprite class="text-white w-6 h-6 mr-4" :src="icon"></sprite>
        <span class="font-semibold">
            <slot></slot>
        </span>
    </inertia-link>
</template>

<script>
export default {
    props: {
        href: {},
        icon: {},
        menu: {},
    }
};
</script>
