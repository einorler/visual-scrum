// 'use strict';

var gulp = require('gulp');
var copy = require('gulp-copy');
var sass = require('gulp-sass');
var concat = require('gulp-concat');
var watch = require('gulp-watch');
var uglify = require('gulp-uglify');
var sourcemaps = require('gulp-sourcemaps');

var dir = {
    fonts: './web/fonts/',
    css: './web/css/',
    js: './web/js/',
    img: './web/img/',
    plugins: './web/plugins/',
    dist: './web/public/',
    npm: './node_modules/'
};

gulp.task('default', function() {
    loadJquery();
    loadBootstrap();
    loadAdminLte();
});

function loadJquery() {
    load('jQuery', 'js', '/dist/');
    load('jquery-knob', 'js', '/dist/');
}

function loadBootstrap() {
    loadDist('bootstrap', 'css', '.min');
    loadDist('bootstrap', 'js', '.min');
    gulp.src(dir.npm + 'bootstrap/dist/fonts/*')
        .pipe(copy(dir.fonts, {prefix: 3}));
}

function loadAdminLte() {
    loadDist('admin-lte', 'css', '.min');
    loadDist('admin-lte', 'js', '.min');
    gulp.src(dir.npm + 'admin-lte/dist/img/*')
        .pipe(copy(dir.img, {prefix: 4}));
    gulp.src(dir.npm + 'admin-lte/plugins/*.{js,css}')
        .pipe(copy(dir.plugins, {prefix: 3}));
    gulp.src(dir.npm + 'admin-lte/plugins/**/*.{js,css}')
        .pipe(copy(dir.plugins, {prefix: 3}));
}

function load(package_name, ext, path) {
    gulp.src(dir.npm + package_name + path + '/*' + ext + '*')
        .pipe(copy('./web/' + ext + '/' + package_name + '/', {prefix: 3}));
}

function loadDist(package_name, part, min) {
    gulp.src(dir.npm + package_name + '/dist/' + part + '/*' + min + '.{css,js}*')
        .pipe(copy('./web/' + part + '/' + package_name + '/', {prefix: 4}));
    gulp.src(dir.npm + package_name + '/dist/' + part + '/**/*' + min + '.{css,js}*')
        .pipe(copy('./web/' + part + '/' + package_name + '/', {prefix: 4}));
}
