const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin
const log = require('fancy-log')
const PluginError = require('plugin-error')
const webpack = require('webpack')
const webpackConfig = require('../webpack.config')

// PRODUCTION
function buildProd(done) {
  const prodConfig = Object.assign(webpackConfig, {
    mode: 'production',
    devtool: false,
    plugins: [
      ...webpackConfig.plugins,
      new BundleAnalyzerPlugin({
        analyzerMode: 'static',
        generateStatsFile: true
      })
    ]
  })

  webpack(prodConfig, function(err, stats) {
    if (err) {
      throw new PluginError('webpack:build', err)
    }

    log(
      '[webpack:build]',
      stats.toString(prodConfig.stats)
    )
    done()
  })
}

// DEVELOPMENT
function buildDev(done) {
  const devConfig = Object.assign(webpackConfig, {
    mode: 'development',
    devtool: false,
    optimization: {
      minimize: false
    },
    stats: 'errors-warnings'
  })

  webpack(devConfig, function(err, stats) {
    if (err) {
      throw new PluginError('webpack:build-dev', err)
    }

    log(
      '[webpack:build-dev]',
      stats.toString(devConfig.stats)
    )
    done()
  })
}

module.exports = {
  buildProd: buildProd,
  buildDev: buildDev
}
