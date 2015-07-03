module.exports = 
    app: {}
    currentView: 'home-view'

    view: (name) ->
        @currentView = name + '-view'

    goToAnd: (view, name, args...) ->
        args = [view, name].concat(args)
        @call(view, name)
        @currentView = view + '-view'

    call: (view, funcName, args...) ->
        console.log !! @views[view].model
        if   @views[view].ready then @views[view]['model'][funcName].apply(null, args)
        else @views[view].funcs.push({name: funcName, args: args}) 

    views:
        # Eventually a CLI will add new lines below
        'home': {funcs: [],model: {},ready: false}
        'about': {funcs: [],model: {},ready: false}