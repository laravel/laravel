import Vue from 'vue';
import VueFormulate from '@braid/vue-formulate';

import Box from '../components/common/form/Box';
import ESelect from '../components/common/form/Select';

Vue.component('Box', Box);
Vue.component('ESelect', ESelect);

Vue.use(VueFormulate, {
	library: {
		checkbox: {
			component: 'Box',
		},
		button: {
			component: 'EButton',
		},
		radio: {
			component: 'Box',
		},
		submit: {
			component: 'EButton',
		},
		select: {
			component: 'ESelect',
		},
	},
	classes: {
		outer: '',
		wrapper: '',
		element: ({ classification }) => {
			switch (classification) {
				case 'group':
					return 'space-y-5';
				default:
					return null;
			}
		},
		input: ({ classification }) => {
			switch (classification) {
				case 'box':
				case 'button':
				case 'submit':
				case 'group':
					return null;
				default:
					return 'block w-full p-4 py-3 text-black text-base font-body font-normal bg-white border border-grey-500 rounded-none placeholder-grey-400 bg-clip-padding';
			}
		},
		label: ({ classification }) => {
			switch (classification) {
				case 'box':
					return 'sr-only';
				default:
					return 'block mb-1/2em cursor-pointer';
			}
		},
		help: 'mt-1/2em',
		errors: '',
		error: 'inline-flex relative mt-1/2em text-red italic text-sm leading-snug',
		decorator: 'bg-green',
	},
});
