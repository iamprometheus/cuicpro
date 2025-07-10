jQuery(document).on("click", "#add-official-button", function (e) {
	e.preventDefault();

	const officialName = jQuery("#official-name").val();
	let officialSchedule = jQuery("#official-schedule").val();
	const officialMode = jQuery("#official-mode").val();
	const officialTeamId = jQuery("#official-team-id").val();
	const officialCity = jQuery("#official-city").val();
	const officialState = jQuery("#official-state").val();
	const officialCountry = jQuery("#official-country").val();
	const tournaments = jQuery(".tournament-item[selected]");
	if (tournaments.length === 0) {
		jQuery("#official-result-table")
			.removeClass("success")
			.addClass("error")
			.html(
				"No hay torneos disponibles, agrega un torneo para agregar arbitros.",
			);
		return;
	}

	const tournamentID = tournaments[0].id.replace("tournament-", "");

	const officialDays = officialSchedule.replaceAll(" ", "").split(",");

	const officialHours = {};
	for (let i = 0; i < officialDays.length; i++) {
		const rawDay = officialDays[i];
		officialHours[rawDay] = [];
		const day = rawDay.replaceAll("/", "-");

		if (jQuery(`#official-day-${day}`).hasClass("hidden")) {
			continue;
		}
		const hoursContainer = jQuery(
			`#hours-selector-${day} .hours-selector-item`,
		);
		for (let j = 0; j < hoursContainer.length; j++) {
			if (hoursContainer[j].children[0].checked) {
				officialHours[rawDay].push(hoursContainer[j].children[0].value);
			}
		}
	}

	let hasHours = false;
	for (let day in officialHours) {
		if (officialHours[day].length !== 0) {
			hasHours = true;
		} else {
			officialDays.splice(officialDays.indexOf(day), 1);
		}
	}

	officialSchedule = officialDays.join(",");

	if (officialName === "") {
		jQuery("#official-result-table")
			.removeClass("success")
			.addClass("error")
			.html("Agregar un nombre al arbitro");
		return;
	}

	if (!hasHours) {
		jQuery("#official-result-table")
			.removeClass("success")
			.addClass("error")
			.html("Agregar al menos una hora al arbitro");
		return;
	}

	if (officialSchedule === "") {
		jQuery("#official-result-table")
			.removeClass("success")
			.addClass("error")
			.html("Agregar al menos un dia al arbitro");
		return;
	}

	if (officialCity === "" || officialState === "" || officialCountry === "") {
		jQuery("#official-result-table")
			.removeClass("success")
			.addClass("error")
			.html("Agregar la ciudad, estado y pais del arbitro");
		return;
	}

	jQuery.ajax({
		type: "POST",
		url: cuicpro.ajax_url,
		data: {
			action: "add_official",
			official_name: officialName,
			official_hours: officialHours,
			official_schedule: officialSchedule,
			official_mode: officialMode,
			official_team_id: officialTeamId,
			official_city: officialCity,
			official_state: officialState,
			official_country: officialCountry,
			tournament_id: tournamentID,
		},
		success: function (response) {
			if (response.success) {
				// add official to the table
				jQuery("#officials-data").append(response.data.html);

				clearOfficialInputs(response.data.tournament_days);

				jQuery("#official-result-table")
					.removeClass("error")
					.addClass("success")
					.html(response.data.message);
			} else {
				jQuery("#official-result-table")
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

jQuery(document).on("click", "#delete-official-button", function () {
	const officialID = jQuery(this).data("official-id");

	const confirmationBoxText =
		"¿Estas seguro de eliminar el arbitro? Esta accion podria ser irreversible.";

	const onResponse = function (response) {
		if (response.success) {
			jQuery(`#official-${officialID}`).remove();

			jQuery("#official-result-table")
				.removeClass("error")
				.addClass("success")
				.html(response.data.message);
		} else {
			jQuery("#official-result-table")
				.removeClass("success")
				.addClass("error")
				.html(response.data.message);
		}
	};

	confirmateActionBox(
		this,
		confirmationBoxText,
		"delete_official",
		{ official_id: officialID },
		onResponse,
	);
});

jQuery(document).on("click", "#active-official-button", function () {
	const officialID = jQuery(this).data("official-id");
	const officialIsActive = jQuery(this).is(":checked") ? 1 : 0;

	jQuery.ajax({
		type: "POST",
		url: cuicpro.ajax_url,
		data: {
			action: "update_official_active",
			official_id: officialID,
			official_is_active: officialIsActive,
		},
		success: function (response) {
			if (response.success) {
				jQuery(`#active-official-button[data-official-id=${officialID}]`).prop(
					"checked",
					officialIsActive,
				);

				jQuery("#official-result-table")
					.removeClass("error")
					.addClass("success")
					.html(response.data.message);
			} else {
				jQuery("#official-result-table")
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

jQuery(document).on("click", "#certified-official-button", function () {
	const officialID = jQuery(this).data("official-id");
	const officialIsCertified = jQuery(this).is(":checked") ? 1 : 0;

	jQuery.ajax({
		type: "POST",
		url: cuicpro.ajax_url,
		data: {
			action: "update_official_certified",
			official_id: officialID,
			official_is_certified: officialIsCertified,
		},
		success: function (response) {
			if (response.success) {
				jQuery(
					`#certified-official-button[data-official-id=${officialID}]`,
				).prop("checked", officialIsCertified);

				jQuery("#official-result-table")
					.removeClass("error")
					.addClass("success")
					.html(response.data.message);
			} else {
				jQuery("#official-result-table")
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

jQuery(document).on("click", "#edit-official-button", function () {
	const officialID = jQuery(this).data("official-id");

	jQuery.ajax({
		type: "POST",
		url: cuicpro.ajax_url,
		data: {
			action: "edit_official",
			official_id: officialID,
		},
		success: function (response) {
			if (response.success) {
				const official = response.data.official;
				const officialName = official.official_name;
				const officialMode = official.official_mode;
				const officialTeamId = !official.official_team_id
					? 0
					: official.official_team_id;
				const officialCity = official.official_city;
				const officialState = official.official_state;
				const officialCountry = official.official_country;

				jQuery("#official-name").val(officialName);
				jQuery("#official-schedule").multiDatesPicker("resetDates");

				jQuery("#official-mode").val(officialMode);
				jQuery("#official-team-id").val(officialTeamId);
				jQuery("#official-city").val(officialCity);
				jQuery("#official-state").val(officialState);
				jQuery("#official-country").val(officialCountry);

				// hide all the elements of the official hours
				jQuery(".hours-selector-container").addClass("hidden");
				jQuery(".hours-selector").addClass("hidden");

				for (let i = 0; i < response.data.hours_data.length; i++) {
					const official_data = response.data.hours_data[i];
					jQuery("#official-schedule").multiDatesPicker(
						"addDates",
						official_data.official_day,
					);
					const day = official_data.official_day.replaceAll("/", "-");
					const hours = official_data.official_hours.split(",");
					const hours_selector = jQuery(`#hours-selector-${day}`);

					// reset hours
					hours_selector.find("input").prop("checked", false);

					// show day
					jQuery(`#official-day-${day}`).removeClass("hidden");

					// check hours
					for (let j = 0; j < hours.length; j++) {
						hours_selector
							.find(`input[value="${hours[j]}"]`)
							.prop("checked", true);
					}
				}

				jQuery("#add-official-button").addClass("hidden");
				jQuery("#update-official-button").removeClass("hidden");
				jQuery("#cancel-official-button").removeClass("hidden");

				jQuery("#update-official-button").data("official-id", officialID);

				jQuery("#official-result-table")
					.removeClass("error")
					.addClass("success")
					.html(response.data.message);
			} else {
				jQuery("#official-result-table")
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

jQuery(document).on("click", "#cancel-official-button", function (e) {
	e.preventDefault();

	clearOfficialInputs("");

	jQuery("#add-official-button").removeClass("hidden");
	jQuery("#update-official-button").addClass("hidden");
	jQuery("#cancel-official-button").addClass("hidden");

	jQuery("#official-result-table")
		.removeClass("error")
		.addClass("success")
		.html("Edicion cancelada.");
});

jQuery(document).on("click", "#update-official-button", function (e) {
	e.preventDefault();

	const officialID = jQuery(this).data("official-id");
	const officialName = jQuery("#official-name").val();
	let officialSchedule = jQuery("#official-schedule").val();
	const officialMode = jQuery("#official-mode").val();
	const officialTeamId = jQuery("#official-team-id").val();
	const officialCity = jQuery("#official-city").val();
	const officialState = jQuery("#official-state").val();
	const officialCountry = jQuery("#official-country").val();

	const officialDays = officialSchedule.replaceAll(" ", "").split(",");

	const officialHours = {};
	for (let i = 0; i < officialDays.length; i++) {
		const rawDay = officialDays[i];
		officialHours[rawDay] = [];
		const day = rawDay.replaceAll("/", "-");

		if (jQuery(`#official-day-${day}`).hasClass("hidden")) {
			continue;
		}
		const hoursContainer = jQuery(
			`#hours-selector-${day} .hours-selector-item`,
		);
		for (let j = 0; j < hoursContainer.length; j++) {
			if (hoursContainer[j].children[0].checked) {
				officialHours[rawDay].push(hoursContainer[j].children[0].value);
			}
		}
	}

	let hasHours = false;
	for (let day in officialHours) {
		if (officialHours[day].length !== 0) {
			hasHours = true;
		} else {
			officialDays.splice(officialDays.indexOf(day), 1);
		}
	}

	officialSchedule = officialDays.join(",");

	if (officialName === "") {
		jQuery("#official-result-table")
			.removeClass("success")
			.addClass("error")
			.html("Agregar un nombre al arbitro");
		return;
	}

	if (!hasHours) {
		jQuery("#official-result-table")
			.removeClass("success")
			.addClass("error")
			.html("Agregar las horas al arbitro");
		return;
	}

	if (officialSchedule === "") {
		jQuery("#official-result-table")
			.removeClass("success")
			.addClass("error")
			.html("Agregar al menos un dia al arbitro");
		return;
	}

	if (officialCity === "" || officialState === "" || officialCountry === "") {
		jQuery("#official-result-table")
			.removeClass("success")
			.addClass("error")
			.html("Agregar la ciudad, estado y pais del arbitro");
		return;
	}

	jQuery.ajax({
		type: "POST",
		url: cuicpro.ajax_url,
		data: {
			action: "update_official",
			official_id: officialID,
			official_name: officialName,
			official_hours: officialHours,
			official_schedule: officialSchedule,
			official_mode: officialMode,
			official_team_id: officialTeamId,
			official_city: officialCity,
			official_state: officialState,
			official_country: officialCountry,
		},
		success: function (response) {
			if (response.success) {
				jQuery(`#official-${officialID}`).html(response.data.html);
				clearOfficialInputs(response.data.tournament_days);

				jQuery("#add-official-button").removeClass("hidden");
				jQuery("#update-official-button").addClass("hidden");
				jQuery("#cancel-official-button").addClass("hidden");

				jQuery("#official-result-table")
					.removeClass("error")
					.addClass("success")
					.html(response.data.message);
			} else {
				jQuery("#official-result-table")
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

jQuery(document).on("click", ".hours-selector-container", function () {
	const id = jQuery(this).attr("id").replace("official-day-", "");
	jQuery("#hours-selector-" + id).toggleClass("hidden");
});

jQuery(document).on("click", ".hours-viewer-container", function () {
	const id = jQuery(this).children("span").text().replaceAll("/", "-");

	jQuery(this)
		.next("#hours-viewer-" + id)
		.toggleClass("hidden");
});

function clearOfficialInputs(tournamentDays) {
	jQuery("#official-name").val("");
	// reset hours
	const hours_selector = jQuery(`[class*="hours-selector-item"]`);
	for (let i = 0; i < hours_selector.length; i++) {
		hours_selector[i].children[0].checked = false;
	}

	// get active tournament days
	const active_tournament = jQuery(`.tournament-item[selected]`)[0].id;

	jQuery(".hours-selector-container").removeClass("hidden");

	const tournamentsContainer = jQuery(".tournaments-container");
	const active_tournament_data = tournamentsContainer.find(
		`#${active_tournament}`,
	);
	const tournament_days = active_tournament_data
		.find("#tournament-selected-days")
		.val();
	jQuery("#official-schedule").val(tournament_days);
	jQuery("#official-mode").val("1");
	jQuery("#official-team-id").val("0");
	jQuery("#official-city").val("");
	jQuery("#official-state").val("");
	jQuery("#official-country").val("");
}
