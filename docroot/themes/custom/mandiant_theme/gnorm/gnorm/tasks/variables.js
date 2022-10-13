const gulp = require('gulp'),
  config = require('../config').variables,
  modify = require('gulp-modify'),
  plumber = require('gulp-plumber'),
  log = require('fancy-log'),
  rename = require('gulp-rename'),
  sass = require('gulp-sass')

// Extracts JSON data from CSS comment
// https://github.com/oddbird/sassdoc-theme-herman/blob/master/sass-json-loader.js
function sassJsonLoader(source) {
  const startMarker = '/*! json-encode:'
  const endMarker = '*/'
  const start = source.indexOf(startMarker)
  const end = source.indexOf(endMarker, start)
  const jsondata = source.slice(start + startMarker.length, end)
  return jsondata
}

function buildVariables() {
  return gulp.src(config.src)
    .pipe(plumber({
      errorHandler: function(error) {
        log(error.message)
        this.emit('end')
      }
    }))
    .pipe(sass({
      includePaths: ['./node_modules/']
    }))
    .pipe(modify({
      fileModifier: function(file, contents) {
        return sassJsonLoader(contents.toString())
      }
    }))
    .pipe(rename({
      basename: '_styleguide',
      extname: '.json'
    }))
    .pipe(plumber.stop())
    .pipe(gulp.dest(config.dest))
}

module.exports = {
  buildVariables: buildVariables
}
