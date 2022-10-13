// Scaffold a JavaScript module with:
// $ fin gulp create-module

import eslint from 'gulp-eslint'
import gulp from 'gulp'
import modify from 'gulp-modify'
import PluginError from 'plugin-error'
import readlineSync from 'readline-sync'
import rename from 'gulp-rename'
import template from 'gulp-template'
import modules from '../../app/scripts/modules'
const config = require('../config').scripts

function createModule(done) {
  // Prompt user for module name
  let className = readlineSync.question('What would you like to call this module? [PascalCase, no spaces or numbers]:  ')

  // Capitalize the class declaration
  className = className.charAt(0).toUpperCase() + className.slice(1)

  gulp
    .src(`${config.modules}/index.js`)
    .pipe(
      modify({
        fileModifier: (file, contents) => {
          const start = 'const modules = {\n'
          const end = '\n}\n\nexport default modules\n'
          let moduleNames = Object.keys(modules)
          let modulesArr = []

          // Check to make sure module doesn't exist already
          if (moduleNames.includes(className)) {
            let err = new PluginError(
              'template',
              `The module "${
                className
              }" already exists. Please choose a unique module name.`
            )
            throw err
          } else {
            // Push all the module names into an array and sort them alphabetically
            moduleNames.push(className)
            moduleNames.sort()

            // Loop through the modules names array and build a dynamic import for each
            moduleNames.forEach((name) => {
              modulesArr.push(
                `${name}: ($el) => {\n    import('./${name}/${name}.main').then((Module) => {\n  new Module.default($el)\n}).catch((e) => console.error(e))\n}`
              )
            })

            // Array to string, and break new lines
            contents = modulesArr.toString().replace(',', ',\n')

            return `${start}${contents}${end}`
          }
        }
      })
    )
    .pipe(
      // Beautify the file
      eslint({
        fix: true,
        quiet: true
      })
    )
    .pipe(eslint.format())
    .pipe(eslint.failOnError())
    .pipe(gulp.dest(`${config.modules}/`))

  // Scaffold the template files into the module folder
  gulp
    .src('./gnorm/templates/module.js')
    .pipe(rename(`${className}.main.js`))
    .pipe(template({ className }))
    .pipe(gulp.dest(`${config.modules}/${className}`))

  done()
}

module.exports = {
  createModule: createModule
}
