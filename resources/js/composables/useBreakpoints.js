import {computed} from 'vue';

export default function (props) {
    const visibleMobile = computed(() => {
        return {
            sm: 'flex sm:hidden',
            md: 'flex md:hidden',
            lg: 'flex lg:hidden',
            xl: 'flex xl:hidden',
        }[props.breakpoint];
    });

    const visibleDesktop = computed(() => {
        return {
            sm: 'hidden sm:flex',
            md: 'hidden md:flex',
            lg: 'hidden lg:flex',
            xl: 'hidden xl:flex',
        }[props.breakpoint];
    });

    return {
        visibleMobile,
        visibleDesktop,
    };
}
