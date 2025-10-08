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
				action: "switch_selected_tournament_register",
				tournament_id: tournamentID,
			},
			success: function (response) {
				if (response.success) {
					selectedTournament
						.attr("selected", true)
						.siblings()
						.attr("selected", false);

					jQuery("#division-selector-register").html(response.data.divisions);
					jQuery("#register-container").html(response.data.pending_table);
					jQuery("#registered-container").html(response.data.registered_table);
				}
			},
			error: function (xhr, status, error) {
				console.error("Error:", error);
			},
		});
	},
);

jQuery(document).on(
	"click",
	"#divisions-selector-register .tournament-item",
	function () {
		const selectedTournament = jQuery(
			"#tournaments-selector-schedule .tournament-item",
		).filter("[selected]");
		const tournamentID = selectedTournament[0].id.replace("tournament-", "");
		const selectedDivision = jQuery(this);
		const divisionID = selectedDivision[0].id.replace("division-", "");

		jQuery.ajax({
			type: "POST",
			url: cuicpro.ajax_url,
			data: {
				action: "switch_selected_division_register",
				tournament_id: tournamentID,
				division_id: divisionID,
			},
			success: function (response) {
				if (response.success) {
					selectedDivision
						.attr("selected", true)
						.siblings()
						.attr("selected", false);

					jQuery("#register-container").html(response.data.pending_table);
					jQuery("#registered-container").html(response.data.registered_table);
				}
			},
			error: function (xhr, status, error) {
				console.error("Error:", error);
			},
		});
	},
);

jQuery(document).on("click", "#accept-team-button", function () {
	const recordID = jQuery(this).attr("data-record-id");
	const trParent = jQuery(this).parent().parent().parent();

	jQuery.ajax({
		type: "POST",
		url: cuicpro.ajax_url,
		data: {
			action: "accept_team_register",
			record_id: recordID,
		},
		success: function (response) {
			if (response.success) {
				trParent.remove();
				alert(response.data.message);
			} else {
				alert(response.data.message);
			}
		},
		error: function (xhr, status, error) {
			console.error("Error:", error);
		},
	});
});

jQuery(document).on("click", "#reject-team-button", function () {
	const recordID = jQuery(this).attr("data-record-id");
	const trParent = jQuery(this).parent().parent().parent();

	const confirmationBoxText =
		"¿Estas seguro de rechazar el equipo? Esta accion podria ser irreversible.";

	const onResponse = function (response) {
		if (response.success) {
			trParent.remove();
			alert(response.data.message);
		} else {
			alert(response.data.message);
		}
	};

	confirmateActionBox(
		this,
		confirmationBoxText,
		"reject_team_register",
		{ record_id: recordID },
		onResponse,
	);
});
