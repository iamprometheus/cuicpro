/**
 * WordPress dependencies
 */
import { store, getContext } from "@wordpress/interactivity";

const { state } = store("cuicpro-brackets", {
	state: {
		isBracketOpen: false,
		bracketId: null,
	},
	actions: {
		toggleBracket() {
			const context = getContext();

			const bracketId = context.bracketId;

			for (const key in context.bracketsState) {
				if (key === "bracketId_" + bracketId) continue;
				if (key.startsWith("bracketId_")) context.bracketsState[key] = false;
			}

			// clean scroll events
			jQuery("#bracket_" + bracketId).off("scroll");
			jQuery(".leader-line").remove();

			state.bracketId = bracketId;

			state.isBracketOpen = !context.bracketsState["bracketId_" + bracketId];

			context.bracketsState["bracketId_" + bracketId] =
				!context.bracketsState["bracketId_" + bracketId];
		},
	},
	callbacks: {
		drawLines: () => {
			const context = getContext();
			const bracketId = context.bracketId;

			// clean scroll events
			jQuery("#bracket_" + bracketId).off("scroll");

			// add lines to elements
			if (!state.isBracketOpen || state.bracketId !== bracketId) return;
			jQuery(".leader-line").remove();

			const bracket = jQuery("#bracket_" + bracketId);
			const rounds = bracket.find(".bracket-round");

			for (let i = rounds.length - 1; i > 0; i--) {
				const matches = rounds[i].children;
				for (let j = 0; j < matches.length; j++) {
					const match = matches[j];
					const matchAttr = match.attributes;
					if (
						!matchAttr["class"].value.includes("line-required-up") &&
						!matchAttr["class"].value.includes("line-required-down")
					)
						continue;
					const matchId = matchAttr["id"].value;

					const matchLink1 = rounds[i - 1].children[j * 2];
					const matchLink2 = rounds[i - 1].children[j * 2 + 1];

					if (matchAttr["class"].value.includes("line-required-up")) {
						const line1 = new LeaderLine(
							document.getElementById(matchLink1.attributes["id"].value),
							document.getElementById(matchId),
							{
								color: "#f6931f",
								size: 2,
								endPlug: "behind",
								endPlugSize: 2,
								path: "grid",
								startSocket: "right",
								endSocket: "left",
							},
						);

						jQuery("#bracket_" + bracketId).on("scroll", function () {
							line1.position();
						});
					}

					if (matchAttr["class"].value.includes("line-required-down")) {
						const line2 = new LeaderLine(
							document.getElementById(matchLink2.attributes["id"].value),
							document.getElementById(matchId),
							{
								color: "#f6931f",
								size: 2,
								endPlug: "behind",
								endPlugSize: 2,
								path: "grid",
								startSocket: "right",
								endSocket: "left",
							},
						);

						jQuery("#bracket_" + bracketId).on("scroll", function () {
							line2.position();
						});
					}
				}
			}

			const container = jQuery("#brackets-data");

			const lines = jQuery(".leader-line");
			jQuery("#bracket_" + bracketId).on("scroll", function () {
				lines.each(function (line) {
					lines[line].style.removeProperty("display");
					const lineEl = lines[line];
					const containerRect = container[0].getBoundingClientRect();
					const lineRect = lineEl.getBoundingClientRect();

					const isOutside =
						lineRect.right > containerRect.right ||
						lineRect.left < containerRect.left;

					if (!isOutside) {
						lineEl.style.removeProperty("display");
					} else {
						lineEl.style.setProperty("display", "none");
					}
				});
			});
		},
	},
});
