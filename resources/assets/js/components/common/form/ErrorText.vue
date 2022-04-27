<script setup>
	import { ErrorMessage } from 'vee-validate';

	defineProps({
		name: {
			type: String,
			required: true,
		},
	});
</script>

<template>
	<error-message
		class="font-bold text-xs text-red"
		:name="name"
	/>
</template>
