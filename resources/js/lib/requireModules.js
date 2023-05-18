import {paramCase} from 'change-case';

export default function (context, Vue, prefix) {
    context.keys().forEach((file) => {
        let componentName = paramCase(`${prefix}${file.replace(/^\.\/(.*?)\.vue$/g, '$1')}`);

        console.log(componentName);
        Vue.component(componentName, () => context(file));
    });
}
