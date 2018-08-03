<template>
	<span class="error-text" v-if="messages">
		<span v-for="message in messages" v-text="message"></span>
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
