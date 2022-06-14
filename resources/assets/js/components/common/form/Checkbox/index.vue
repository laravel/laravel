<script setup>
	import { useAttrs, computed } from 'vue';

	import SingleCheckbox from './SingleCheckbox';
	import MultipleCheckboxes from './MultipleCheckboxes';

	const attrs = useAttrs();
	const element = computed(() => (attrs.options ? MultipleCheckboxes : SingleCheckbox));
</script>

<script>
	export default {
		name: 'ECheckbox',
	};
</script>

<template>
	<component
		:is="element"
		v-bind="$attrs"
	/>
</template>
