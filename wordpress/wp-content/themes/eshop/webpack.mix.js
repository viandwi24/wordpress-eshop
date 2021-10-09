const mix = require('laravel-mix');
const tailwindcss = require('tailwindcss');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

 mix
    .setPublicPath('./')
    .js('assets/scripts/app.js', './js')
    .sass('assets/scss/vendor.scss', './css')
    .sass('assets/scss/app.scss', './style.css')
    .options({
        postCss: [ tailwindcss('./tailwind.config.js') ],
    })
    .version();