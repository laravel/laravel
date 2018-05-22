var webpack = require('webpack');
var WebpackDevServer = require('webpack-dev-server');
var config = require('./client/webpack.config');

new WebpackDevServer(webpack(config), {
    contentBase: "public/",
    publicPath: config.output.publicPath,
    hot: true,
    historyApiFallback: true,
    proxy: {
        '*': {
            target: 'http://php'
        }
    },
    disableHostCheck: true,
    watchOptions: {
        aggregateTimeout: 300,
        poll: 100
    }
}).listen(81, '0.0.0.0', function (err, result) {
    if (err) {
        console.log(err);
    }

    console.log('Listening at localhost');
});
