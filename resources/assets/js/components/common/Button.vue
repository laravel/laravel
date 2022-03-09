<script setup>
	import { useAttrs } from 'vue';

	defineProps({
		title: {
			type: String,
			default: null,
		},
	});

	const attrs = useAttrs();
	const element = attrs.href ? 'a' : 'button';
</script>

<template>
	<component
		:is="element"
		:class="[
			'inline-block',
			'relative',
			'p-4',
			'bg-black text-white',
			'font-body text-base font-normal leading-none',
			'no-underline',
		]"
	>
		<slot>
			<span v-text="title" />
		</slot>
	</component>
</template>
