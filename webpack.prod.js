'use strict';

const path = require("path");
const webpack = require('webpack');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const DashboardPlugin = require('webpack-dashboard/plugin');
const autoprefixer = require('autoprefixer');
const ProvidePlugin = require('webpack/lib/ProvidePlugin');
const WebpackCleanupPlugin = require('webpack-cleanup-plugin');

const UglifyJSPlugin = require('uglifyjs-webpack-plugin');

module.exports = {
    stats: {
        colors: true,
        reasons: true
    },

    entry: {
        app: './source/app.ts',
        vendor: [
            'core-js',
            'reflect-metadata',
            '@angular/platform-browser-dynamic',
            '@angular/core',
            '@angular/common',
            '@angular/router',
            '@angular/http',
            'jquery',
            'bootstrap-loader',
            'zone.js/dist/zone'
        ],
    },

    output: {
        path: path.resolve(__dirname, 'dist'),
        filename: '[name].[hash].bundle.js',
        publicPath: "",
        sourceMapFilename: '[name].[hash].bundle.js.map',
        chunkFilename: '[id].chunk.js'
    },

    //  output: {
    //    path: root('build'),
    //    filename: '[name].js',
    //    sourceMapFilename: '[name].js.map',
    //    chunkFilename: '[id].chunk.js'
    //    path: path.resolve(__dirname, 'dist'),
    //    filename: 'eepal.1.0.bundle.js',
    //    publicPath: "",
    //    sourceMapFilename: 'eepal.1.0.bundle.js.map',
    //    chunkFilename: '1.0.chunk.js'
    //  },

    devtool: 'source-map',

    resolve: {
        extensions: ['', '.webpack.js', '.web.js', '.ts', '.js']
    },

    plugins: [
        /*      new ClosureCompilerPlugin({
                  compiler: {
                    language_in: 'ECMASCRIPT6',
                    language_out: 'ECMASCRIPT5',
                    compilation_level: 'ADVANCED',
                    warning_level: 'VERBOSE',
                  },
                  concurrency: 3,
              }), */
        new webpack.DefinePlugin({
            'process.env.NODE_ENV': JSON.stringify('production')
        }),
        new WebpackCleanupPlugin(),
        new UglifyJSPlugin({
            mangle: false
        }),
        //      new BabiliPlugin(),
        //    new webpack.optimize.CommonsChunkPlugin('vendor', '[name].[hash].bundle.js'),
        //    new webpack.optimize.CommonsChunkPlugin('vendor', 'vendor.bundle.js'),
        new ProvidePlugin({
            jQuery: 'jquery',
            $: 'jquery',
            jquery: 'jquery',
            "Tether": 'tether',
            "window.Tether": "tether",
            Tooltip: "exports-loader?Tooltip!bootstrap/js/dist/tooltip",
            Alert: "exports-loader?Alert!bootstrap/js/dist/alert",
            Button: "exports-loader?Button!bootstrap/js/dist/button",
            Carousel: "exports-loader?Carousel!bootstrap/js/dist/carousel",
            Collapse: "exports-loader?Collapse!bootstrap/js/dist/collapse",
            Dropdown: "exports-loader?Dropdown!bootstrap/js/dist/dropdown",
            Modal: "exports-loader?Modal!bootstrap/js/dist/modal",
            Popover: "exports-loader?Popover!bootstrap/js/dist/popover",
            Scrollspy: "exports-loader?Scrollspy!bootstrap/js/dist/scrollspy",
            Tab: "exports-loader?Tab!bootstrap/js/dist/tab",
            Util: "exports-loader?Util!bootstrap/js/dist/util"
        }),
        new HtmlWebpackPlugin({
            template: './source/index.html',
            inject: 'body',
            minify: false
        }),
        new DashboardPlugin()
    ],

    module: {
        preLoaders: [{
            test: /\.ts$/,
            loader: 'tslint'
        }],
        loaders: [{
                test: /\.ts$/,
                loaders: ['ts', 'angular2-router-loader', 'angular2-template-loader'],
                exclude: /node_modules/
            },
            {
                test: /\.js$/,
                exclude: [/bower_components/, /node_modules\/@angular\/compiler\/bundles\/.+/],
                loader: 'babel-loader',
                query: {
                    presets: ['es2015']
                }
            },
            {
                test: /\.html$/,
                loader: 'raw'
            },
            //      { test: /\.css$/, loader: 'style-loader!css-loader?sourceMap' },
            {
                test: /\.css$/,
                loaders: ['to-string-loader', "style-loader", 'css-loader']
            },
            {
                test: /.scss$/,
                loaders: ['raw-loader', 'sass-loader']
            },
            {
                test: /\.svg/,
                loader: 'url'
            },
            {
                test: /\.eot/,
                loader: 'url'
            },
            {
                test: /\.woff/,
                loader: 'url'
            },
            {
                test: /\.woff2/,
                loader: 'url'
            },
            {
                test: /\.ttf/,
                loader: 'url'
            },
            {
                test: /bootstrap\/dist\/js\/umd\//,
                loader: 'imports?jQuery=jquery'
            },
        ],
        noParse: [/zone\.js\/dist\/.+/, /angular2\/bundles\/.+/]
    },

}
