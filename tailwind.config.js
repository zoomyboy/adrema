var modules = require('js-modules/tailwind/index.js');
const { colors } = require('tailwindcss/defaultTheme');

module.exports = {
    purge: {
        enabled: false,
        content: [
            "resources/js/views/**/*.vue",
            "resources/js/layouts/**/*.vue",
        ]
    },
    theme: {
        extend: {
        },
        colors: {
            teal: [],
            primary: {
                100: 'hsl(181, 98%, 93%)',
                200: 'hsl(181, 84%, 78%)',
                300: 'hsl(181, 78%, 62%)',
                400: 'hsl(181, 76%, 53%)',
                500: 'hsl(181, 75%, 44%)',
                600: 'hsl(181, 75%, 35%)',
                700: 'hsl(181, 75%, 26%)', // locked
                800: 'hsl(181, 86%, 16%)',
                900: 'hsl(181, 94%, 10%)'
            },
            gray: colors.gray,
            white: colors.white,
            red: colors.red,
            green: {
                100: colors.green[100],
                800: colors.green[800],
            }
        }
    },
    corePlugins: {
        clear: false,
        float: false,
        boxSizing: false,
        accessibility: false,
        animation: false,
        backgroundPosition: false,
        backgroundRepeat: false,
        skew: false,
        verticalAlign: false,
        wordBreak: false,
        ringWidth: false,
        ringColor: false,
        ringOffsetColor: false,
        ringOffsetWidth: false,
        ringOpacity: false,
        ringWidth: false,
    },
    variants: {
        alignContent: ['responsive'],
        alignItems: [],
        alignSelf: [],
        appearance: [],
        backgroundAttachment: ['responsive'],
        backgroundColor: ['hover'],
        backgroundPosition: [],
        backgroundSize: [],
        borderCollapse: [],
        borderColor: [],
        borderRadius: [],
        borderStyle: [],
        borderWidth: [],
        boxShadow: ['hover'],
        cursor: [],
        display: ['responsive', 'group-hover'],
        fill: ['responsive'],
        flex: ['responsive'],
        flexDirection: ['responsive'],
        flexGrow: ['responsive'],
        flexShrink: [],
        flexWrap: ['responsive'],
        fontFamily: [],
        fontSize: ['responsive'],
        fontSmoothing: [],
        fontStyle: ['responsive'],
        fontWeight: ['hover'],
        height: ['responsive'],
        inset: [],
        justifyContent: ['responsive'],
        width: ['responsive'],
        zIndex: [],
        gap: ['responsive'],
        gridAutoFlow: ['responsive'],
        gridTemplateColumns: ['responsive'],
        gridColumn: ['responsive'],
        gridColumnStart: ['responsive'],
        gridColumnEnd: ['responsive'],
        gridTemplateRows: ['responsive'],
        gridRow: ['responsive'],
        gridRowStart: ['responsive'],
        gridRowEnd: ['responsive'],
        transform: [],
        transformOrigin: [],
        scale: [],
        rotate: [],
        translate: [],
        skew: [],
        transitionProperty: [],
        transitionTimingFunction: [],
        transitionDuration: [],
    },
    plugins: [
        modules.checkbox({}),
        modules.switch({})
    ]
}
