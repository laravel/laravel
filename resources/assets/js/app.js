/* eslint-disable no-new */

import Vue from 'vue';
import svg4everybody from 'svg4everybody';

import './bootstrap';
import lang from './i18n';

import ForgotPasswordForm from './components/accounts/forgot-password-form';
import LoginForm from './components/accounts/login-form';
import PasswordResetForm from './components/accounts/password-reset-form';
import RegisterForm from './components/accounts/register-form';
import ResendVerifyCodeForm from './components/accounts/resend-verify-code-form';

Vue.filter('trans', (...args) => lang.get(...args));

// Global
// Vue.component('ExampleGlobalComponent', ExampleGlobalComponent);

new Vue({
	el: '#app',

	// Local
	components: {
		// App
		ForgotPasswordForm,
		LoginForm,
		PasswordResetForm,
		RegisterForm,
		ResendVerifyCodeForm,

		// Styleguide
		// ExampleStyleguideOnlyComponent,
	},

	mounted() {
		svg4everybody();
	},
});
