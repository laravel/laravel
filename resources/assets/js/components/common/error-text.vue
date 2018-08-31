<template>
	<span class="error-text" v-if="errors">
		<p v-for="(message) in errors" :key="message" v-text="message"></p>
	</span>
</template>

<script>
	export default {
		props: {
			errors: Array,
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
