/*eslint no-sync: "off"*/
const browsersync = require('browser-sync').create()
const config = require('../config')
const gulp = require('gulp')
const log = require('fancy-log')

let BROWSERSYNC_DEFAULT_OPTIONS = {
  interval: 1000,
  usePolling: true
}

function reload(done) {
  browsersync.reload()
  done()
}

function init(done) {
  return new Promise((resolve) => {
    // initialize browserSync
    browsersync.init(config.browserSync)

    // watch styles
    gulp
      .watch(
        config.styles.src,
        BROWSERSYNC_DEFAULT_OPTIONS,
        gulp.series('styles:dev', reload)
      )
      .on('change', function(path) {
        log(`File ${path} was changed`)
        if (path == 'app/styles/0_utility/_settings.colors.scss') {
          log('Colors changed')
          gulp.series('variables', 'twig:dev', 'styles:dev', reload)(done)
        }
      })

    // watch images
    gulp
      .watch(
        config.images.src,
        BROWSERSYNC_DEFAULT_OPTIONS,
        gulp.series('copy:images', reload)
      )
      .on('change', function(path) {
        log(`File ${path} was changed`)
      })

    // watch twig
    gulp
      .watch(
        config.twig.watchSrc,
        BROWSERSYNC_DEFAULT_OPTIONS,
        gulp.series('twig:dev', reload)
      )
      .on('change', function(path) {
        log(`File ${path} was changed`)
      })

    // watch scripts
    gulp
      .watch(
        config.scripts.all,
        BROWSERSYNC_DEFAULT_OPTIONS,
        gulp.series('webpack:dev', reload)
      )
      .on('change', function(path) {
        log(`File ${path} was changed`)
      })

    resolve()
  })
}

module.exports = {
  init: init,
  reload: reload
}
