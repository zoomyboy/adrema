import queryString from 'query-string';
import merge from 'merge';

export default {
    methods: {
        qs(merge) {
            var c = queryString.parse(window.location.search);

            var m = { ...c, ...merge };
            var mn = {};

            Object.keys(m).forEach((k) => {
                if (m[k] !== null) {
                    mn[k] = m[k];
                }
            });

            var merged = queryString.stringify(mn);

            return window.location.pathname + (merged ? '?'+merged : '');
        },

        query(options) {
            options = merge({
                only: null,
            }, options);
            var c = queryString.parse(window.location.search);

            if (options.only !== null) {
                for (var k in c) {
                    if (options.only.indexOf(k) < 0) {
                        delete c[k];
                    }
                }
            }

            return Object.keys(c).length === 0 ? '' : `?${queryString.stringify(c)}`;
        }
    }
};

