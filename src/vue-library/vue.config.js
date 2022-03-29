const path = require('path');

module.exports = {
  lintOnSave: true,
  filenameHashing: false,
  productionSourceMap: false,
  outputDir: './dist',

  configureWebpack: {
    optimization: {
      splitChunks: false,
    },
    output: {
      libraryExport: 'default',
    },
    resolve: {
      symlinks: false,
    },
  },

  pluginOptions: {
    i18n: {
      locale: 'en',
      fallbackLocale: 'en',
      localeDir: 'locales',
      enableLegacy: false,
      runtimeOnly: false,
      compositionOnly: false,
      fullInstall: true,
    },
  },
};
