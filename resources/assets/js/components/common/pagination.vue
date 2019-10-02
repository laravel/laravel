<template>
	<ol class="flex justify-center">
		<li
			v-for="(page, index) in pages"
			:key="index"
			:class="itemClassList(page.type)"
		>
			<a
				:is="page.url ? 'a' : 'span'"
				:href="page.url"
				:class="linkClassList(page.type, page.disabled, page.current)"
				v-html="page.title"
			/>
		</li>
	</ol>
</template>

<script>
	export default {
		props: {
			pages: {
				type: Array,
				required: true,
			},
		},

		data() {
			return {
				hiddenSmallScreen: ['jump', 'prev', 'next', 'gap', 'page-end'],
			};
		},

		methods: {
			itemClassList(type) {
				const classes = ['mx-1', 'align-center'];

				if (this.$data.hiddenSmallScreen.includes(type)) {
					classes.push('hidden', 'md:flex');
				} else {
					classes.push('flex');
				}

				return classes;
			},

			linkClassList(type, isDisabled, isCurrent) {
				const classes = ['block', 'text-sm', 'leading-none', 'p-2'];

				if (isDisabled) {
					classes.push('bg-grey-300', 'cursor-not-allowed');
				} else if (isCurrent) {
					classes.push('bg-green');
				} else if (type !== 'gap') {
					classes.push('bg-blue');
				}

				if (type !== 'gap' && !isDisabled) {
					classes.push('text-white', 'no-underline', 'hover:bg-red');
				}

				return classes;
			},
		},
	};
</script>
