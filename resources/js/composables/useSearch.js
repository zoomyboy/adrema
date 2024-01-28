import {inject} from 'vue';

export default function useSearch() {
    const axios = inject('axios');
    async function search(text, filters = []) {
        var response = await axios.post(
            document.querySelector('meta[name="meilisearch_baseurl"]').content + '/indexes/members/search',
            {
                q: text,
                filter: filters,
            },
            {headers: {Authorization: 'Bearer ' + document.querySelector('meta[name="meilisearch_key"]').content}}
        );

        return response.data;
    }

    return {
        search,
    };
}
