var {resolve} = require('path');
var webpack = require('webpack');
const ExtractTextPlugin = require('extract-text-webpack-plugin')

module.exports = {
    devServer: {
        contentBase: "public/",
        publicPath: '/assets/',
        hot: true,
        historyApiFallback: true,
        port: 80,
        proxy: {
            '*': {
                target: 'http://localhost:81'
            }
        },
        watchOptions: {
            aggregateTimeout: 300,
            poll: 100
        }
    },
    entry: [
        // 'react-hot-loader/patch',
        'webpack-dev-server/client?http://localhost',
        'webpack/hot/only-dev-server',
        './main'
    ],
    output: {
        path: resolve(__dirname, 'assets'),
        filename: 'main.js',
        publicPath: '/assets/'
    },
    context: resolve(__dirname, 'client'),
    module: {
        loaders: [
            {
                test: /\.jsx?$/,
                exclude: /(node_modules)/,
                loaders: ['babel-loader']
            },
            {
                test: /(\.sass|\.scss)$/,
                exclude: /node_modules/,
                use: ['css-hot-loader'].concat(
                    ExtractTextPlugin.extract({
                        fallback: 'style-loader',
                        // Could also be write as follow:
                        // use: 'css-loader?modules&importLoader=2&sourceMap&localIdentName=[name]__[local]___[hash:base64:5]!sass-loader'
                        use: [
                            {
                                loader: 'css-loader',
                                query: {
                                    modules: true,
                                    sourceMap: true,
                                    importLoaders: 2,
                                    localIdentName: '[name]__[local]___[hash:base64:5]',
                                },
                            },
                            {
                                loader: 'sass-loader',
                                options: {
                                    sourceMap: true,
                                },
                            },
                            {
                                loader: 'postcss-loader',
                                options: {
                                    sourceMap: 'inline',
                                    plugins: function () {
                                        return [require('autoprefixer')]
                                    },
                                },
                            },
                        ],
                    })
                ),
            },
            {
                test: /\.less$/,
                use: ExtractTextPlugin.extract({
                    use: ['css-loader', 'less-loader'],
                }),
            },
            {
                test: /\.css$/,
                use: ExtractTextPlugin.extract({
                    use: ['css-loader'],
                }),
            },
            {
                test: /\.jpe?g$|\.gif$|\.png$|\.ttf$|\.eot$|\.svg$/,
                use: 'file-loader?name=[name].[ext]?[hash]',
            },
            {
                test: /\.woff(2)?(\?v=[0-9]\.[0-9]\.[0-9])?$/,
                loader: 'url-loader?limit=10000&mimetype=application/fontwoff',
            },
            {
                test: /\.json$/,
                loader: "json-loader"
            }
        ]
    },
    plugins: [
        new webpack.LoaderOptionsPlugin({
            test: /\.jsx?$/,
            options: {
                eslint: {
                    configFile: resolve(__dirname, '.eslintrc'),
                    cache: false,
                },
            },
        }),
        new webpack.optimize.ModuleConcatenationPlugin(),
        //        new CopyWebpackPlugin([{ from: 'vendors', to: 'vendors' }]),
        //     new OpenBrowserPlugin({url: 'http://localhost:8080'}),
        new webpack.HotModuleReplacementPlugin(),
        new webpack.NamedModulesPlugin(),
        new ExtractTextPlugin({
            // filename: '[name].[contenthash].css',
            filename: '[name].css',
        }),
    ],
    // Create Sourcemaps for the bundle
    devtool: 'cheap-module-eval-source-map',
    resolve: {
        modules: [resolve('client'), 'node_modules'],
        extensions: ['.js', '.jsx', '.json', '.css'],
    }
};