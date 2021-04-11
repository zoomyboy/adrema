import queryString from 'query-string';

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
        }
    }
};
