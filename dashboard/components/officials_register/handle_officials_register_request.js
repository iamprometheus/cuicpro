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
				action: "switch_selected_tournament_register_officials",
				tournament_id: tournamentID,
			},
			success: function (response) {
				if (response.success) {
					selectedTournament
						.attr("selected", true)
						.siblings()
						.attr("selected", false);

					jQuery("#pending-officials-container").html(
						response.data.pending_table,
					);
					jQuery("#registered-officials-container").html(
						response.data.registered_table,
					);
				}
			},
			error: function (xhr, status, error) {
				console.error("Error:", error);
			},
		});
	},
);

jQuery(document).on("click", "#accept-official-button", function () {
	const recordID = jQuery(this).attr("data-record-id");
	const trParent = jQuery(this).parent().parent().parent();

	jQuery.ajax({
		type: "POST",
		url: cuicpro.ajax_url,
		data: {
			action: "accept_official_register",
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

jQuery(document).on("click", "#reject-official-button", function () {
	const recordID = jQuery(this).attr("data-record-id");
	const trParent = jQuery(this).parent().parent().parent();

	const confirmationBoxText =
		"¿Estas seguro de rechazar el arbitro? Esta accion podria ser irreversible.";

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
		"reject_official_register",
		{ record_id: recordID },
		onResponse,
	);
});
