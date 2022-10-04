<script setup>
	import SvgSprite from './SvgSprite';

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
	<svg-sprite
		:class="[
			'fill-current align-top',
			block ? 'block' : 'inline-block',
			size,
		]"
		:name="name"
	/>
</template>
