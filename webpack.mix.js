const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css')
   .copy('node_modules/@coreui/coreui/dist/js/coreui.bundle.min.js', 'public/js')
   .copy('node_modules/simplebar/dist/simplebar.min.js', 'public/js');