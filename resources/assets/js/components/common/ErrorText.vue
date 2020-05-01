<template>
	<div
		v-if="$props.errors"
		class="error-text inline-flex relative mt-1 text-red italic text-sm leading-snug"
	>
		<p
			v-for="(message) in $props.errors"
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
