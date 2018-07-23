<template>
	<form name="password-reset" @submit.prevent="onSubmit" novalidate>
		<div
			v-if="errorMessage"
			v-text="errorMessage"
		></div>

		<div>
			<label>{{ 'accounts.password_reset.labels.email' | trans }}</label>

			<input
				type="email"
				name="email"
				:disabled="isSubmitting"
				:placeholder="'accounts.password_reset.placeholders.email' | trans"
				v-model="form.email"
			/>

			<error-text :errors="errors.email"></error-text>
		</div>

		<div>
			<label>{{ 'accounts.password_reset.labels.password' | trans }}</label>

			<input
				type="password"
				name="password"
				:disabled="isSubmitting"
				:placeholder="'accounts.password_reset.placeholders.password' | trans"
				v-model="form.password"
			/>

			<error-text :errors="errors.password"></error-text>
		</div>

		<div>
			<label>{{ 'accounts.password_reset.labels.password_confirmation' | trans }}</label>

			<input
				type="password"
				name="password_confirmation"
				:disabled="isSubmitting"
				:placeholder="'accounts.password_reset.placeholders.password_confirmation' | trans"
				v-model="form.password_confirmation"
			/>

			<error-text :errors="errors.password_confirmation"></error-text>
		</div>

		<button
			type="submit"
			:disabled="!isSubmitEnabled"
		>{{ 'accounts.password_reset.button' | trans }}</button>
	</form>
</template>

<script>
	import Form from '../../mixins/form';

	export default {
		mixins: [ Form ],

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
	}
</script>
