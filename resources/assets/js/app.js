/* eslint-disable no-new */
import Vue from 'vue';

import './etc/forms';

// Common
import EButton from './components/common/Button';
import Icon from './components/common/Icon';
import Placeholder from './components/common/Placeholder';

// Global
Vue.component('EButton', EButton);
Vue.component('Icon', Icon);
Vue.component('Placeholder', Placeholder);

new Vue({
	el: '#app',
});
