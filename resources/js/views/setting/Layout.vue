<template>
    <div class="flex grow relative">
        <ui-menulist v-model="active" :entries="$page.props.setting_menu"></ui-menulist>
        <slot></slot>
    </div>
</template>

<script>
export default {
    data: function () {
        return {
            innerActive: this.$page.props.setting_menu.findIndex((menu) => menu.is_active),
        };
    },
    computed: {
        active: {
            get() {
                return this.innerActive;
            },
            set(v) {
                var _self = this;
                this.$inertia.visit(this.$page.props.setting_menu[v].url, {
                    onSuccess() {
                        _self.innerActive = v;
                    },
                });
            },
        },
    },
};
</script>
