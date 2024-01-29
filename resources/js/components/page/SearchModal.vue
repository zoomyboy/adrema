<template>
    <div class="fixed z-40 top-0 left-0 w-full h-full flex items-start pt-12 justify-center p-6 bg-black/60" @click.self="emit('close')">
        <div class="relative rounded-lg p-8 shadow-2xl shadow-black border border-sky-700/30 bg-sky-800/10 border-solid w-full max-w-[50rem] overflow-auto backdrop-blur-lg">
            <div class="relative">
                <input
                    ref="searchInput"
                    v-model="searchString"
                    type="text"
                    class="w-full px-5 py-3 pl-16 rounded-2xl border-sky-200/40 text-sky-200 text-xl placeholder-sky-200/40 bg-sky-600/20"
                    placeholder="Wer suchet, der findet …"
                />
                <div class="absolute flex items-center h-full top-0 left-4">
                    <ui-sprite src="search" class="w-7 h-7 text-sky-200/20" />
                </div>
            </div>
            <div v-if="results !== null" class="mt-10 space-y-2">
                <div v-for="member in results.hits" :key="member.id">
                    <div class="flex items-center justify-between hover:bg-sky-600/20 transition text-sky-300 px-6 py-3 rounded-lg">
                        <div class="flex space-x-2 items-center">
                            <div class="w-16 flex">
                                <ui-age-groups icon-class="w-6 h-6" :member="member"></ui-age-groups>
                            </div>
                            <div class="flex items-baseline">
                                <span class="text-lg" v-text="member.fullname"></span>
                                <span class="ml-2 text-xs" v-text="member.group_name"></span>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <i-link v-tooltip="`Details`" :href="member.links.show" class="inline-flex btn btn-primary btn-sm" @click="emit('close')"><ui-sprite src="eye"></ui-sprite></i-link>
                            <i-link v-tooltip="`Bearbeiten`" :href="member.links.edit" class="inline-flex btn btn-warning btn-sm" @click="emit('close')"><ui-sprite src="pencil"></ui-sprite></i-link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {computed, ref, onMounted} from 'vue';
import useSearch from '../../composables/useSearch.js';
const emit = defineEmits(['close']);

const {search} = useSearch();

const realSearchString = ref('');
const results = ref(null);
const searchInput = ref(null);

onMounted(() => {
    searchInput.value.focus();
});

const searchString = computed({
    get: () => realSearchString.value,
    set: async (v) => {
        realSearchString.value = v;

        if (!v.length) {
            results.value = null;
            return;
        }
        results.value = await search(v, [], {limit: 8});
    },
});
</script>
