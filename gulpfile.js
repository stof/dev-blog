'use strict';

var gulp = require('gulp');
var del = require('del');
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');
var plumber = require('gulp-plumber');


var _is_dev_mode = false;

/* ################################################################
 * META TASKS
 * ################################################################ */

gulp.task('default', ['build:css']);

gulp.task('devmode', function() {
    _is_dev_mode = true;
});


gulp.task('watch', ['default'], function() {
    gulp.watch('./source/css/**/*.scss', ['build:css']);
});


/* ################################################################
 * SASS/CSS TASKS
 * ################################################################ */

gulp.task('build:css', ['sass']);


gulp.task('clean:css', function (cb) {
    del(['./source/css/*.css'], cb);
});


gulp.task('sass', ['clean:css'], function () {
    var stream = gulp.src('./source/css/style.scss')
        .pipe(plumber({
            errorHandler: onError
        }));

    if (_is_dev_mode) {
        stream = stream.pipe(sourcemaps.init());
    }

    stream = stream.pipe(sass())

    if (_is_dev_mode) {
        stream = stream.pipe(sourcemaps.write());
    }

    return stream = stream.pipe(gulp.dest('./source/css'));
});


/* ################################################################
 * UTILITIES
 * ################################################################ */



function onError(err) {
    console.log(err.toString());
    this.emit('end');
}

