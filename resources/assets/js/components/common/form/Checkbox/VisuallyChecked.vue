<script setup>
	defineProps({
		checked: Boolean,
	});
</script>
<template>
	<div
		:class="[
			'relative flex-shrink-0 w-8 h-8 mr-2',
			'peer-focus:ring-2 peer-focus:ring-focus',
		]"
	>
		&nbsp;

		<div class="absolute top-1/2 left-0 w-full pt-full transform -translate-y-1/2">
			<div
				:class="[
					'flex items-center justify-center absolute inset-0',
					'bg-grey-100 border-1 border-grey-900',
					{
						'bg-grey-200': checked,
					},
				]"
			>
				<e-icon
					:class="[
						'transform transition-transform duration-200 ease-out-cubic',
						checked ? 'scale-90' : 'scale-0',
					]"
					size="w-full h-full"
					name="check"
				/>
			</div>
		</div>
	</div>
</template>
