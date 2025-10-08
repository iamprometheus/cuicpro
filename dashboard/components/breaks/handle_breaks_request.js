jQuery(document).on("click", "#add-break-button", function (e) {
	e.preventDefault();

	const breakDays = jQuery("#break-preferred-days").val();
	const breakHour = jQuery(`#slider-break-time`).slider("option", "values")[0];
	const breakReason = jQuery("#break-reason").val();

	const tournaments = jQuery(".tournament-item[selected]");

	if (tournaments.length === 0) {
		jQuery("#break-result-table")
			.removeClass("success")
			.addClass("error")
			.html(
				"No hay torneos disponibles, agrega un torneo para agregar pausas.",
			);
		return;
	}

	const tournamentID = tournaments[0].id.replace("tournament-", "");

	if (breakDays === "") {
		jQuery("#break-result-table")
			.removeClass("success")
			.addClass("error")
			.html("Agregar un nombre a la pausa");
		return;
	}

	if (breakHour === "") {
		jQuery("#break-result-table")
			.removeClass("success")
			.addClass("error")
			.html("Agregar una hora");
		return;
	}

	if (breakReason === "") {
		jQuery("#break-result-table")
			.removeClass("success")
			.addClass("error")
			.html("Agregar una razon de la pausa");
		return;
	}

	jQuery.ajax({
		type: "POST",
		url: cuicpro.ajax_url,
		data: {
			action: "add_break",
			break_days: breakDays,
			break_hour: breakHour,
			break_reason: breakReason,
			tournament_id: tournamentID,
		},
		success: function (response) {
			if (response.success) {
				// add break to the table
				const breaksData = document.querySelector("#breaks-data");
				breaksData.classList.remove("cell-hidden");
				breaksData.insertAdjacentHTML("beforeend", response.data.html);

				// clear inputs from table
				clearBreakInputs();

				jQuery("#break-result-table")
					.removeClass("error")
					.addClass("success")
					.html(response.data.message);
			} else {
				jQuery("#break-result-table")
					.removeClass("success")
					.addClass("error")
					.html(response.data.message);
			}
		},
		error: function (xhr, status, error) {
			console.error("Error:", error);
		},
	});
});

jQuery(document).on("click", "#delete-break-button", function () {
	const breakID = jQuery(this).data("break-id");

	const confirmationBoxText =
		"¿Estas seguro de eliminar la pausa? Esta accion podria ser irreversible.";

	const onResponse = function (response) {
		if (response.success) {
			jQuery(`#break-${breakID}`).remove();

			jQuery("#break-result-table")
				.removeClass("error")
				.addClass("success")
				.html(response.data.message);
		} else {
			jQuery("#break-result-table")
				.removeClass("success")
				.addClass("error")
				.html(response.data.message);
		}
	};

	confirmateActionBox(
		this,
		confirmationBoxText,
		"delete_break",
		{ break_id: breakID },
		onResponse,
	);
});

jQuery(document).on("click", "#edit-break-button", function () {
	const breakID = jQuery(this).data("break-id");

	jQuery.ajax({
		type: "POST",
		url: cuicpro.ajax_url,
		data: {
			action: "edit_break",
			break_id: breakID,
		},
		success: function (response) {
			if (response.success) {
				const breakData = response.data.break;
				const breakDays = breakData.tournament_days;
				const breakTime = breakData.tournament_break_hour;
				const breakReason = breakData.tournament_break_reason;

				jQuery("#break-preferred-days").multiDatesPicker("resetDates");

				const days = breakDays.split(",");
				for (let i = 0; i < days.length; i++) {
					jQuery("#break-preferred-days").multiDatesPicker("addDates", days[i]);
				}

				jQuery("#break-hour").val(breakTime.toString() + ":00");
				jQuery("#slider-break-time").slider("destroy");
				jQuery("#slider-break-time").slider({
					min: 7,
					max: 23,
					step: 1,
					values: [breakTime], // Initial values (12 PM)
					slide: function (event, ui) {
						const startHour = (ui.values[0] < 10 ? "0" : "") + ui.values[0];
						jQuery("#break-hour").val(startHour + ":00");
					},
				});

				jQuery("#break-reason").val(breakReason);

				jQuery("#add-break-button").addClass("hidden");
				jQuery("#update-break-button").removeClass("hidden");
				jQuery("#cancel-break-button").removeClass("hidden");

				jQuery("#update-break-button").data("break-id", breakID);

				jQuery("#break-result-table")
					.removeClass("error")
					.addClass("success")
					.html(response.data.message);
			} else {
				jQuery("#break-result-table")
					.removeClass("success")
					.addClass("error")
					.html(response.data.message);
			}
		},
		error: function (xhr, status, error) {
			console.error("Error:", error);
		},
	});
});

