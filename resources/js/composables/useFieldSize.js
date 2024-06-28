export default function () {
    function sizeClass(size) {
        return {
            sm: 'field-sm',
            base: 'field-base',
            lg: 'field-lg',
        }[size];
    }

    const fieldHeight = 'group-[.field-base]:h-[35px] group-[.field-sm]:h-[23px]';
    const fieldAppearance =
        'group-[.field-base]:border-2 group-[.field-sm]:border border-gray-600 border-solid text-gray-300 bg-gray-700 leading-none rounded-lg group-[.field-base]:text-sm group-[.field-sm]:text-xs';

    const paddingX = 'group-[.field-base]:px-2 group-[.field-sm]:px-1';
    const paddingY = 'group-[.field-base]:py-2 group-[.field-sm]:py-1';
    const selectAppearance = 'py-0 pr-8 group-[.field-base]:pl-2 group-[.field-sm]:pl-1 w-full';

    return {
        fieldHeight,
        fieldAppearance,
        paddingX,
        paddingY,
        sizeClass,
        selectAppearance,
    };
}
