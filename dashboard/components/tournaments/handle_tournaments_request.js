jQuery(document).on("click", "#delete-tournament-button", function () {
	const tournamentID = jQuery(this).data("tournament-id");

	jQuery.ajax({
		type: "POST",
		url: cuicpro.ajax_url,
		data: {
			action: "delete_tournament",
			tournament_id: tournamentID,
		},
		success: function (response) {
			alert(response.data.message);
			if (response.success) {
				const element = document.querySelector(`#tournament-${tournamentID}`);
				element.remove();
			}
		},
		error: function (xhr, status, error) {
			console.error("Error:", error);
		},
	});
});

jQuery(document).on("click", "#add-tournament-button", function (e) {
	e.preventDefault();

	const tournamentName = jQuery("#tournament-name").val();
	const tournamentDays =
		jQuery("#tournament-days").multiDatesPicker("getDates");

	const tournamentHours = [];
	jQuery("#hours-container")
		.children()
		.each(function (index) {
			const tournamentHoursStart = jQuery(`#slider-hours-${index}`).slider(
				"values",
				0,
			);
			const tournamentHoursEnd = jQuery(`#slider-hours-${index}`).slider(
				"values",
				1,
			);
			tournamentHours.push([tournamentHoursStart, tournamentHoursEnd]);
		});

	const tournamentFields5v5Start = jQuery("#slider-fields-5v5").slider(
		"values",
		0,
	);
	const tournamentFields5v5End = jQuery("#slider-fields-5v5").slider(
		"values",
		1,
	);
	const tournamentFields7v7Start = jQuery("#slider-fields-7v7").slider(
		"values",
		0,
	);
	const tournamentFields7v7End = jQuery("#slider-fields-7v7").slider(
		"values",
		1,
	);

	if (tournamentName === "") {
		alert("Agregar Nombre");
		return;
	}

	if (tournamentDays.length === 0) {
		alert("Agregar Dias");
		return;
	}

	jQuery.ajax({
		type: "POST",
		url: cuicpro.ajax_url,
		data: {
			action: "add_tournament",
			tournament_name: tournamentName,
			tournament_days: tournamentDays.join(","),
			tournament_hours: tournamentHours,
			tournament_fields_5v5_start: tournamentFields5v5Start,
			tournament_fields_5v5_end: tournamentFields5v5End,
			tournament_fields_7v7_start: tournamentFields7v7Start,
			tournament_fields_7v7_end: tournamentFields7v7End,
		},
		success: function (response) {
			console.log(response);
			if (response.success) {
				const tournamentData = document.querySelector("#tournament-data");
				tournamentData.innerHTML = response.data.html;
			}
		},
		error: function (xhr, status, error) {
			console.error("Error:", error);
		},
	});
});

jQuery(document).on("click", "#start-tournament-button", function (e) {
	e.preventDefault();
	const tournamentID = jQuery(this).data("tournament-id");

	jQuery.ajax({
		type: "POST",
		url: cuicpro.ajax_url,
		data: {
			action: "start_tournament",
			tournament_id: tournamentID,
		},
		success: function (response) {
			console.log(response);
		},
		error: function (xhr, status, error) {
			console.error("Error:", error);
		},
	});
});
