var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Less
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
    mix
    .sass("ensphere.scss", "public/package/ensphere/ensphere/css")
    .copy("resources/assets/images/", "public/package/ensphere/ensphere/images/")
	.copy("resources/assets/js/", "public/package/ensphere/ensphere/js/");
});
