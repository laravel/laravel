<script setup>
	import { useAttrs, useSlots } from 'vue';

	defineProps({
		as: {
			type: String,
			default: 'div',
		},

		ratioClass: {
			type: String,
			default: 'aspect-16/9',
		},
	});

	const attrs = useAttrs();
	const slots = useSlots();

	const componentAttrs = slots.default ? attrs : null;
</script>

<script>
	export default {
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
			<ui-img
				overlay
				v-bind="$attrs"
			/>
		</slot>
	</component>
</template>
