<template>
    <div class="flex grow relative">
        <v-tabs v-model="active" :entries="$page.props.setting_menu"></v-tabs>
        <slot></slot>
    </div>
</template>

<script>
export default {
    data: function () {
        return {
            innerActive: 0,
        };
    },
    components: {
        'v-tabs': () => import('../../components/VTabs.vue'),
    },
    computed: {
        active: {
            get() {
                return this.innerActive;
            },
            set(v, old) {
                var _self = this;
                this.$inertia.visit(this.$page.props.setting_menu[v].url, {
                    onSuccess(page) {
                        console.log('A');
                        _self.innerActive = v;
                    },
                });
            },
        },
    },
    mounted() {
        this.innerActive = this.$page.props.setting_menu.findIndex((menu) => menu.is_active);
    },
};
</script>
