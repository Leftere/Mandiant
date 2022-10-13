const gulp = require('gulp')
const config = require('../config')
const changed = require('gulp-changed')

function copyFavicon() {
  return gulp
    .src(config.favicon.src)
    .pipe(changed(config.favicon.dest))
    .pipe(gulp.dest(config.favicon.dest))
}

function copyFonts() {
  return gulp
    .src(config.fonts.src)
    .pipe(changed(config.fonts.dest))
    .pipe(gulp.dest(config.fonts.dest))
}

function copyImages() {
  return gulp
    .src(config.images.src)
    .pipe(changed(config.images.dest))
    .pipe(gulp.dest(config.images.dest))
}

module.exports = {
  copyFavicon: copyFavicon,
  copyFonts: copyFonts,
  copyImages: copyImages
}
