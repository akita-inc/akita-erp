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

mix.js('resources/assets/js/app-vl.js', 'public/assets/js/app-vl.js');

//mix service
mix.js([
    //append-auto-service
    'resources/assets/js/service/customers-service.js',
], 'public/assets/js/service/service.js');

//mix directive
mix.js([
    'resources/assets/js/directive/input-directive.js'
], 'public/assets/js/directive/directive.js');

//customer
mix.js([
    'resources/assets/js/controller/customers-vl.js'
], 'public/assets/js/controller/customers.js');
