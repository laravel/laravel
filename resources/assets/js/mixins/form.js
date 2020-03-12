import get from 'lodash/get';

import CheckboxWrapper from '../components/common/form/CheckboxWrapper';
import InputWrapper from '../components/common/form/InputWrapper';

export default {
	components: {
		CheckboxWrapper,
		InputWrapper,
	},

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
