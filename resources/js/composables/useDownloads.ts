import { Axios } from 'axios';
import { inject } from 'vue';

export default function() {
    const axios = inject<Axios>('axios');

    async function download(url: string, payload: Record<string, string>) {
        const payloadString = btoa(encodeURIComponent(JSON.stringify(payload)));
        await axios.get(`${url}?payload=${payloadString}&validate=1`);
        window.open(`${url}?payload=${payloadString}`);
    }

    return { download };
}
