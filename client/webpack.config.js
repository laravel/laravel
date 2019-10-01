const path = require('path')
const webpack = require('webpack')

module.exports = {
    entry: {
        main: [
            'webpack-dev-server/client?http://project-name.local/', // WebpackDevServer host and port
            'webpack/hot/only-dev-server', // "only" prevents reload on syntax errors
            './client/main',
        ],
        customerService: [
            'webpack-dev-server/client?http://project-name.local/', // WebpackDevServer host and port
            'webpack/hot/only-dev-server', // "only" prevents reload on syntax errors
            './client/customerService',
        ],
    },
    output: {
        path: path.resolve(__dirname, 'assets'),
        filename: '[name].js',
        publicPath: '/assets/',
    },
    module: {
        loaders: [
            {
                test: /\.jsx?$/,
                exclude: /(node_modules)/,
                loaders: ['babel-loader'],
            },
            {
                test: /\.(sass|scss|css)$/,
                loaders: ['style-loader', 'css-loader?sourceMap', 'sass-loader?sourceMap'],
            },
            {
                test: /\.json$/,
                loader: 'json-loader',
            },
            {
                test: /\.(svg|woff|woff2|eot|ttf|png|jpg)(\?.*$|$)/,
                loader: 'file-loader',
            },
        ],
    },
    plugins: [
        new webpack.DefinePlugin({
            __DEBUG__: true,
        }),
        // Avoid publishing files when compilation fails
        new webpack.NoEmitOnErrorsPlugin(),
        new webpack.HotModuleReplacementPlugin(),
    ],
    // Create Sourcemaps for the bundle
    devtool: '#eval-source-map',
    resolve: {
        extensions: ['.jsx', '.js', '.json'],
        modules: [path.resolve(__dirname, '..', 'client'), 'node_modules'],
    },
}
