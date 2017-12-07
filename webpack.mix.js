let mix = require('laravel-mix');

mix.js('assets/js/script.js', 'dist')
   .sass('assets/scss/styles.scss', 'dist')
   .copy('assets/fonts', 'dist')
   .setPublicPath('dist');