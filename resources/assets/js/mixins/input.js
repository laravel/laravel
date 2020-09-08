export default {
	data() {
		return {
			hasKeyboardFocus: false,
		};
	},

	methods: {
		isKeyboard() {
			return window.whatInput.ask() === 'keyboard';
		},
	},
};
