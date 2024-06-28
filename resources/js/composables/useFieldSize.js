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

    const fieldHeight = 'group-[.field-base]:h-[35px] group-[.field-sm]:h-[23px]';
    const fieldAppearance =
        'group-[.field-base]:border-2 group-[.field-sm]:border border-gray-600 border-solid text-gray-300 bg-gray-700 leading-none rounded-lg group-[.field-base]:text-sm group-[.field-sm]:text-xs';

    const paddingX = 'group-[.field-base]:px-2 group-[.field-sm]:px-1';

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
        fieldHeight,
        fieldAppearance,
        paddingX,
    };
}
