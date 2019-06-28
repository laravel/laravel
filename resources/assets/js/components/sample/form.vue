<template>
	<form>
		<div>
			<e-input
				id="testing"
				v-model="form.testing"
				label="Testing label"
				type="email"
				:error="errors.testing"
			/>
		</div>

		<div class="push-top">
			<e-input
				id="checkbox"
				v-model="form.checkbox"
				label="Checkbox label"
				type="checkbox"
				:error="errors.checkbox"
			/>
		</div>
	</form>
</template>

<script>
	import Form from '../../mixins/form';

	export default {
		mixins: [
			Form,
		],
	};
</script>
