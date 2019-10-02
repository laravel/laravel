<template>
	<label
		class="block mb-1/2em pointer-cursor font-bold leading-snug"
		v-html="text"
	/>
</template>

<script>
	export default {
		props: {
			text: {
				type: String,
				required: true,
			},
		},
	};
</script>
