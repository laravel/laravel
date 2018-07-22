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
		onSubmit: function () {
			this.$data.isSubmitting = true;

			axios
				.post(this.$props.action, this.$data.form)
				.then(this.onSubmitSuccess)
				.catch(this.onSubmitFailure);
		},

		onSubmitSuccess: function (response) {
			window.location = _.get(response, 'data.redirect');
		},

		onSubmitFailure: function (error) {
			this.$data.isSubmitting = false;

			if (_.get(error, 'response.status') !== 422) {
				return;
			}

			this.$data.errorMessage = _.get(error, 'response.data.message');
			this.$data.errors = (_.get(error, 'response.data.errors') || {});
		},
	},

	components: {
		ErrorText,
	},
};
