import {ref, inject, computed, onBeforeUnmount} from 'vue';
import {router} from '@inertiajs/vue3';
import useQueueEvents from './useQueueEvents.js';

export default function () {
    const sizes = {
        sm: {
            label: 'text-xs',
            field: 'text-xs',
        },
        default: {
            label: 'text-sm',
            field: 'text-sm',
        },
    };

    const defaultFieldClass = 'border-2 p-2 rounded-lg bg-gray-700 border-gray-600 text-gray-300 border-solid';

    function labelClass(size) {
        return sizes[size ? size : 'default'].label;
    }

    function fieldClass(size) {
        return sizes[size ? size : 'default'].field;
    }

    return {
        labelClass,
        fieldClass,
        defaultFieldClass,
    };
}
