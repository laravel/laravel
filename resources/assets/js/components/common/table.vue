<template>
	<div class="table-wrapper">
		<table>
			<caption v-if="caption" v-html="caption" />

			<thead v-if="headers">
				<th
					v-for="(header, index) in headers"
					:key="index"
					scope="col"
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
	};
</script>
