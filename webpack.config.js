const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = {
  ...defaultConfig,
  entry: {
    // JS: регистрация блоков
    index: path.resolve(process.cwd(), 'src', 'index.jsx'),
    // CSS только для редактора
    editor: path.resolve(process.cwd(), 'src', 'editor.scss'),
    // CSS только для фронта
    frontend: path.resolve(process.cwd(), 'src', 'frontend.scss'),
  },
  optimization: {
    ...defaultConfig.optimization,
    splitChunks: {
      ...defaultConfig.optimization?.splitChunks,
      cacheGroups: {
        editor: {
          name: 'editor',
          type: 'css/mini-extract',
          test: /editor\.s?css$/,
          chunks: 'all',
          enforce: true,
        },
        frontend: {
          name: 'frontend',
          type: 'css/mini-extract',
          test: /frontend\.s?css$/,
          chunks: 'all',
          enforce: true,
        },
      },
    },
  },
  plugins: [
    ...defaultConfig.plugins,
    new MiniCssExtractPlugin({
      filename: '[name].css',
    }),
  ],
};