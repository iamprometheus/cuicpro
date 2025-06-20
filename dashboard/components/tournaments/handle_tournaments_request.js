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
		jQuery("#tournament-result-table")
			.removeClass("success")
			.addClass("error")
			.html("Agregar Nombre");
		return;
	}

	if (tournamentDays.length === 0) {
		jQuery("#tournament-result-table")
			.removeClass("success")
			.addClass("error")
			.html("Agregar Dias");
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
			if (response.success) {
				if (
					jQuery(".tournament-item-header")[0].children[0].innerHTML !==
					"Torneos Activos:"
				) {
					const tournamentsHeader = jQuery(".tournament-item-header");
					for (let i = 0; i < tournamentsHeader.length; i++) {
						tournamentsHeader[i].children[0].innerHTML = "Torneos Activos:";
					}
				}

				jQuery(".tournaments-container").append(response.data.html);
				jQuery("[id='tournaments-selector']").append(
					response.data.tournament_entry,
				);

				jQuery("#tournament-name").val("");
				jQuery("#tournament-days").multiDatesPicker("resetDates");
				jQuery("#hours-container").html("");
				jQuery("#slider-fields-5v5").slider("values", [1, 8]);

				jQuery("#slider-fields-7v7").slider("values", [9, 12]);

				jQuery("#tournament-result-table")
					.removeClass("error")
					.addClass("success")
					.html(response.data.message);
			}
		},
		error: function (xhr, status, error) {
			console.error("Error:", error);
		},
	});
});

jQuery(document).on(
	"click",
	"#tournaments-selector .tournament-item",
	function () {
		const selectedTournament = jQuery(this);
		const tournamentID = selectedTournament[0].id.replace("tournament-", "");

		jQuery.ajax({
			type: "POST",
			url: cuicpro.ajax_url,
			data: {
				action: "switch_selected_tournament",
				tournament_id: tournamentID,
			},
			success: function (response) {
				if (response.success) {
					selectedTournament
						.attr("selected", true)
						.siblings()
						.attr("selected", false);

					const selectors = jQuery(`[id='tournament-${tournamentID}']`);
					selectors.attr("selected", true);
					selectors.siblings().attr("selected", false);

					// clean lines
					jQuery("#brackets-data").off("scroll");
					jQuery(".leader-line").remove();
					jQuery("#leader-line-defs").remove();

					// assign divisions data for selected tournament
					jQuery("#divisions-data").html(response.data.divisions);

					// assign coaches data for selected tournament
					jQuery("#coaches-data").html(response.data.coaches);

					// assign brackets data for selected tournament
					jQuery("#brackets-dropdown").html(response.data.brackets);
					// clean brackets data
					jQuery("#brackets-data").html("");

					// assign officials data for selected tournament
					jQuery("#officials-data").html(response.data.officials);

					// assign teams data for selected tournament
					jQuery("#teams-data").html(response.data.teams);

					// assign hours data for for officials in selected tournament
					jQuery("#official-hours").html(response.data.official_hours);

					// assign matches data for selected tournament
					jQuery("#matches-data").html(response.data.matches);

					// fix the days of the tournament in the official schedule selector date

					jQuery("#official-schedule").multiDatesPicker("destroy");
					jQuery("#official-schedule").val(
						response.data.tournament_days.replaceAll(",", ", "),
					);

					const availableDays = response.data.tournament_days.split(",");

					const rawDate1 = availableDays[0].split("/").reverse();
					rawDate1[0] = "2025";
					rawDate1.join("-");
					const rawDate2 = availableDays[availableDays.length - 1]
						.split("/")
						.reverse();
					rawDate2[0] = "2025";
					rawDate2.join("-");

					const date1 = new Date(rawDate1);
					const date2 = new Date(rawDate2);

					jQuery("#official-schedule").multiDatesPicker({
						minDate: date1,
						maxDate: date2,
						dateFormat: "d/m/y",
						onSelect: function (dateText, inst) {
							const searchID = `#official-day-${dateText}`.replaceAll("/", "-");
							const searchID2 = `#hours-selector-${dateText}`.replaceAll(
								"/",
								"-",
							);
							const daySelector = jQuery(searchID);
							const hoursSelector = jQuery(searchID2);
							if (daySelector.hasClass("hidden")) {
								daySelector.removeClass("hidden");
							} else {
								daySelector.addClass("hidden");
								hoursSelector.addClass("hidden");
							}
						},
					});

					jQuery("#switch-selected-tournament-button").attr("disabled", true);
					jQuery("#tournament-result-table")
						.removeClass("error")
						.addClass("success")
						.html(response.data.message);
				} else {
					jQuery("#tournament-result-table")
						.removeClass("success")
						.addClass("error")
						.html(response.data.message);
				}
			},
			error: function (xhr, status, error) {
				console.error("Error:", error);
			},
		});
	},
);

