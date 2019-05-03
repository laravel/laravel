<template>
	<form name="forgot-password" novalidate @submit.prevent="onSubmit">
		<div
			v-if="message"
			v-text="message"
		/>

		<div>
			<label>{{ 'accounts.forgot_password.labels.email' | trans }}</label>

			<input
				v-model="form.email"
				type="email"
				name="email"
				:disabled="isSubmitting"
				:placeholder="'accounts.forgot_password.placeholders.email' | trans"
			>

			<error-text :errors="errors.email" />
		</div>

		<div>
			<a :href="loginUrl">{{ 'accounts.forgot_password.login' | trans }}</a> |
			<a :href="registerUrl">{{ 'accounts.forgot_password.register' | trans }}</a>
		</div>

		<button
			type="submit"
			:disabled="!isSubmitEnabled"
		>
			{{ 'accounts.forgot_password.button' | trans }}
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

			registerUrl: {
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
	};
</script>
