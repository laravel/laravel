<template>
	<form @submit.prevent="onSubmit" novalidate>
		<div
			v-if="errorMessage"
			v-text="errorMessage"
		></div>

		<div>
			<label>{{ 'accounts.login.labels.email' | trans }}</label>

			<input
				type="email"
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
				:disabled="isSubmitting"
				:placeholder="'accounts.login.placeholders.password' | trans"
				v-model="form.password"
			/>

			<error-text :errors="errors.password"></error-text>
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
	}
</script>
