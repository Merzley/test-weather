const mix = require('laravel-mix');

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

mix.copyDirectory('resources/img/http_errors', 'public/svg');

mix.less('resources/less/weather/weather.less', 'public/css/weather.css')
    .version();

mix.js('resources/js/weather/weather.js', 'public/js/weather.js')
    .version();
