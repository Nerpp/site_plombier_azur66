const Encore = require('@symfony/webpack-encore');

if (!Encore.isRuntimeEnvironmentConfigured()) {
  Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
  .setOutputPath('public/build/')
  .setPublicPath('/build')

  .addEntry('app', './assets/app.js')


  .enableStimulusBridge('./assets/controllers.json')

  .splitEntryChunks()
  .enableSingleRuntimeChunk()

  .cleanupOutputBeforeBuild()
  .enableBuildNotifications()
  .enableSourceMaps(!Encore.isProduction())

  // on garde la copie des SVG (utile)
  .copyFiles({ from: './assets/svg', to: 'svg/[path][name].[hash:8].[ext]' })

  // plus besoin de copier /assets/fonts
  // .copyFiles({ from: './assets/fonts', to: 'fonts/[path][name].[hash:8].[ext]' })

  .configureImageRule({ type: 'asset', maxSize: 0 })
  .configureFontRule({ type: 'asset', maxSize: 0 }) // OK de laisser, mais pas critique

  .enableVersioning(Encore.isProduction())

  .configureBabelPresetEnv((config) => {
    config.useBuiltIns = 'usage';
    config.corejs = 3;
    config.bugfixes = true;
    config.targets = { esmodules: true };
  })
  .enableSassLoader()
;

module.exports = Encore.getWebpackConfig();
