/**
 * WordPress dependencies
 */
import { store, getContext } from "@wordpress/interactivity";

const { state } = store("cuicpro-register", {
	state: {
		get isOpen() {
			return state.isOpen;
		},
		isOpen: true,
	},
	actions: {
		toggleOpen() {
			const context = getContext();
			context.isOpen = !context.isOpen;
		},
		toggleMatches() {
			const context = getContext();
			context.isOpen = !context.isOpen;
		},
	},
	callbacks: {
		logIsOpen: () => {
			console.log("toggleOpen");
		},
	},
});
