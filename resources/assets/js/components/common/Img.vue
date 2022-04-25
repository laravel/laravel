<script setup>
	import { computed } from 'vue';

	const props = defineProps({
		classList: {
			type: [Array, String],
			default: null,
		},

		loading: {
			type: String,
			default: 'lazy',
		},

		sizes: {
			type: Array,
			default: null,
		},

		src: {
			type: Array,
			required: true,
		},

		webp: {
			type: Array,
			required: true,
		},

		overlay: Boolean,
	});

	const srcSet = (webp) => {
		const type = webp ? 'image/webp' : null;
		const sizes = props.sizes || [];

		return props[webp ? 'webp' : 'src']
			.slice(0, webp ? undefined : -1)
			.map((srcset, index) => ({
				srcset,
				media: sizes[index] ? `(min-width: ${sizes[index]}px)` : null,
				type,
			}));
	};

	const source = computed(() => ({
		default: props.src.at(-1),
		srcSet: srcSet(),
		srcSetWebP: srcSet(true),
	}));
</script>

<script>
	export default {
		name: 'EImg',

		inheritAttrs: false,
	};
</script>

<template>
	<picture :key="source.default">
		<source
			v-for="item in source.srcSetWebP"
			:key="item.srcset"
			v-bind="item"
		>

		<source
			v-for="item in source.srcSet"
			:key="item.srcset"
			v-bind="item"
		>

		<img
			v-bind="$attrs"
			:class="[
				[classList].flat(),
				{
					'absolute top-0 left-0 w-full h-full object-cover': overlay,
				},
			]"
			:loading="loading"
			:src="source.default"
		>
	</picture>
</template>
