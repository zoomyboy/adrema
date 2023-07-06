import {paramCase} from 'change-case';
import {defineAsyncComponent} from 'vue';

export default function (context, app, prefix) {
    for (const file in context) {
        let componentName = paramCase(`${prefix}${file.replace(/^.*\/(.*?)\.vue$/g, '$1')}`);

        app.component(componentName, typeof context[file] === 'function' ? defineAsyncComponent(context[file]) : context[file].default);
    }
}
