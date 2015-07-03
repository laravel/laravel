var Vue = require('vue')
// Vue.use(require('vue-resource')) // Optional Vue Resource plugin for HTTP requests
// Vue.use(require('vue-validator')) // Optional Vue plugin for validating inputs
// Vue.use(requrie('vue-router'))    // Will be default when released by VueJS

Vue.config.debug = true; // Disable this in production
var appOptions = require('./app.vue');
var app = new Vue(appOptions).$mount('#app');