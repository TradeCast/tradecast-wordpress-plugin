module.exports = {
  lintOnSave: true,
  filenameHashing: false,
  productionSourceMap: false,
  outputDir: '../wp-plugin/src/public/assets',

  chainWebpack: (config) => {
    ['vue-modules', 'vue', 'normal-modules', 'normal'].forEach((rule) => {
      config.module
        .rule('scss')
        .oneOf(rule)
        .use('resolve-url-loader')
        .loader('resolve-url-loader')
        .before('sass-loader')
        .end()
        .use('sass-loader')
        .loader('sass-loader')
        .tap((options) => ({ ...options, sourceMap: true }));
    });
  },

  configureWebpack: {
    optimization: {
      splitChunks: false,
    },
    output: {
      libraryExport: 'default',
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
