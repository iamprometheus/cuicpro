jQuery(document).ready(function ($) {
	jQuery(document).on("change", "#tournament-select-standings", function () {
		const tournamentID = $("#tournament-select-standings").val();
		$.ajax({
			url: cuicpro.ajax_url,
			type: "POST",
			data: {
				action: "fetch_tournament_divisions_standings",
				tournament_id: tournamentID,
			},
			success: function (response) {
				if (response.success) {
					$("#division-select-standings").html(response.data.divisions);
					$("#team-select-standings").html(response.data.teams);
				}
			},
			error: function (xhr, status, error) {
				console.error("Error:", error);
			},
		});
	});

	jQuery(document).on("change", "#division-select-standings", function () {
		const divisionID = $(this).val();
		const tournamentID = $("#tournament-select-standings").val();
		$.ajax({
			url: cuicpro.ajax_url,
			type: "POST",
			data: {
				action: "fetch_division_team_standings",
				division_id: divisionID,
				tournament_id: tournamentID,
			},
			success: function (response) {
				if (response.success) {
					$("#team-select-standings").html(response.data.teams);
				}
			},
			error: function (xhr, status, error) {
				console.error("Error:", error);
			},
		});
	});

	jQuery(document).on("change", "#team-select-standings", function () {
		const teamID = $(this).val();
		const tournamentID = $("#tournament-select-standings").val();
		$.ajax({
			url: cuicpro.ajax_url,
			type: "POST",
			data: {
				action: "fetch_team_standings",
				team_id: teamID,
				tournament_id: tournamentID,
			},
			success: function (response) {
				if (response.success) {
					$("#team-standings").html(response.data.standings);
				}
			},
			error: function (xhr, status, error) {
				console.error("Error:", error);
			},
		});
	});
});
