let mix = require('laravel-mix');

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

mix
  .react('resources/assets/js/4afc/app.js', 'public/js/4afc')
  .sass('resources/assets/sass/4afc/app.scss', 'public/css/4afc');

mix
  .react('resources/assets/js/researcher/app.js', 'public/js/researcher')
  .sass('resources/assets/sass/researcher/app.scss', 'public/css/researcher');

mix
    .react('resources/assets/js/researcher-goldilocks/app.js', 'public/js/researcher-goldilocks')
    .sass('resources/assets/sass/researcher-goldilocks/app.scss', 'public/css/researcher-goldilocks');
