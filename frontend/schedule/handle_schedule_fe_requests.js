jQuery(document).ready(function ($) {
	jQuery(document).on("change", "#tournament-select", function () {
		const tournamentID = $("#tournament-select").val();
		$.ajax({
			url: cuicpro.ajax_url,
			type: "POST",
			data: {
				action: "fetch_tournament_divisions_fe",
				tournament_id: tournamentID,
			},
			success: function (response) {
				if (response.success) {
					$("#division-select").html(response.data.divisions);
					$("#team-select").html(response.data.teams);
					$("#team-schedules").html(response.data.matches);
				}
			},
			error: function (xhr, status, error) {
				console.error("Error:", error);
			},
		});
	});

	jQuery(document).on("change", "#division-select", function () {
		const divisionID = $(this).val();
		const tournamentID = $("#tournament-select").val();
		$.ajax({
			url: cuicpro.ajax_url,
			type: "POST",
			data: {
				action: "fetch_division_teams_fe",
				division_id: divisionID,
				tournament_id: tournamentID,
			},
			success: function (response) {
				if (response.success) {
					$("#team-select").html(response.data.teams);
					$("#team-schedules").html(response.data.matches);
				}
			},
			error: function (xhr, status, error) {
				console.error("Error:", error);
			},
		});
	});

	jQuery(document).on("change", "#team-select", function () {
		const teamID = $(this).val();
		const tournamentID = $("#tournament-select").val();
		$.ajax({
			url: cuicpro.ajax_url,
			type: "POST",
			data: {
				action: "fetch_team_schedule_fe",
				team_id: teamID,
				tournament_id: tournamentID,
			},
			success: function (response) {
				console.log(response);
				if (response.success) {
					$("#team-schedules").html(response.data.matches);
				}
			},
			error: function (xhr, status, error) {
				console.error("Error:", error);
			},
		});
	});
});
