<template>
	<form name="resend-verify-code" novalidate @submit.prevent="onSubmit">
		<div
			v-if="message"
			v-text="message"
		/>

		<div>
			<label>{{ 'accounts.resend_verify_code.labels.email' | trans }}</label>

			<input
				v-model="form.email"
				type="email"
				name="email"
				:disabled="isSubmitting"
				:placeholder="'accounts.resend_verify_code.placeholders.email' | trans"
			>

			<error-text :errors="errors.email" />
		</div>

		<div>
			<a :href="loginUrl">{{ 'accounts.resend_verify_code.login' | trans }}</a> |
			<a :href="forgotPasswordUrl">{{ 'accounts.resend_verify_code.forgot_password' | trans }}</a>
		</div>

		<button
			type="submit"
			:disabled="!isSubmitEnabled"
		>
			{{ 'accounts.resend_verify_code.button' | trans }}
		</button>
	</form>
</template>

<script>
	import Form from '../../mixins/form';

	export default {
		mixins: [Form],

		props: {
			email: {
				type: String,
				default: '',
			},

			loginUrl: {
				type: String,
				required: true,
			},

			forgotPasswordUrl: {
				type: String,
				required: true,
			},
		},

		data() {
			return {
				form: {
					email: this.$props.email,
				},
			};
		},

		methods: {
		},
	};
</script>
