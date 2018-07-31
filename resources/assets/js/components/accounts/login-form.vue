<template>
	<form name="login" @submit.prevent="onSubmit" novalidate>
		<div
			v-if="message"
			v-text="message"
		></div>

		<div>
			<label>{{ 'accounts.login.labels.email' | trans }}</label>

			<input
				type="email"
				name="email"
				:disabled="isSubmitting"
				:placeholder="'accounts.login.placeholders.email' | trans"
				v-model="form.email"
			/>

			<error-text :errors="errors.email"></error-text>
		</div>

		<div>
			<label>{{ 'accounts.login.labels.password' | trans }}</label>

			<input
				type="password"
				name="password"
				:disabled="isSubmitting"
				:placeholder="'accounts.login.placeholders.password' | trans"
				v-model="form.password"
			/>

			<error-text :errors="errors.password"></error-text>
		</div>

		<div>
			<a :href="forgotPasswordUrl">{{ 'accounts.login.forgot_password' | trans }}</a> |
			<a :href="registerUrl">{{ 'accounts.login.register' | trans }}</a>
		</div>

		<button
			type="submit"
			:disabled="!isSubmitEnabled"
		>{{ 'accounts.login.button' | trans }}</button>
	</form>
</template>

<script>
	import Form from '../../mixins/form';

	export default {
		mixins: [ Form ],

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
	}
</script>
