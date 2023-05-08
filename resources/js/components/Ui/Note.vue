<template>
    <div class="relative p-3 border-2 rounded-lg flex space-x-2 items-start" :class="types[type].container">
        <div class="absolute flex items-center justify-center top-0 left-0 mt-[-12px] ml-[-12px] w-8 h-8">
            <svg class="absolute top-0 left-0 w-full h-full flex-none rotate-[105deg]" viewBox="-16 -16 32 32" :class="types[type].iconContainer">
                <circle cx="0" cy="0" r="15" stroke-dasharray="62.76896820 31.38448410" stroke-width="2"></circle>
            </svg>
            <svg-sprite :src="types[type].icon" class="relative w-4 h-4" :class="types[type].iconClass"></svg-sprite>
        </div>
        <div>
            <h3 class="font-semibold" :class="types[type].heading" v-text="types[type].intro"></h3>
            <div :class="types[type].body">
                <slot></slot>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data: function () {
        return {
            types: {
                warning: {
                    container: 'bg-yellow-800 border-yellow-600',
                    heading: 'text-yellow-200',
                    icon: 'warning',
                    intro: 'Warnung',
                    body: 'text-yello-100',
                    iconContainer: 'stroke-yellow-600 fill-yellow-800',
                    iconClass: 'text-yellow-500',
                },
                danger: {
                    container: 'bg-red-800 border-red-600',
                    heading: 'text-red-200',
                    icon: 'danger',
                    intro: 'Achtung',
                    body: 'text-red-100',
                    iconContainer: 'stroke-red-600 fill-red-800',
                    iconClass: 'text-red-500',
                },
                info: {
                    container: 'bg-blue-800 border-blue-600',
                    heading: 'text-blue-200',
                    icon: 'info',
                    intro: 'Info',
                    body: 'text-blue-100',
                    iconContainer: 'stroke-blue-600 fill-blue-800',
                    iconClass: 'text-blue-500',
                },
            },
        };
    },

    props: {
        type: {
            default: () => 'info',
            required: false,
            type: String,
        },
    },
};
</script>
