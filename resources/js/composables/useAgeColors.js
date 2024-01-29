import {ref} from 'vue';

export default function useAgeColors() {
    const ageColors = ref({
        biber: 'text-biber',
        woelfling: 'text-woelfling',
        jungpfadfinder: 'text-jungpfadfinder',
        pfadfinder: 'text-pfadfinder',
        rover: 'text-rover',
        leiter: 'text-leiter',
    });

    return {ageColors};
}
