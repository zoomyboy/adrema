var tailwind = require('agnoster/tailwind.js');
var modules = require('js-modules/tailwind/index.js');

module.exports = {
    theme: {
        extend: {
            ...tailwind,
            colors: {
                ...tailwind.colors,
                'primary-lightest': 'hsl(181, 98%, 93%)',
                'primary-ligher': 'hsl(181, 78%, 62%)',
                'primary-light': 'hsl(181, 75%, 44%)',
                'primary': 'hsl(181, 75%, 26%)',
                'primary-dark': 'hsl(181, 86%, 16%)',
                'primary-darker': 'hsl(181, 94%, 10%)',
                'primary-darkest': 'hsl(181, 98%, 6%)',
                'primary-100': 'hsl(181, 98%, 93%)',
                'primary-200': 'hsl(181, 84%, 78%)',
                'primary-300': 'hsl(181, 78%, 62%)',
                'primary-400': 'hsl(181, 76%, 53%)',
                'primary-500': 'hsl(181, 75%, 44%)',
                'primary-600': 'hsl(181, 75%, 35%)',
                'primary-700': 'hsl(181, 75%, 26%)', // locked
                'primary-800': 'hsl(181, 86%, 16%)',
                'primary-900': 'hsl(181, 94%, 10%)'
            },
            width: tailwind.width,
            height: tailwind.height,
        },
    },
    variants: {},
    plugins: [
        modules.checkbox({}),
        modules.switch({})
    ]
}
