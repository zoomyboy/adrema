export default {
    props: {
        src: {required: true, type: String},
    },
    render: function (createElement) {
        var attr = this.$attrs.class ? this.$attrs.class : '';
        return createElement(
            'svg',
            {
                class: attr + ' fill-current',
            },
            [
                createElement(
                    'use',
                    {
                        attrs: {
                            'xlink:href': `/sprite.svg#${this.$props.src}`,
                        },
                    },
                    ''
                ),
            ]
        );
    },
};
