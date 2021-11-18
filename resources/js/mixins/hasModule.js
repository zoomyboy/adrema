export default {
    methods: {
        hasModule(module) {
            return this.$page.props.settings.modules.indexOf(module) !== -1;
        }
    }
};
