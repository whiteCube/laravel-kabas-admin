let mix = require('laravel-mix');

mix.js('assets/js/script.js', 'dist')
   .sass('assets/scss/admin.scss', 'dist')
   .copy('assets/fonts', 'dist')
   .copy('assets/img', 'dist')
   .setPublicPath('dist');