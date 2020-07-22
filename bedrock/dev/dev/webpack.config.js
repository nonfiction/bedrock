const DEV = process.env.WEBPACK_DEV_SERVER === 'true';

// Paths to make this work!
const src = '/srv/dev';
const path = '/srv/web/app/site/dist';
const publicPath = '/app/site/dist/';

// Format of filenames (without extension)
const filename = DEV ? '[name]' : '[name]-[chunkhash:8]';
const chunkFileName = DEV ? '[id]' : '[id]-[chunkhash:8]';

// Build config object literal
const config = {

  target: 'web',
  bail: !DEV,
  mode: DEV ? 'development' : 'production',
  devtool: DEV ? 'cheap-eval-source-map' : 'source-map',

  // Which files to start looking at
  entry: {
    head:       `${src}/head.js`,
    body:       `${src}/body.js`,
    blocks:     `${src}/blocks.js`,
    blocktypes: `${src}/blocktypes.js`,
    admin:      `${src}/admin.js`,
  },

  // Where to save build files to disk
  output: {
    path: `${path}`,
    filename: `${filename}.js`,
    chunkFilename: `${chunkFileName}.js`,
    publicPath: `${publicPath}`,
  },

  // How to handle different file extensions
  module: {
    rules: [

      // Convert JS
      {
        test: /\.(js|jsx)$/,
        exclude: /node_modules/,
        use: [{
          loader: 'babel-loader',
          options: {
            presets: ['@babel/preset-env', '@babel/preset-react']
          },
        }],
      },

      // Convert CSS
      {
        test: /\.css$/,
        exclude: /node_modules/,
        use: [

          // 3: inject styles (dev), or save to files (production)
          ((DEV) ? 'style-loader' : require("mini-css-extract-plugin").loader),


          // 2. Convert from JS strings to CSS
          {
            loader: 'css-loader',
            options: { importLoaders: 1 }
          },


          // 1. Convert future CSS to current CSS
          {
            loader: 'postcss-loader',
            options: {
              ident: 'postcss',
              plugins: (loader) => [
                require('postcss-import')({ root: loader.resourcePath }),
                require('postcss-preset-env')({ stage: 2, features: { 'nesting-rules': true } }),
                require('postcss-pxtorem')({ propList: ['*'] }),
              ],
            }
          }

        ],
      },

      // Images
      {
        test: /\.(png|svg|jpg|jpeg|gif)$/,
        use: [
          'file-loader',
        ],
      },

    ],
  },

  // Make CSS and JS tiny
  optimization: {
    minimize: !DEV,
    minimizer: [
      new (require('optimize-css-assets-webpack-plugin'))(),
      new (require('terser-webpack-plugin'))()
    ]
  },

  // HMR goodness
  devServer: {
    index: '',
    host: '0.0.0.0',
    port: 443,
    https: true,
    disableHostCheck: true,
    overlay: true,
    contentBase: `${path}`,
    contentBasePublicPath: `${publicPath}`,
    publicPath: `${publicPath}`,
    // writeToDisk: true,
    proxy: {
      context: () => true,
      target: 'https://srv',
      secure: false,
    }
  },

  plugins: [

    // Save CSS files (imported by entry js) to the build directory
    new (require("mini-css-extract-plugin"))({
      filename: `${filename}.css`,
      chunkFilename: `${chunkFileName}.css`,
    }),

    // Keep build directory tidy without old files
    new (require('clean-webpack-plugin').CleanWebpackPlugin)({
      cleanOnceBeforeBuildPatterns: ['**/*', '!*.json'],
    }),

    // Wordpress can use this json file to know which assets to enqueue
    new (require('assets-webpack-plugin'))({
      path: `${path}`,
      filename: DEV ? `manifest-dev.json` : `manifest.json`,
    }),

    // Maybe this helps me understand?
    DEV &&
      new (require('friendly-errors-webpack-plugin'))({
        clearConsole: false,
      }),

  ].filter(Boolean),
}

module.exports = config;
