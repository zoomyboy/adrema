import {inject, computed, ref} from 'vue';

export default function useSearch(params = null, options = null) {
    params = params === null ? [] : params;
    options = options === null ? {} : options;
    const axios = inject('axios');
    const results = ref({hits: []});
    const realSearchString = ref('');

    async function search(text, filters = [], options = {}) {
        var response = await axios.post(
            import.meta.env.MODE === 'development' ? 'http://localhost:7700/indexes/members/search' : '/indexes/members/search',
            {
                q: text,
                filter: filters,
                sort: ['lastname:asc', 'firstname:asc'],
                ...options,
            },
            {headers: {Authorization: 'Bearer ' + document.querySelector('meta[name="meilisearch_key"]').content}}
        );

        return response.data;
    }

    function clearSearch() {
        searchString.value = '';
    }

    const searchString = computed({
        get: () => realSearchString.value,
        set: async (v) => {
            realSearchString.value = v;

            if (!v.length) {
                results.value = {hits: []};
                return;
            }

            results.value = await search(v, params, options);
        },
    });

    return {
        search,
        searchString,
        results,
        clearSearch,
    };
}
