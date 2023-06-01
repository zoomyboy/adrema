import {paramCase} from 'change-case';

export default function (context, Vue, prefix) {
    for (const file in context) {
        let componentName = paramCase(`${prefix}${file.replace(/^.*\/(.*?)\.vue$/g, '$1')}`);

        Vue.component(componentName, typeof context[file] === 'function' ? context[file] : context[file].default);
    }
}