jQuery(document).on("click", "#create-brackets-button", function (e) {
	e.preventDefault();
	const tournamentID = jQuery(this).data("tournament-id");
	const buttonsContainer = jQuery(this).parent();

	jQuery.ajax({
		type: "POST",
		url: cuicpro.ajax_url,
		data: {
			action: "create_brackets",
			tournament_id: tournamentID,
		},
		success: function (response) {
			if (response.success) {
				toggleButtonsWhenSelectedTypeOfTournament(buttonsContainer);

				jQuery("#brackets-dropdown").append(response.data.brackets_dropdown);

				jQuery(`#tournament-result-table-${tournamentID}`)
					.removeClass("error")
					.addClass("success")
					.html(response.data.message);
			} else {
				jQuery(`#tournament-result-table-${tournamentID}`)
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

jQuery(document).on("click", "#create-round-robin-button", function (e) {
	e.preventDefault();
	const tournamentID = jQuery(this).data("tournament-id");
	const buttonsContainer = jQuery(this).parent();

	jQuery.ajax({
		type: "POST",
		url: cuicpro.ajax_url,
		data: {
			action: "create_round_robin",
			tournament_id: tournamentID,
		},
		success: function (response) {
			if (response.success) {
				console.log(response.data);
				toggleButtonsWhenSelectedTypeOfTournament(buttonsContainer);

				jQuery(`#tournament-result-table-${tournamentID}`)
					.removeClass("error")
					.addClass("success")
					.html(response.data.message);
			} else {
				jQuery(`#tournament-result-table-${tournamentID}`)
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

jQuery(document).on("click", "#assign-officials-button", function (e) {
	e.preventDefault();
	const tournamentID = jQuery(this).data("tournament-id");
	const buttonsContainer = jQuery(this).parent();

	jQuery.ajax({
		type: "POST",
		url: cuicpro.ajax_url,
		data: {
			action: "assign_officials",
			tournament_id: tournamentID,
		},
		success: function (response) {
			console.log(response.data);
			if (response.success) {
				buttonsContainer
					.find(`#assign-officials-button`)
					.attr("disabled", true);
				buttonsContainer
					.find(`#unassign-officials-button`)
					.attr("disabled", false);
				jQuery(`#tournament-result-table-${tournamentID}`)
					.removeClass("error")
					.addClass("success")
					.html(response.data.message);
			} else {
				jQuery(`#tournament-result-table-${tournamentID}`)
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

jQuery(document).on("click", "#unassign-officials-button", function (e) {
	e.preventDefault();
	const tournamentID = jQuery(this).data("tournament-id");
	const buttonsContainer = jQuery(this).parent();

	const confirmationBoxText =
		"¿Estas seguro de desasignar los arbitros? Esta acción es irreversible. Aunque la asignación de los arbitros se perderá, podrá reasignarlos en cualquier momento.";

	const onResponse = function (response) {
		if (response.success) {
			buttonsContainer
				.find(`#unassign-officials-button`)
				.attr("disabled", true);
			buttonsContainer.find(`#assign-officials-button`).attr("disabled", false);

			jQuery(`#tournament-result-table-${tournamentID}`)
				.removeClass("error")
				.addClass("success")
				.html(response.data.message);
		} else {
			jQuery(`#tournament-result-table-${tournamentID}`)
				.removeClass("success")
				.addClass("error")
				.html(response.data.message);
		}
	};

	confirmateActionBox(
		this,
		confirmationBoxText,
		"unassign_officials",
		{ tournament_id: tournamentID },
		onResponse,
	);
});

jQuery(document).on("click", "#delete-matches-button", function (e) {
	e.preventDefault();
	const tournamentID = jQuery(this).data("tournament-id");
	const buttonsContainer = jQuery(this).parent();

	const confirmationBoxText =
		"¿Estas seguro de eliminar los partidos? Esta accion es irreversible.";

	const onResponse = function (response) {
		if (response.success) {
			toggleButtonsWhenDeletingMatchesOfTournament(buttonsContainer);
			resetBracketsData();

			jQuery(`#tournament-result-table-${tournamentID}`)
				.removeClass("error")
				.addClass("success")
				.html(response.data.message);
		} else {
			jQuery(`#tournament-result-table-${tournamentID}`)
				.removeClass("success")
				.addClass("error")
				.html(response.data.message);
		}
	};

	confirmateActionBox(
		this,
		confirmationBoxText,
		"delete_brackets",
		{ tournament_id: tournamentID },
		onResponse,
	);
});

jQuery(document).on("click", "#delete-tournament-button", function () {
	const tournamentID = jQuery(this).data("tournament-id");

	const confirmationBoxText =
		"¿Estas seguro de eliminar el torneo? Esta accion es irreversible.";

	const onResponse = function (response) {
		if (response.success) {
			jQuery(`[id='tournament-${tournamentID}']`).remove();

			if (jQuery(".tournaments-selector").length === 0) {
				const tournamentsHeader = jQuery(".tournament-item-header");
				for (let i = 0; i < tournamentsHeader.length; i++) {
					tournamentsHeader[i].children[0].innerHTML = "No hay torneos activos";
				}
			}

			jQuery("#tournament-result-table")
				.removeClass("error")
				.addClass("success")
				.html(response.data.message);
		} else {
			jQuery("#tournament-result-table")
				.removeClass("success")
				.addClass("error")
				.html(response.data.message);
		}
	};

	confirmateActionBox(
		this,
		confirmationBoxText,
		"delete_tournament",
		{ tournament_id: tournamentID },
		onResponse,
	);
});

// toggle buttons when selected type of tournament
function toggleButtonsWhenSelectedTypeOfTournament(element) {
	element.find("#create-round-robin-button").attr("disabled", true);
	element.find("#create-brackets-button").attr("disabled", true);
	element.find("#assign-officials-button").attr("disabled", false);
	element.find("#delete-matches-button").attr("disabled", false);
}

// toggle buttons deleting matches of tournament
function toggleButtonsWhenDeletingMatchesOfTournament(element) {
	element.find("#create-round-robin-button").attr("disabled", false);
	element.find("#create-brackets-button").attr("disabled", false);
	element.find("#assign-officials-button").attr("disabled", true);
	element.find("#unassign-officials-button").attr("disabled", true);
	element.find("#delete-matches-button").attr("disabled", true);
}

function resetBracketsData() {
	jQuery("#brackets-data").html("");
	jQuery("#brackets-dropdown").html("");
	jQuery("#brackets-dropdown").append(
		"<option value=''>Seleccionar Bracket</option>",
	);

	jQuery(".leader-line").remove();
}

function confirmateActionBox(element, text, action, parameters, onResponse) {
	jQuery("#warning-box").remove();
	jQuery("#yes-button").off("longclick");
	jQuery("#no-button").off("click");

	const confirmationButtons = `<div><button id='no-button'>No</button><button id='yes-button'>Si</button></div>`;
	const usageHint = `<span>Nota: Para aceptar la accion presiona el boton "Si" por mas de un segundo y luego suelta el boton.</span>`;
	const confirmationBox = `<div id='warning-box'><span>${text}</span>${usageHint}${confirmationButtons}</div>`;
	jQuery(element).after(confirmationBox);

	jQuery("#yes-button").on("longclick", function () {
		jQuery("#warning-box").remove();
		jQuery.ajax({
			type: "POST",
			url: cuicpro.ajax_url,
			data: {
				action: action,
				...parameters,
			},
			success: function (response) {
				onResponse(response);
			},
			error: function (xhr, status, error) {
				console.error("Error:", error);
			},
		});
	});

	jQuery("#no-button").on("click", function () {
		jQuery("#warning-box").remove();
	});

	let start, end, diff;
	const clickTime = 500;
	const longClick = new CustomEvent("longclick");

	const btn = document.querySelector("#yes-button");

	btn.onmousedown = function () {
		start = Date.now();
		btn.onmouseup = function () {
			end = Date.now();
			diff = end - start + 1;
			if (diff > clickTime) {
				btn.dispatchEvent(longClick);
			}
		};
	};
}
