const {colors} = require('tailwindcss/defaultTheme');

module.exports = {
    content: [
        'resources/js/views/**/*.vue',
        'resources/js/components/**/*.vue',
        'resources/js/layouts/**/*.vue',
        'resources/views/**/*.blade.php',
        'resources/js/composables/**/*.js',
        'packages/medialibrary-helper/**/*.vue',
    ],
    theme: {
        extend: {
            colors: {
                woelfling: '#ff6400',
                jungpfadfinder: '#2f53a7',
                pfadfinder: '#00823c',
                rover: '#cc1f2f',
                biber: '#ffed00',
                leiter: '#9d9d9c',
                primary: {
                    100: 'hsl(181, 98%, 93%)',
                    200: 'hsl(181, 84%, 78%)',
                    300: 'hsl(181, 78%, 62%)',
                    400: 'hsl(181, 76%, 53%)',
                    500: 'hsl(181, 75%, 44%)',
                    600: 'hsl(181, 75%, 35%)',
                    700: 'hsl(181, 75%, 26%)', // locked
                    800: 'hsl(181, 86%, 16%)',
                    900: 'hsl(181, 94%, 10%)',
                },
            },
        },
    },

    plugins: [require('@tailwindcss/typography'), require('@tailwindcss/forms')],
};
