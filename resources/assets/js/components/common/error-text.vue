<template>
	<span v-if="errors" class="error-text">
		<p
			v-for="(message) in errors"
			:key="message"
			v-text="message"
		/>
	</span>
</template>

<script>
	export default {
		props: {
			errors: {
				type: Array,
				default() {
					return [];
				},
			},
		},

		computed: {
			messages() {
				return (!_.isEmpty(this.$props.errors))
					? this.$props.errors
					: null;
			},
		},
	};
</script>
