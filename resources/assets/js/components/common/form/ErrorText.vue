<script setup>
	defineProps({
		message: {
			type: String,
			required: true,
		},
	});
</script>

<template>
	<span
		class="font-bold text-xs text-red"
		v-text="message"
	/>
</template>
