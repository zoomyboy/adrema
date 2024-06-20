import {computed, ref, inject} from 'vue';

export default function (init) {
    const axios = inject('axios');

    const children = ref(init);

    function isOpen(child) {
        return child in children.value;
    }

    async function toggle(parent) {
        if (isOpen(parent.id)) {
            delete children.value[parent.id];
        } else {
            children.value[parent.id] = (await axios.get(parent.links.children)).data.data;
        }
    }

    function childrenOf(parentId) {
        return children.value[parentId] ? children.value[parentId] : [];
    }

    function clearToggle() {
        children.value = {};
    }

    return {
        isOpen,
        toggle,
        childrenOf,
        clearToggle,
    };
}
