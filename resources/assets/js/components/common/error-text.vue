<template>
	<div
		v-if="errors"
		class="error-text inline-flex relative mt-1 text-red italic text-sm leading-sm"
	>
		<p
			v-for="(message) in errors"
			:key="message"
			v-text="message"
		/>
	</div>
</template>

<script>
	export default {
		props: {
			errors: {
				type: Array,
				default: null,
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
