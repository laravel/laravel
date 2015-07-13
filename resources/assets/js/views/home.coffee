module.exports =
    ready: () -> require('../view-ready.coffee').call(this)
    props: ['app']
    template: require('./home.template.html')