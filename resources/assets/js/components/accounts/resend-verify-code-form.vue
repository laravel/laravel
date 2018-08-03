<template>
	<form name="resend-verify-code" @submit.prevent="onSubmit" novalidate>
		<div
			v-if="message"
			v-text="message"
		></div>

		<div>
			<label>{{ 'accounts.resend_verify_code.labels.email' | trans }}</label>

			<input
				type="email"
				name="email"
				:disabled="isSubmitting"
				:placeholder="'accounts.resend_verify_code.placeholders.email' | trans"
				v-model="form.email"
			/>

			<error-text :errors="errors.email"></error-text>
		</div>

		<div>
			<a :href="loginUrl">{{ 'accounts.resend_verify_code.login' | trans }}</a> |
			<a :href="forgotPasswordUrl">{{ 'accounts.resend_verify_code.forgot_password' | trans }}</a>
		</div>

		<button
			type="submit"
			:disabled="!isSubmitEnabled"
		>{{ 'accounts.resend_verify_code.button' | trans }}</button>
	</form>
</template>

<script>
	import Form from '../../mixins/form';

	export default {
		mixins: [ Form ],

		props: {
			email: String,

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
		}
	}
</script>
