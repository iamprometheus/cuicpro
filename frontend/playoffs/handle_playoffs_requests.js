jQuery(document).ready(function ($) {
	jQuery(document).on("change", "#tournament-select-playoffs", function () {
		const tournamentID = $("#tournament-select-playoffs").val();
		$.ajax({
			url: cuicpro.ajax_url,
			type: "POST",
			data: {
				action: "fetch_tournament_divisions_playoffs",
				tournament_id: tournamentID,
			},
			success: function (response) {
				if (response.success) {
					$("#division-select-playoffs").html(response.data.divisions);

					$("#brackets-container").html(response.data.html);

					$(".leader-line").remove();
					$("#leader-line-defs").remove();
					const elements = response.data.elements;

					if (elements !== null) {
						for (let playoff in elements) {
							const container = jQuery(`#playoff_${playoff} .rounds-container`);
							container.off("scroll");
							for (let i = elements[playoff].length - 1; i > 0; i--) {
								for (let match in elements[playoff][i]) {
									for (let j = 0; j < elements[playoff][i][match].length; j++) {
										const startElement = document.getElementById(
											elements[playoff][i][match][j],
										);
										const endElement = document.getElementById(match);

										const line = new LeaderLine(startElement, endElement, {
											color: "#000000",
											size: 2,
											endPlug: "behind",
											endPlugSize: 2,
											path: "grid",
											endSocket: "left",
											startSocket: "right",
										});

										container.on("scroll", () => {
											line.position();
										});
									}
								}
							}

							let lines = jQuery(".leader-line");
							const containerRect = container[0].getBoundingClientRect();

							// filter lines not belonging to current container
							lines = lines.filter((line) => {
								const lineEl = lines[line];
								const lineRect = lineEl.getBoundingClientRect();

								const isOutside =
									lineRect.top < containerRect.top ||
									lineRect.bottom > containerRect.bottom;

								return !isOutside;
							});

							lines.each(function (line) {
								lines[line].style.removeProperty("display");
								const lineEl = lines[line];
								const lineRect = lineEl.getBoundingClientRect();

								const isOutside =
									lineRect.right > containerRect.right ||
									lineRect.left < containerRect.left;

								if (isOutside) {
									lineEl.style.setProperty("display", "none");
								} else {
									lineEl.style.removeProperty("display");
								}
							});

							jQuery(`#playoff_${playoff} .rounds-container`).on(
								"scroll",
								function () {
									lines.each(function (index) {
										lines[index].style.removeProperty("display");
										const lineEl = lines[index];
										const lineRect = lineEl.getBoundingClientRect();

										const isOutside =
											lineRect.right > containerRect.right ||
											lineRect.left < containerRect.left;

										if (isOutside) {
											lineEl.style.setProperty("display", "none");
										} else {
											lineEl.style.removeProperty("display");
										}
									});
								},
							);
						}
					}
				}
			},
			error: function (xhr, status, error) {
				console.error("Error:", error);
			},
		});
	});

	jQuery(document).on("change", "#division-select-playoffs", function () {
		const divisionID = $(this).val();
		const tournamentID = $("#tournament-select-playoffs").val();
		$.ajax({
			url: cuicpro.ajax_url,
			type: "POST",
			data: {
				action: "fetch_division_playoffs",
				division_id: divisionID,
				tournament_id: tournamentID,
			},
			success: function (response) {
				if (response.success) {
					$("#brackets-container").html(response.data.html);

					$(".leader-line").remove();
					$("#leader-line-defs").remove();
					const elements = response.data.elements;

					if (elements !== null) {
						for (let playoff in elements) {
							const container = jQuery(`#playoff_${playoff} .rounds-container`);
							container.off("scroll");
							for (let i = elements[playoff].length - 1; i > 0; i--) {
								for (let match in elements[playoff][i]) {
									for (let j = 0; j < elements[playoff][i][match].length; j++) {
										const startElement = document.getElementById(
											elements[playoff][i][match][j],
										);
										const endElement = document.getElementById(match);

										const line = new LeaderLine(startElement, endElement, {
											color: "#000000",
											size: 2,
											endPlug: "behind",
											endPlugSize: 2,
											path: "grid",
											endSocket: "left",
											startSocket: "right",
										});

										container.on("scroll", () => {
											line.position();
										});
									}
								}
							}

							let lines = jQuery(".leader-line");
							const containerRect = container[0].getBoundingClientRect();

							// filter lines not belonging to current container
							lines = lines.filter((line) => {
								const lineEl = lines[line];
								const lineRect = lineEl.getBoundingClientRect();

								const isOutside =
									lineRect.top < containerRect.top ||
									lineRect.bottom > containerRect.bottom;

								return !isOutside;
							});

							lines.each(function (line) {
								lines[line].style.removeProperty("display");
								const lineEl = lines[line];
								const lineRect = lineEl.getBoundingClientRect();

								const isOutside =
									lineRect.right > containerRect.right ||
									lineRect.left < containerRect.left;

								if (isOutside) {
									lineEl.style.setProperty("display", "none");
								} else {
									lineEl.style.removeProperty("display");
								}
							});

							jQuery(`#playoff_${playoff} .rounds-container`).on(
								"scroll",
								function () {
									lines.each(function (index) {
										lines[index].style.removeProperty("display");
										const lineEl = lines[index];
										const lineRect = lineEl.getBoundingClientRect();

										const isOutside =
											lineRect.right > containerRect.right ||
											lineRect.left < containerRect.left;

										if (isOutside) {
											lineEl.style.setProperty("display", "none");
										} else {
											lineEl.style.removeProperty("display");
										}
									});
								},
							);
						}
					}
				}
			},
			error: function (xhr, status, error) {
				console.error("Error:", error);
			},
		});
	});
});
