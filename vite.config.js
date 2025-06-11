import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';
import Components from 'unplugin-vue-components/vite'

export default defineConfig({
    plugins: [
        Components({
            globs: [],
            directives: false,
            directoryAsNamespace: false,
            types: [],
            dts: 'resources/js/components/components.d.ts',
            resolvers: [
                (componentName) => {
                    if (componentName === 'FMultiplefiles') {
                        return;
                    }
                    if (componentName === 'FSinglefile') {
                        return;
                    }
                    if (componentName.startsWith('Ui')) {
                        let singleComponentName = componentName.replace(/^Ui/, '');
                        return { name: 'default', from: `@/components/ui/${singleComponentName}.vue` };
                    }
                    if (componentName.startsWith('F')) {
                        let singleComponentName = componentName.replace(/^F/, '');
                        return { name: 'default', from: `@/components/form/${singleComponentName}.vue` };
                    }
                    if (componentName.startsWith('Page')) {
                        let singleComponentName = componentName.replace(/^Page/, '');
                        return { name: 'default', from: `@/components/page/${singleComponentName}.vue` };
                    }
                },
            ]
        }),
        laravel(['resources/js/app.js']),
        vue({
            template: {
                transformAssetUrls: {
                    // The Vue plugin will re-write asset URLs, when referenced
                    // in Single File Components, to point to the Laravel web
                    // server. Setting this to `null` allows the Laravel plugin
                    // to instead re-write asset URLs to point to the Vite
                    // server instead.
                    base: null,

                    // The Vue plugin will parse absolute URLs and treat them
                    // as absolute paths to files on disk. Setting this to
                    // `false` will leave absolute URLs un-touched so they can
                    // reference assets in the public directory as expected.
                    includeAbsolute: false,
                },
                compilerOptions: {
                    isCustomElement: (tag) => tag === 'event-form',
                },
            },
        }),
    ],
    resolve: {
        alias: {
            '!': path.resolve(__dirname, './packages'),
        },
    },
});
