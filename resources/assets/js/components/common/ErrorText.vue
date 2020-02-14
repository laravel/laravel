<template>
	<div
		v-if="errors"
		class="error-text inline-flex relative mt-1 text-red italic text-sm leading-snug"
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
	};
</script>
