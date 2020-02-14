<template>
	<form name="login" novalidate @submit.prevent="onSubmit">
		<div
			v-if="message"
			v-text="message"
		/>

		<div>
			<label>{{ 'accounts.login.labels.email' | trans }}</label>

			<input
				v-model="form.email"
				type="email"
				name="email"
				:disabled="isSubmitting"
				:placeholder="'accounts.login.placeholders.email' | trans"
			>

			<error-text :errors="errors.email" />
		</div>

		<div>
			<label>{{ 'accounts.login.labels.password' | trans }}</label>

			<input
				v-model="form.password"
				type="password"
				name="password"
				:disabled="isSubmitting"
				:placeholder="'accounts.login.placeholders.password' | trans"
			>

			<error-text :errors="errors.password" />
		</div>

		<div>
			<a :href="forgotPasswordUrl">{{ 'accounts.login.forgot_password' | trans }}</a> |
			<a :href="registerUrl">{{ 'accounts.login.register' | trans }}</a>
		</div>

		<button
			type="submit"
			:disabled="!isSubmitEnabled"
		>
			{{ 'accounts.login.button' | trans }}
		</button>
	</form>
</template>

<script>
	import Form from '../../mixins/form';

	export default {
		mixins: [Form],

		props: {
			forgotPasswordUrl: {
				type: String,
				required: true,
			},

			registerUrl: {
				type: String,
				required: true,
			},
		},
	};
</script>
