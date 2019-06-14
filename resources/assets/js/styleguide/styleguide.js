/* eslint-disable no-new */

import Block from './block';
import Icon from './icon';

window.Vue.component('icon', Icon);

new window.Vue({
	el: '#app',

	components: {
		Block,
	},

	data() {
		return {
			toggle: {
				copy: true,
			},
		};
	},
});
