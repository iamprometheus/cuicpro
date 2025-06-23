jQuery(document).ready(function ($) {
	jQuery(document).on("click", ".tournament-item", function () {
		const tournamentID = $(this).attr("id").split("-")[1];
		const selected = $(this);
		$.ajax({
			url: cuicpro.ajax_url,
			type: "POST",
			data: {
				action: "fetch_tournament_brackets_display",
				tournament_id: tournamentID,
			},
			success: function (response) {
				if (response.success) {
					$(".tournament-item").attr("selected", false);
					selected.attr("selected", true);
					$(".brackets-list").html(response.data.html);
				}
			},
			error: function (xhr, status, error) {
				console.error("Error:", error);
			},
		});
	});

	jQuery(document).on("click", ".brackets-list-item", function () {
		const bracketID = $(this).attr("data-bracket-id");
		$.ajax({
			url: cuicpro.ajax_url,
			type: "POST",
			data: {
				action: "fetch_brackets_diagram",
				bracket_id: bracketID,
			},
			success: function (response) {
				if (response.success) {
					$("#bracket-container").html(response.data.html);

					const elements = response.data.elements;
					$("#bracket-container").off("scroll");

					if (elements !== null) {
						for (let i = elements.length - 1; i > 0; i--) {
							for (let match in elements[i]) {
								for (let j = 0; j < elements[i][match].length; j++) {
									const line = new LeaderLine(
										document.getElementById(elements[i][match][j]),
										document.getElementById(match),
										{
											color: "#000000",
											size: 2,
											endPlug: "behind",
											endPlugSize: 2,
											path: "grid",
											endSocket: "left",
											startSocket: "right",
										},
									);
									$("#bracket-container").on("scroll", function () {
										line.position();
									});
								}
							}
						}

						const container = jQuery("#bracket-container");
						const lines = jQuery(".leader-line");
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

						jQuery("#bracket-container").on("scroll", function () {
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
					}
				}
			},
			error: function (xhr, status, error) {
				console.error("Error:", error);
			},
		});
	});
});
