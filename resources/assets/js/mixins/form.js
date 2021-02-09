import get from 'lodash/get';

export default {
	props: {
		action: {
			type: String,
			required: true,
		},

		schema: {
			type: [Array, Object],
			default: null,
		},

		values: {
			type: Object,
			default() {},
		},
	},

	data() {
		return {
			message: null,
			errors: {},
			form: this.$props.values || {},
			isSubmitting: false,
			isError: false,
			response: null,
		};
	},

	computed: {
		cIsSubmitEnabled() {
			return !this.$data.isSubmitting && !this.$data.response;
		},
	},

	methods: {
		onSubmit() {
			if (this.$data.response) return;

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

				const resp = get(response, 'data.response');

				if (resp) {
					this.$data.response = resp;
				}
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
				const $firstError = this.$el.querySelector('.e-error');

				if (!$firstError) return;

				$firstError.scrollIntoView({
					behavior: 'smooth',
					block: 'center',
				});
			});
		},
	},
};