jQuery(document).on("click", "#cancel-break-button", function (e) {
	e.preventDefault();

	clearBreakInputs();

	jQuery("#add-break-button").removeClass("hidden");
	jQuery("#update-break-button").addClass("hidden");
	jQuery("#cancel-break-button").addClass("hidden");

	jQuery("#break-result-table")
		.removeClass("error")
		.addClass("success")
		.html("Edicion cancelada.");
});

jQuery(document).on("click", "#update-break-button", function (e) {
	e.preventDefault();

	const breakID = jQuery(this).data("break-id");
	const breakDays = jQuery("#break-preferred-days").val();
	const breakHour = jQuery(`#slider-break-time`).slider("option", "values")[0];
	const breakReason = jQuery("#break-reason").val();

	const tournaments = jQuery(".tournament-item[selected]");

	if (tournaments.length === 0) {
		jQuery("#break-result-table")
			.removeClass("success")
			.addClass("error")
			.html(
				"No hay torneos disponibles, agrega un torneo para agregar pausas.",
			);
		return;
	}

	if (breakDays === "") {
		jQuery("#break-result-table")
			.removeClass("success")
			.addClass("error")
			.html("Agregar un nombre a la pausa");
		return;
	}

	if (breakHour === "") {
		jQuery("#break-result-table")
			.removeClass("success")
			.addClass("error")
			.html("Agregar una hora");
		return;
	}

	if (breakReason === "") {
		jQuery("#break-result-table")
			.removeClass("success")
			.addClass("error")
			.html("Agregar una razon de la pausa");
		return;
	}

	jQuery.ajax({
		type: "POST",
		url: cuicpro.ajax_url,
		data: {
			action: "update_break",
			break_id: breakID,
			break_days: breakDays,
			break_hour: breakHour,
			break_reason: breakReason,
		},
		success: function (response) {
			if (response.success) {
				jQuery(`#break-${breakID}`).html(response.data.html);

				clearBreakInputs();

				jQuery("#update-break-button").attr("data-break-id", 0);

				jQuery("#add-break-button").removeClass("hidden");
				jQuery("#update-break-button").addClass("hidden");
				jQuery("#cancel-break-button").addClass("hidden");

				jQuery("#break-result-table")
					.removeClass("error")
					.addClass("success")
					.html(response.data.message);
			} else {
				jQuery("#break-result-table")
					.removeClass("success")
					.addClass("error")
					.html(response.data.message);
			}
		},
		error: function (xhr, status, error) {
			console.error("Error:", error);
		},
	});
});

function clearBreakInputs() {
	jQuery("#break-days").val("");
	jQuery("#break-reason").val("");
	jQuery("#break-hour").val("12:00");
	jQuery("#slider-break-time").slider("destroy");

	jQuery("#slider-break-time").slider({
		min: 7,
		max: 23,
		step: 1,
		values: [12], // Initial values (12 PM)
		slide: function (event, ui) {
			const startHour = (ui.values[0] < 10 ? "0" : "") + ui.values[0];
			jQuery("#break-hour").val(startHour + ":00");
		},
	});

	const active_tournament = jQuery(`.tournament-item[selected]`)[0].id;

	const tournamentsContainer = jQuery(".tournaments-container");
	const active_tournament_data = tournamentsContainer.find(
		`#${active_tournament}`,
	);
	const tournament_days = active_tournament_data
		.find("#tournament-selected-days")
		.val();
	jQuery("#break-preferred-days").val(tournament_days);
}
