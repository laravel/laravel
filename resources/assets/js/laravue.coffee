module.exports = 
    app: {}
    currentView: 'home-view'
    version: "0.1.0"

    init: (views) ->
        @laravue.app = this
        views.forEach ((view) =>
            @laravue.views[view] = {funcs: [],model: {},ready: false}
        ).bind this
            
    view: (name) ->
        @currentView = name + '-view'

    goToAnd: (view, name, args...) ->
        args = [view, name].concat(args)
        @call(view, name)
        @currentView = view + '-view'

    call: (view, funcName, args...) ->
        if   @views[view].ready then @views[view]['model'][funcName].apply(null, args)
        else @views[view].funcs.push({name: funcName, args: args}) 

    views: {}