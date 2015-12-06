var compass = require('laravel-elixir-sass-compass');
var elixir = require('laravel-elixir');

elixir(function(mix) {
    mix.compass("*", "public/assets/css", {
        require: ['breakpoint', 'susy'],
        sass: "resources/assets/sass",
        style: "nested",
        font: "public/fonts",
        image: "public/images",
        javascript: "public/js",
        sourcemap: false,
        comments: true,
        relative: true,
        http_path: false,
        generated_images_path: false
    });
});
