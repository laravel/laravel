<template>
	<div class="flex flex-wrap items-center">
		<template v-for="(page, index) in pages">
			<span
				v-if="index === ($props.pages.length - 1)"
				:key="`label-${index}`"
				class=""
				v-text="page.title"
			/>

			<template v-else>
				<a
					:key="`link-${index}`"
					class="mr-2"
					:href="page.url"
					v-text="page.title"
				/>

				<icon
					:key="`icon-${index}`"
					class="mr-2 text-sm text-grey-500"
					name="chevron-right"
				/>
			</template>
		</template>
	</div>
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
				itemClasses: [

				],
			};
		},
	};
</script>
