jQuery(document).on(
	"click",
	"#tournaments-selector-schedule .tournament-item",
	function () {
		const selectedTournament = jQuery(this);
		const tournamentID = selectedTournament[0].id.replace("tournament-", "");

		jQuery.ajax({
			type: "POST",
			url: cuicpro.ajax_url,
			data: {
				action: "switch_selected_tournament_schedule",
				tournament_id: tournamentID,
			},
			success: function (response) {
				if (response.success) {
					selectedTournament
						.attr("selected", true)
						.siblings()
						.attr("selected", false);

					jQuery("#schedule-container").html(response.data.schedule);
				}
			},
			error: function (xhr, status, error) {
				console.error("Error:", error);
			},
		});
	},
);
