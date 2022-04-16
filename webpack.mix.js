let mix = require('laravel-mix');

mix.js('resources/assets/js/app.js', 'public/js')
    .js('resources/assets/js/app-demo.js', 'public/js')
    .sass('resources/assets/sass/app.scss', 'public/css')
    .sass('resources/assets/sass/print.scss', 'public/css')
    .version();
