jQuery(document).ready(function ($) {
	jQuery(document).on("click", ".tournament-item", function () {
		const tournamentID = $(this).attr("id").split("-")[1];

		const selected = $(this);
		$.ajax({
			url: cuicpro.ajax_url,
			type: "POST",
			data: {
				action: "fetch_tournament_teams_display",
				tournament_id: tournamentID,
			},
			success: function (response) {
				if (response.success) {
					$(".tournament-item").attr("selected", false);
					selected.attr("selected", true);
					$(".teams-grid-container").html(response.data.html);
				}
			},
			error: function (xhr, status, error) {
				console.error("Error:", error);
			},
		});
	});
});
