<template>
	<div class="max-w-full overflow-x-auto">
		<table
			:class="cClasses"
		>
			<caption
				v-if="caption"
				v-html="caption"
			/>

			<thead v-if="headers">
				<th
					v-for="(header, index) in headers"
					:key="index"
					scope="col"
					class="align-top py-3 pr-3 font-bold text-left"
					:class="{
						'pl-3': index === 0,
					}"
					v-html="header"
				/>
			</thead>

			<tbody>
				<tr
					v-for="(row, index) in content"
					:key="index"
				>
					<td
						v-for="(cell, cellIndex) in row"
						:key="cellIndex"
						class="align-top py-3 pr-3 border-t border-solid border-grey-500"
						:class="{
							'pl-3': cellIndex === 0,
						}"
						v-html="cell"
					/>
				</tr>
			</tbody>
		</table>
	</div>
</template>

<script>
	export default {
		props: {
			caption: {
				type: String,
				default: null,
			},

			headers: {
				type: Object,
				default: null,
			},

			content: {
				type: Object,
				required: true,
			},
		},

		computed: {
			cClasses() {
				return [
					'w-full',
					'border',
					'border-solid',
					'border-grey-500',
					'border-collapse',
					'font-base',
					'leading-tight',
				];
			},
		},
	};
</script>
