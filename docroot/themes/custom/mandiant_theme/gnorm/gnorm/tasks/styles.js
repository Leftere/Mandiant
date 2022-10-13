const autoprefixer = require('autoprefixer')
const config = require('../config').styles
const cssnano = require('cssnano')
const gulp = require('gulp')
const log = require('fancy-log')
const plumber = require('gulp-plumber')
const postcss = require('gulp-postcss')
const sass = require('gulp-sass')
const sassGlob = require('gulp-sass-glob')
const sourcemaps = require('gulp-sourcemaps')

const postCssPlugins = [
  autoprefixer({
    grid: 'autoplace'
  }),
  cssnano({preset: ['default', {}]})
]

// styles:build does not output sourcemaps
function buildStyles() {
  return gulp
    .src(config.src)
    .pipe(
      plumber({
        errorHandler: function(error) {
          log(error.message)
          this.emit('end')
        }
      })
    )
    .pipe(sassGlob())
    .pipe(
      sass({
        includePaths: ['./node_modules/']
      })
    )
    .pipe(postcss(postCssPlugins))
    .pipe(plumber.stop())
    .pipe(gulp.dest(config.dest))
}

// styles:dev generates sourcemaps
function devStyles() {
  return gulp
    .src(config.src)
    .pipe(
      plumber({
        errorHandler: function(error) {
          log(error.message)
          this.emit('end')
        }
      })
    )
    .pipe(sourcemaps.init())
    .pipe(sassGlob())
    .pipe(
      sass({
        includePaths: ['./node_modules/']
      })
    )
    .pipe(postcss(postCssPlugins))
    .pipe(sourcemaps.write('.'))
    .pipe(plumber.stop())
    .pipe(gulp.dest(config.dest))
}

module.exports = {
  buildStyles: buildStyles,
  devStyles: devStyles
}
