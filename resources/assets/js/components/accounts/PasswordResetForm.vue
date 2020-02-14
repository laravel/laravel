<template>
	<form name="password-reset" novalidate @submit.prevent="onSubmit">
		<div
			v-if="message"
			v-text="message"
		/>

		<div>
			<label>{{ 'accounts.password_reset.labels.email' | trans }}</label>

			<input
				v-model="form.email"
				type="email"
				name="email"
				:disabled="isSubmitting"
				:placeholder="'accounts.password_reset.placeholders.email' | trans"
			>

			<error-text :errors="errors.email" />
		</div>

		<div>
			<label>{{ 'accounts.password_reset.labels.password' | trans }}</label>

			<input
				v-model="form.password"
				type="password"
				name="password"
				:disabled="isSubmitting"
				:placeholder="'accounts.password_reset.placeholders.password' | trans"
			>

			<error-text :errors="errors.password" />
		</div>

		<div>
			<label>{{ 'accounts.password_reset.labels.password_confirmation' | trans }}</label>

			<input
				v-model="form.password_confirmation"
				type="password"
				name="password_confirmation"
				:disabled="isSubmitting"
				:placeholder="'accounts.password_reset.placeholders.password_confirmation' | trans"
			>

			<error-text :errors="errors.password_confirmation" />
		</div>

		<button
			type="submit"
			:disabled="!isSubmitEnabled"
		>
			{{ 'accounts.password_reset.button' | trans }}
		</button>
	</form>
</template>

<script>
	import Form from '../../mixins/form';

	export default {
		mixins: [Form],

		props: {
			token: {
				type: String,
				required: true,
			},
		},

		data() {
			return {
				form: {
					token: this.$props.token,
				},
			};
		},
	};
</script>
