import whatInput from 'what-input';

export default {
	data() {
		return {
			hasKeyboardFocus: false,
		};
	},

	methods: {
		isKeyboard() {
			return whatInput.ask() === 'keyboard';
		},
	},
};
