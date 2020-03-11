/* eslint-disable no-new */

import Vue from 'vue';
import svg4everybody from 'svg4everybody';

import './bootstrap';
import lang from './i18n';

// Common
import EButton from './components/common/Button';
import EForm from './components/common/Form';
import ETable from './components/common/Table';
import Breadcrumb from './components/common/Breadcrumb';
import Icon from './components/common/Icon';
import IconText from './components/common/IconText';
import Pagination from './components/common/Pagination';
import Placeholder from './components/common/Placeholder';

// Accounts
// import ForgotPasswordForm from './components/accounts/ForgotPasswordForm';
// import LoginForm from './components/accounts/LoginForm';
// import PasswordResetForm from './components/accounts/PasswordResetForm';
// import RegisterForm from './components/accounts/RegisterForm';
// import ResendVerifyCodeForm from './components/accounts/ResendVerifyCodeForm';

Vue.filter('trans', (...args) => lang.get(...args));

// Global
Vue.component('EButton', EButton);
Vue.component('EForm', EForm);
Vue.component('ETable', ETable);
Vue.component('Breadcrumb', Breadcrumb);
Vue.component('Icon', Icon);
Vue.component('IconText', IconText);
Vue.component('Pagination', Pagination);
Vue.component('Placeholder', Placeholder);

new Vue({
	el: '#app',

	// Local
	components: {
		// App
		// ForgotPasswordForm,
		// LoginForm,
		// PasswordResetForm,
		// RegisterForm,
		// ResendVerifyCodeForm,

		// Styleguide
		// ExampleStyleguideOnlyComponent,
	},

	mounted() {
		svg4everybody();
	},
});
