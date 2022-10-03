<script setup>
	defineProps({
		checked: Boolean,

		radio: Boolean,
	});
</script>

<template>
	<div class="e-focus-sibling shrink-0 relative w-6">
		&nbsp;

		<div
			:class="[
				'e-focus-sibling__outline',
				'flex items-center justify-center',
				'absolute top-1/2 left-0 w-full aspect-square -translate-y-1/2',
				'border border-grey-900',
				checked ? 'border-grey-200' : 'bg-grey-100',
				{ 'rounded-full': radio },
			]"
		>
			<e-icon
				:class="[
					'transition-transform duration-200 ease-out-cubic',
					checked ? 'scale-90' : 'scale-0',
				]"
				size="w-full h-full"
				name="check"
			/>
		</div>
	</div>
</template>
