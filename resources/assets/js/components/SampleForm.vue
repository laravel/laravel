<template>
	<div>
		<formulate-form
			v-model="form"
		>
			<formulate-input
				type="text"
				name="name"
				label="Name"
				placeholder="Default placeholder"
				error="Default error"
			/>

			<formulate-input
				type="textarea"
				name="message"
				label="Message"
				rows="5"
				placeholder="Default placeholder"
				error="Default error"
			/>

			<formulate-input
				type="password"
				name="password"
				label="New password"
				help="Pick a new password, must have at least 1 number."
				validation="^required|min:5,length|matches:/[0-9]/"
				:validation-messages="{
					matches: 'Password must contain at least 1 number.',
				}"
			/>

			<formulate-input
				type="password"
				name="password_confirm"
				label="Confirm password"
				help="Just re-type what you entered above"
				validation="^required|confirm"
				validation-name="Password confirmation"
			/>

			<formulate-input
				type="radio"
				label="Select your payment method"
				name="payment"
				:options="{
					paypal: 'PayPal',
					credit: 'Credit Card',
				}"
			/>

			<formulate-input
				type="checkbox"
				label="Select your payment method"
				name="paymentoptions"
				:options="{
					paypal: 'PayPal',
					credit: 'Credit Card',
				}"
			/>

			<formulate-input
				type="checkbox"
				label="Single checkbox"
				name="checkbox"
			/>

			<formulate-input
				type="checkbox"
				label="Single checkbox"
				label-position="before"
				name="checkbox"
				disabled
			/>

			<formulate-input
				type="select"
				name="state"
				label="State"
				placeholder="Choose a time"
				:options="{
					VA: 'Virginia',
					CA: 'California',
					NY: 'New York',
				}"
			/>

			<formulate-input
				type="button"
				label="Just a button"
			/>

			<formulate-input
				type="submit"
				label="Purchase tickets"
			/>
		</formulate-form>
	</div>
</template>

<script>
	import form from '../mixins/form';

	export default {
		mixins: [
			form,
		],

		data() {
			return {};
		},
	};
</script>
