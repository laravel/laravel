module.exports = () ->
    view = @app.laravue.currentView.replace('-view', '')
    @app.laravue.views[view].ready = true
    @app.laravue.views[view].model = this

    methods = @app.laravue.views[view].funcs
    if !! this.$options.methods.ready
        this.$options.methods.ready()
    for method in methods
        this[method.name].apply(null, method.args);