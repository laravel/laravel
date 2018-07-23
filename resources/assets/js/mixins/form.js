import ErrorText from '../components/common/error-text.vue';

export default {
	props: {
		action: {
			type: String,
			required: true,
		},
	},

	data() {
		return {
			errorMessage: null,
			errors: {},
			form: {},
			isSubmitting: false,
		};
	},

	computed: {
		isSubmitEnabled() {
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
			const redirect = _.get(response, 'data.redirect');

			if (redirect) {
				window.location = redirect;
			}
		},

		onSubmitFailure(error) {
			if (_.get(error, 'response.status') !== 422) {
				return;
			}

			this.$data.errorMessage = _.get(error, 'response.data.message');
			this.$data.errors = (_.get(error, 'response.data.errors') || {});
		},

		onSubmitAlways() {
			this.$data.isSubmitting = false;
		},
	},

	components: {
		ErrorText,
	},
};
