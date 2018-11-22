
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

Vue.filter('trans', function (...args) {
	return trans.get(...args);
});

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
	el: '#app',

	mounted: () => {
		require('svg4everybody')();
	},

	components: {
		'login-form': require('./components/accounts/login-form.vue'),
		'register-form': require('./components/accounts/register-form.vue'),
		'forgot-password-form': require('./components/accounts/forgot-password-form.vue'),
		'password-reset-form': require('./components/accounts/password-reset-form.vue'),
		'resend-verify-code-form': require('./components/accounts/resend-verify-code-form.vue'),
	},
});
