export default {
	data() {
		return {
			hasKeyboardFocus: false,
		};
	},

	methods: {
		onWhatFocus() {
			this.$data.hasKeyboardFocus = window.whatInput.ask() === 'keyboard';
		},

		onWhatBlur() {
			this.$data.hasKeyboardFocus = false;
		},
	},
};
