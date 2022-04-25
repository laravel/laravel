<script setup>
	import { useAttrs, useSlots } from 'vue';
	import EImg from './Img';

	defineProps({
		ratioClass: {
			type: String,
			default: 'pt-9/16',
		},

		as: {
			type: String,
			default: 'div',
		},
	});

	const attrs = useAttrs();
	const slots = useSlots();

	const componentAttrs = slots.default ? attrs : null;
</script>

<script>
	export default {
		name: 'EPlaceholder',

		inheritAttrs: false,
	};
</script>

<template>
	<component
		:is="$attrs.href ? 'a' : as"
		:class="ratioClass"
		class="block relative w-full"
		v-bind="componentAttrs"
	>
		<slot>
			<e-img
				overlay
				v-bind="$attrs"
			/>
		</slot>
	</component>
</template>
