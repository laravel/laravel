<script setup>
	import { useAttrs, useSlots } from 'vue';
	import EImg from './Img';

	const props = defineProps({
		ratioClass: {
			type: String,
			default: 'pt-9/16',
		},

		tag: {
			type: String,
			default: 'div',
		},
	});

	const attrs = useAttrs();
	const slots = useSlots();

	const componentAttrs = slots.default ? attrs : null;
	const as = attrs.href ? 'a' : props.tag;
</script>

<script>
	export default {
		name: 'EPlaceholder',

		inheritAttrs: false,
	};
</script>

<template>
	<component
		:is="as"
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
