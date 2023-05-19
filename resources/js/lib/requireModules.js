import {paramCase} from 'change-case';

export default function (context, Vue, prefix) {
    var isAsync = context.name === 'webpackAsyncContext';

    context.keys().forEach((file) => {
        let componentName = paramCase(`${prefix}${file.replace(/^\.\/(.*?)\.vue$/g, '$1')}`);

        Vue.component(componentName, isAsync ? () => context(file) : context(file).default);
    });
}
