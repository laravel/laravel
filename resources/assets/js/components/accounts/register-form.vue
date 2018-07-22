<template>
	<form @submit.prevent="onSubmit" novalidate>
		<div
			v-if="errorMessage"
			v-text="errorMessage"
		></div>

		<div>
			<label>{{ 'accounts.register.labels.name' | trans }}</label>

			<input
				type="text"
				:disabled="isSubmitting"
				:placeholder="'accounts.register.placeholders.name' | trans"
				v-model="form.name"
			/>

			<error-text :errors="errors.name"></error-text>
		</div>

		<div>
			<label>{{ 'accounts.register.labels.email' | trans }}</label>

			<input
				type="email"
				:disabled="isSubmitting"
				:placeholder="'accounts.register.placeholders.email' | trans"
				v-model="form.email"
			/>

			<error-text :errors="errors.email"></error-text>
		</div>

		<div>
			<label>{{ 'accounts.register.labels.password' | trans }}</label>

			<input
				type="password"
				:disabled="isSubmitting"
				:placeholder="'accounts.register.placeholders.password' | trans"
				v-model="form.password"
			/>

			<error-text :errors="errors.password"></error-text>
		</div>

		<div>
			<label>{{ 'accounts.register.labels.password_confirmation' | trans }}</label>

			<input
				type="password"
				:disabled="isSubmitting"
				:placeholder="'accounts.register.placeholders.password_confirmation' | trans"
				v-model="form.password_confirmation"
			/>

			<error-text :errors="errors.password_confirmation"></error-text>
		</div>

		<button
			type="submit"
			:disabled="!isSubmitEnabled"
		>{{ 'accounts.register.button' | trans }}</button>
	</form>
</template>

<script>
	import Form from '../../mixins/form';

	export default {
		mixins: [ Form ],
	}
</script>
