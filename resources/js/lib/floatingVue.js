import Plugin from 'floating-vue';

import 'floating-vue/dist/style.css';
import '../../css/tooltip.css';

var options = {
    distance: 7,
    themes: {
        tooltip: {
            delay: {
                show: 50,
                hide: 0,
            },
            handleResize: true,
            html: true,
        },
    },
};

export {Plugin, options};
