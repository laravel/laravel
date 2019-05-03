<template>
	<form name="register" novalidate @submit.prevent="onSubmit">
		<div
			v-if="message"
			v-text="message"
		/>

		<div>
			<label>{{ 'accounts.register.labels.name' | trans }}</label>

			<input
				v-model="form.name"
				type="text"
				name="name"
				:disabled="isSubmitting"
				:placeholder="'accounts.register.placeholders.name' | trans"
			>

			<error-text :errors="errors.name" />
		</div>

		<div>
			<label>{{ 'accounts.register.labels.email' | trans }}</label>

			<input
				v-model="form.email"
				type="email"
				name="email"
				:disabled="isSubmitting"
				:placeholder="'accounts.register.placeholders.email' | trans"
			>

			<error-text :errors="errors.email" />
		</div>

		<div>
			<label>{{ 'accounts.register.labels.password' | trans }}</label>

			<input
				v-model="form.password"
				type="password"
				name="password"
				:disabled="isSubmitting"
				:placeholder="'accounts.register.placeholders.password' | trans"
			>

			<error-text :errors="errors.password" />
		</div>

		<div>
			<label>{{ 'accounts.register.labels.password_confirmation' | trans }}</label>

			<input
				v-model="form.password_confirmation"
				type="password"
				name="password_confirmation"
				:disabled="isSubmitting"
				:placeholder="'accounts.register.placeholders.password_confirmation' | trans"
			>

			<error-text :errors="errors.password_confirmation" />
		</div>

		<div>
			<a :href="loginUrl">{{ 'accounts.register.login' | trans }}</a>
		</div>

		<button
			type="submit"
			:disabled="!isSubmitEnabled"
		>
			{{ 'accounts.register.button' | trans }}
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
