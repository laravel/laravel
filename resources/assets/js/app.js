var Vue = require('vue')
// Vue.use(require('vue-resource'))
// Vue.use(require('vue-validator'))
// Vue.use(requrie('vue-router'))
Vue.config.debug = true // Comment this line for production

new Vue({
    el: '#app',
    created: function() {
        this.laravue.init.call(this, ['home']);
    },
    data: {
        laravue: require('./laravue.coffee')
    },
    components: require('./components.coffee') // You can just inline them, but I recommend putting them in their own file
})

