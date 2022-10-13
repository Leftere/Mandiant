const config = require('../config').twig,
  exec = require('child_process').exec,
  shellescape = require('shell-escape')

// Default command to run.  Passing a json encoded config.
var cmd = ['php', 'vendor/bin/compile', '-c', JSON.stringify(config)]

function buildProd(done) {
  'use strict'
  cmd.push('-b', 'TRUE')
  exec(shellescape(cmd), function (err, stdout, stderr) {
    if (err) {
      console.log(err)
    }
    console.log(stdout, stderr)
  })
  done()
}

function buildDev(done) {
  'use strict'
  cmd.push('-b', 'FALSE')
  exec(shellescape(cmd), function (err, stdout, stderr) {
    if (err) {
      console.log(err)
    }
    console.log(stdout, stderr)
  })
  done()
}

module.exports = {
  buildProd: buildProd,
  buildDev: buildDev
}

