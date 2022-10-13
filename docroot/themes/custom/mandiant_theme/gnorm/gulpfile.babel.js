/*
gulpfile.js
===========
Rather than manage one giant configuration file responsible
for creating multiple tasks, each task has been broken out into
its own file in gulp/tasks.
*/

import gulp from 'gulp'

// import tasks
gulp.task('browserSync', require('./gnorm/tasks/browserSync').init)
gulp.task('clean', require('./gnorm/tasks/clean').cleanBuild)
gulp.task('copy:favicon', require('./gnorm/tasks/copy').copyFavicon)
gulp.task('copy:fonts', require('./gnorm/tasks/copy').copyFonts)
gulp.task('copy:images', require('./gnorm/tasks/copy').copyImages)
gulp.task('create-module', require('./gnorm/tasks/createModule').createModule)
gulp.task('styles:build', require('./gnorm/tasks/styles').buildStyles)
gulp.task('styles:dev', require('./gnorm/tasks/styles').devStyles)
gulp.task('twig:build', require('./gnorm/tasks/twig').buildProd)
gulp.task('twig:dev', require('./gnorm/tasks/twig').buildDev)
gulp.task('variables', require('./gnorm/tasks/variables').buildVariables)
gulp.task('webpack:build', require('./gnorm/tasks/webpack').buildProd)
gulp.task('webpack:dev', require('./gnorm/tasks/webpack').buildDev)

gulp.task(
  'build',
  gulp.series(
    'clean',
    gulp.parallel(
      'webpack:build',
      'variables',
      'styles:build',
      'twig:build',
      'copy:favicon',
      'copy:images',
      'copy:fonts'
    )
  )
)
gulp.task('build').description = 'Builds project from the app folder into the build, uglifies the JS, and minifies the CSS'

exports.default = gulp.series(
  gulp.parallel(
    'webpack:dev',
    'styles:dev',
    'twig:dev',
    'copy:favicon',
    'copy:images',
    'copy:fonts'
  ),
  'browserSync'
)
