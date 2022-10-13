/* eslint-disable no-sync*/
const config = require('./config')
const path = require('path')

const ESLintPlugin = require('eslint-webpack-plugin')
const TerserPlugin = require('terser-webpack-plugin')


module.exports = {
  context: path.resolve(config.app),
  entry: {
    app: './scripts/app',
    fontloader: './scripts/fontloader'
  },
  output: {
    chunkFilename: '[name].bundle.js?[contenthash:6]',
    filename: '[name].built.js',
    path: path.resolve(config.scripts.dest),
    publicPath: '/themes/custom/mandiant_theme/gnorm/build/scripts/'
  },
  optimization: {
    minimizer: [new TerserPlugin()]
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: [/node_modules/],
        use: {
          loader: 'babel-loader',
          options: {
            presets: ['@babel/preset-env']
          }
        }
      },
    ]
  },
  devtool: false,
  plugins: [
    new ESLintPlugin({
      failOnError: true,
      fix: true
    })
  ],
  stats: {
    colors: true,
    env: true,
    modules: false
  }
}
