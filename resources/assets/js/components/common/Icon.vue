<script setup>
	import ESprite from './Sprite';

	defineProps({
		name: {
			type: String,
			required: true,
		},

		size: {
			type: String,
			default: 'w-em h-em',
		},

		block: Boolean,
	});
</script>

<template>
	<e-sprite
		:class="[
			'fill-current align-top',
			block ? 'block' : 'inline-block',
			size,
		]"
		:name="name"
	/>
</template>
