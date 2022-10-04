<script setup>
	import sprite from 'assets/img/sprite.svg';
	import { computed } from 'vue';

	const props = defineProps({
		name: {
			type: String,
			required: true,
		},

		url: {
			type: String,
			default: sprite,
		},
	});

	const asset = computed(() => `${props.url}#${props.name}`);
</script>

<template>
	<svg>
		<use :xlink:href="asset" />
	</svg>
</template>
