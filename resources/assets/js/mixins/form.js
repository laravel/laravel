import Vue from 'vue';
import VueFormulate from '@braid/vue-formulate';
import get from 'lodash/get';

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
		element: '',
		input: ({ classification }) => {
			switch (classification) {
				case 'box':
				case 'button':
				case 'submit':
				case 'group':
					return null;
				default:
					return 'e-input block w-full p-4 bg-white border border-grey-500 rounded-none text-black focus:border-grey-700 focus:shadow-none';
			}
		},
		label: ({ classification }) => {
			switch (classification) {
				case 'box':
					return 'sr-only';
				default:
					return 'block mb-1/2em cursor-pointer font-bold leading-snug';
			}
		},
		help: 'text-sm mt-1/2em text-gray-600',
		error: 'inline-flex relative mt-1/2em text-red italic text-sm leading-snug',
		decorator: 'bg-green',
	},
});

export default {
	props: {
		action: {
			type: String,
			required: true,
		},
	},

	data() {
		return {
			message: null,
			errors: {},
			form: {},
			isSubmitting: false,
			isError: false,
		};
	},

	computed: {
		cIsSubmitEnabled() {
			return (!this.$data.isSubmitting);
		},
	},

	methods: {
		onSubmit() {
			this.$data.isSubmitting = true;

			axios
				.post(this.$props.action, this.$data.form)
				.then(this.onSubmitSuccess)
				.catch(this.onSubmitFailure)
				.then(this.onSubmitAlways, this.onSubmitAlways);
		},

		onSubmitSuccess(response) {
			const redirect = get(response, 'data.redirect');

			if (redirect) {
				window.location = redirect;
			} else {
				this.$data.isError = false;
				this.$data.form = {};
			}
		},

		onSubmitFailure(error) {
			this.$data.isError = true;

			if (get(error, 'response.status') !== 422) {
				return;
			}

			this.$data.errorMessage = get(error, 'response.data.message');
			this.$data.errors = (get(error, 'response.data.errors') || {});

			this.scrollToError();
		},

		onSubmitAlways() {
			this.$data.isSubmitting = false;
		},

		scrollToError() {
			if (!this.$data.errors) return;

			this.$nextTick(() => {
				const $firstError = this.$refs.fields.filter(el => el.errors)[0].$el;

				$firstError.scrollIntoView({
					behavior: 'smooth',
					block: 'center',
				});
			});
		},
	},
};
