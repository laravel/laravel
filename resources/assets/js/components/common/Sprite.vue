<script setup>
	import sprite from 'assets/img/sprite.svg';

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

	const asset = `${props.url}#${props.name}`;
</script>

<template>
	<svg>
		<use :xlink:href="asset" />
	</svg>
</template>
