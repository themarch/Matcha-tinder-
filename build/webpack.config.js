'use strict'

const path = require("path")
const webpack = require("webpack")
const VueLoaderPlugin = require('vue-loader/lib/plugin')

module.exports = {
  mode: 'production',
  entry: [
    './public/js/App.js'
  ],
  output: {
    path: path.resolve(path.dirname(__dirname), "./public/dist"),
    filename: "webpack.[name].js"
  },
  module: {
    rules: [{
        test: /\.vue$/,
        use: 'vue-loader'
      },
      {
        exclude: /(node_modules|bower_components)/,
        test: /\.js$/,
        loader: 'babel-loader'
      },
      {
        test: /\.css$/,
        use: [
          'vue-style-loader',
          'css-loader'
        ]
      }
    ]
  },
  resolve: {
    extensions: ['*', '.wasm', '.mjs', '.js', '.jsx', '.json', '.vue'],
    alias: {
      'vue$': 'vue/dist/vue.common.js'
    }
  },
  plugins: [
    new VueLoaderPlugin()
  ],
  stats: {
    colors: true,
    warnings: true,
    errors: true,
    modules: true,
    moduleTrace: true,
    errorDetails: true,
    publicPath: true
  }
}