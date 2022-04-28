<script setup>
	defineProps({
		text: {
			type: String,
			required: true,
		},
	});
</script>

<template>
	<span
		class="font-bold"
		v-text="text"
	/>
</template>
