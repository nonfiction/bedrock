require('dotenv').config();
const DEV = process.env.WP_ENV === 'development';

function getPath(relativePath) {
  const cwd = require('fs').realpathSync(process.cwd());
  return require('path').resolve(cwd, relativePath);
}

// Entry and build directory paths
const src  = getPath('web/app/site/theme/assets');
const dist = getPath('web/app/site/theme/dist');

// Format of filenames (without extension)
const filename = DEV ? '[name]' : '[name]-[hash:8]';
const chunkFileName = DEV ? '[id]' : '[id]-[hash:8]';


// Build config object literal
const config = {

  target: 'web',
  bail: !DEV,
  mode: DEV ? 'development' : 'production',
  devtool: DEV ? 'none' : 'source-map',
  // devtool: DEV ? 'cheap-eval-source-map' : 'source-map',

  // Which files to start looking at
  entry: {
    main: `${src}/entry-main.js`,
    vendor: `${src}/entry-vendor.js`,
  },

  // Where to save build files to disk
  output: {
    path: `${dist}`,
    filename: `${filename}.js`,
    chunkFilename: `${chunkFileName}.js`,
  },

  // How to handle different file extensions
  module: {
    rules: [

      // Convert JS
      {
        test: /\.(js|jsx)$/,
        exclude: /(node_modules|bower_components)/,
        use: [{
          loader: 'babel-loader',
          options: {
            // presets: ['env, react']
          },
        }],
      },

      // Convert CSS
      {
        test: /\.css$/,
        exclude: /(node_modules|bower_components)/,
        use: [

          // 3: save to file
          require("mini-css-extract-plugin").loader,
          // 'style-loader',

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
                require('postcss-preset-env')(),
              ]
            }
          }

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

  plugins: [

    // Save CSS files (imported by entry js) to the build directory
    new (require("mini-css-extract-plugin"))({
      filename: `${filename}.css`,
      chunkFilename: `${chunkFileName}.css`,
    }),

    // Keep build directory tidy without old files
    new (require('clean-webpack-plugin').CleanWebpackPlugin)(),   

    // Wordpress can use this json file to know which assets to enqueue
    new (require('assets-webpack-plugin'))({
      path: `${dist}`,
      filename: `assets.json`,
    }),

    // Maybe this helps me understand?
    DEV &&
      new (require('friendly-errors-webpack-plugin'))({
        clearConsole: false,
      }),    

    // DEV &&
    //   new (require('browser-sync-webpack-plugin'))({
    //     notify: false,
    //     host: 'localhost',
    //     port: 4000,
    //     logLevel: 'silent',
    //     files: ['./*.php'],
    //     proxy: 'http://localhost:9009/',
    //   }),

  ].filter(Boolean),
}

module.exports = config;
