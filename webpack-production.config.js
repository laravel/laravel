const { resolve, join } = require('path')
const webpack = require('webpack')
const ExtractTextPlugin = require('extract-text-webpack-plugin')
// const FileSystem = require('fs')
// const CopyWebpackPlugin = require('copy-webpack-plugin')

module.exports = {
    devtool: 'cheap-module-eval-source-map',

    entry: {
        main: './main',
    },
    output: {
        // filename: '[name]_[hash].js',
        filename: '[name].js',
        path: resolve(__dirname, 'public', 'assets'),
        publicPath: '/assets/',
    },

    context: resolve(__dirname, 'client'),

    resolve: {
        modules: [resolve('src'), 'node_modules'],
        extensions: ['.js', '.jsx', '.json', '.css'],
    },

    module: {
        rules: [
            // {
            //     enforce: "pre",
            //     test: /\.jsx?$/,
            //     exclude: /node_modules/,
            //     loader: "eslint-loader"
            // },
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
                                    plugins: function() {
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
                test: /\.jsx?$/,
                loaders: ['babel-loader'],
                exclude: /node_modules/,
            },
        ],
    },

    plugins: [
        new webpack.DefinePlugin({
            'process.env': {
                NODE_ENV: JSON.stringify('production'),
            },
        }),

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
        new webpack.NamedModulesPlugin(),
        new ExtractTextPlugin({
            filename: '[name]_[hash].css',
        }),
        // Asset versioning with file template replacement:
        // function() {
        //     this.plugin('done', function(statsData) {
        //         const stats = statsData.toJson()
        //
        //         if (!stats.errors.length) {
        //             const htmlFileName = 'index.html'
        //             let html = FileSystem.readFileSync(join(__dirname, 'build', htmlFileName), 'utf8')
        //
        //             // Add JS hash
        //             html = html.replace(
        //                 /<script\s+src=(["'])(.+?)main(.*?)\.js\1/i,
        //                 '<script src=$1$2' + stats.assetsByChunkName.main[0] + '$1'
        //             )
        //
        //             // Add CSS hash
        //             html = html.replace(
        //                 /<link\s+href=(["'])(.+?)main(.*?)\.css\1/i,
        //                 '<link href=$1$2' + stats.assetsByChunkName.main[1] + '$1'
        //             )
        //
        //             FileSystem.writeFileSync(join(__dirname, 'build', htmlFileName), html)
        //         }
        //     })
        // },
        new webpack.optimize.UglifyJsPlugin(), // minify everything
        new webpack.NoEmitOnErrorsPlugin(), // Avoid publishing files when compilation fails
    ],
}
