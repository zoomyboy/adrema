const mix = require('laravel-mix');
const tailwindcss = require('tailwindcss');
const atImport = require("postcss-import");
const nested = require('postcss-nested');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
.vue({ version: 2 })
.postCss('resources/css/app.css', 'public/css', [
    atImport(),
    tailwindcss('./tailwind.config.js'),
    nested(),
])
.copy('resources/img', 'public/img')
.sourceMaps();
