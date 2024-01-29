import {inject} from 'vue';

export default function useSearch() {
    const axios = inject('axios');
    async function search(text, filters = [], options = {}) {
        var response = await axios.post(
            '/indexes/members/search',
            {
                q: text,
                filter: filters,
                ...options,
            },
            {headers: {Authorization: 'Bearer ' + document.querySelector('meta[name="meilisearch_key"]').content}}
        );

        return response.data;
    }

    return {
        search,
    };
}
