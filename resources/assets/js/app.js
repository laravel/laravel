/* eslint-disable no-new */
import Vue from 'vue';

import './bootstrap';
import lang from './i18n';

// Common
import EButton from './components/common/Button';
import Icon from './components/common/Icon';
import IconText from './components/common/IconText';
import Placeholder from './components/common/Placeholder';

Vue.filter('trans', (...args) => lang.get(...args));

// Global
Vue.component('EButton', EButton);
Vue.component('Icon', Icon);
Vue.component('IconText', IconText);
Vue.component('Placeholder', Placeholder);

new Vue({
	el: '#app',
});
