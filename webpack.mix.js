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
    'resources/assets/js/service/suppliers-service.js',
    'resources/assets/js/service/staffs-service.js',
    'resources/assets/js/service/vehicles-service.js',
], 'public/assets/js/service/service.js');

//mix directive
mix.js([
    'resources/assets/js/directive/input-directive.js'
], 'public/assets/js/directive/directive.js');

//customer
mix.js([
    'resources/assets/js/controller/customers-vl.js'
], 'public/assets/js/controller/customers.js');

mix.js([
    'resources/assets/js/controller/customers-list-vl.js'
], 'public/assets/js/controller/customers-list.js');
//supplier
mix.js([
    'resources/assets/js/controller/suppliers-list-vl.js'
], 'public/assets/js/controller/suppliers-list.js');

mix.js([
    'resources/assets/js/controller/suppliers-vl.js'
], 'public/assets/js/controller/suppliers.js');

mix.js([
    'resources/assets/js/controller/staffs-list-vl.js'
], 'public/assets/js/controller/staffs-list.js');

mix.js([
    'resources/assets/js/controller/staffs-vl.js'
], 'public/assets/js/controller/staffs.js');
//vehicles
mix.js([
    'resources/assets/js/controller/vehicles-list-vl.js'
], 'public/assets/js/controller/vehicles-list.js');

mix.js([
    'resources/assets/js/controller/vehicles-vl.js'
], 'public/assets/js/controller/vehicles.js');