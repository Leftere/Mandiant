const del = require('del')

function cleanBuild() {
  return del(['build/**/*'])
}

module.exports = {
  cleanBuild: cleanBuild
}