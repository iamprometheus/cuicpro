jQuery(document).ready(function ($) {
	jQuery(document).on("click", "#division-name", function () {
		if ($(this).siblings(".teams-container").attr("hidden") == "hidden") {
			$(this).siblings(".teams-container").removeAttr("hidden");
		} else {
			$(this).siblings(".teams-container").attr("hidden", "hidden");
		}
	});

	jQuery(document).on("click", "#division-matches", function () {
		if ($(this).children(".matches-container").attr("hidden") == "hidden") {
			$(this).children(".matches-container").removeAttr("hidden");
		} else {
			$(this).children(".matches-container").attr("hidden", "hidden");
		}
	});

	jQuery(document).on("click", ".tournament-item", function () {
		const tournamentID = $(this).attr("id").split("-")[1];

		const selected = $(this);
		$.ajax({
			url: cuicpro.ajax_url,
			type: "POST",
			data: {
				action: "fetch_tournament_divisions_display",
				tournament_id: tournamentID,
			},
			success: function (response) {
				if (response.success) {
					$(".tournament-item").attr("selected", false);
					selected.attr("selected", true);
					$(".divisions-container").html(response.data.html);
				}
			},
			error: function (xhr, status, error) {
				console.error("Error:", error);
			},
		});
	});
});
