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
    'resources/assets/js/service/home-service.js',
    'resources/assets/js/service/customers-service.js',
    'resources/assets/js/service/suppliers-service.js',
    'resources/assets/js/service/staffs-service.js',
    'resources/assets/js/service/vehicles-service.js',
    'resources/assets/js/service/empty-info-service.js',
    'resources/assets/js/service/invoice-service.js',
    'resources/assets/js/service/sales-lists-service.js',
    'resources/assets/js/service/payments-service.js',
    'resources/assets/js/service/work-flow-service.js',
    'resources/assets/js/service/take-vacation-service.js',
    'resources/assets/js/service/payment-histories-service.js'
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

mix.js([
    'resources/assets/js/controller/empty-info-vl.js'
], 'public/assets/js/controller/empty-info.js');

mix.js([
    'resources/assets/js/controller/empty-info-list-vl.js'
], 'public/assets/js/controller/empty-info-list.js');
mix.js([
    'resources/assets/js/controller/invoice-list-vl.js'
], 'public/assets/js/controller/invoice-list.js');

mix.js([
   'resources/assets/js/controller/sales-lists-vl.js'
],'public/assets/js/controller/sales-lists.js');
//payments
mix.js([
    'resources/assets/js/controller/payments-list-vl.js'
], 'public/assets/js/controller/payments-list.js');

mix.js([
    'resources/assets/js/controller/work-flow-list-vl.js'
], 'public/assets/js/controller/work-flow-list.js');

mix.js([
    'resources/assets/js/controller/work-flow-vl.js'
], 'public/assets/js/controller/work-flow.js');

mix.js([
    'resources/assets/js/controller/take-vacation-list-vl.js'
], 'public/assets/js/controller/take-vacation-list.js');

mix.js([
    'resources/assets/js/controller/take-vacation-vl.js'
], 'public/assets/js/controller/take-vacation.js');

mix.js([
    'resources/assets/js/controller/payment-histories-list-vl.js'
], 'public/assets/js/controller/payment-histories-list.js');