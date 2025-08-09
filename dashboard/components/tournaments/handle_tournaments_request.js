jQuery(document).on("click", "#add-tournament-button", function (e) {
	e.preventDefault();

	const tournamentName = jQuery("#tournament-name").val();
	const tournamentAddress = jQuery("#tournament-address").val();
	const tournamentCity = jQuery("#tournament-city").val();
	const tournamentState = jQuery("#tournament-state").val();
	const tournamentCountry = jQuery("#tournament-country").val();
	const tournamentRules = jQuery("#tournament-rules").val();
	const tournamentCategories = jQuery("#tournament-categories").val();
	const tournamentDays =
		jQuery("#tournament-days").multiDatesPicker("getDates");

	const tournamentHours = [];
	jQuery("#hours-container")
		.children()
		.each(function (index) {
			const dayHours = jQuery(`#slider-hours-${index}`).slider(
				"option",
				"values",
			);
			tournamentHours.push(dayHours);
		});

	const tournamentFields5v5 = jQuery("#fields-5v5").val();
	const tournamentFields7v7 = jQuery("#fields-7v7").val();
	const tournamentOrganizer = jQuery("#organizer").val();

	if (tournamentName === "") {
		jQuery("#tournament-result-table")
			.removeClass("success")
			.addClass("error")
			.html("Agregar Nombre");
		return;
	}

	if (tournamentAddress === "") {
		jQuery("#tournament-result-table")
			.removeClass("success")
			.addClass("error")
			.html("Agregar Lugar");
		return;
	}

	if (
		tournamentCity === "" ||
		tournamentState === "" ||
		tournamentCountry === ""
	) {
		jQuery("#tournament-result-table")
			.removeClass("success")
			.addClass("error")
			.html("Agregar Ciudad, Estado y Pais");
		return;
	}

	if (tournamentRules === "") {
		jQuery("#tournament-result-table")
			.removeClass("success")
			.addClass("error")
			.html("Agregar Reglamento");
		return;
	}

	if (tournamentCategories === "") {
		jQuery("#tournament-result-table")
			.removeClass("success")
			.addClass("error")
			.html("Agregar Categorias");
		return;
	}

	if (tournamentDays.length === 0) {
		jQuery("#tournament-result-table")
			.removeClass("success")
			.addClass("error")
			.html("Agregar Dias");
		return;
	}

	if (tournamentFields5v5.length === 0 && tournamentFields7v7.length === 0) {
		jQuery("#tournament-result-table")
			.removeClass("success")
			.addClass("error")
			.html("Agregar almenos un campo.");
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
			tournament_fields_5v5: tournamentFields5v5,
			tournament_fields_7v7: tournamentFields7v7,
			tournament_organizer: tournamentOrganizer,
			tournament_address: tournamentAddress,
			tournament_city: tournamentCity,
			tournament_state: tournamentState,
			tournament_country: tournamentCountry,
			tournament_rules: tournamentRules,
			tournament_categories: tournamentCategories,
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

				jQuery("#tournaments-container").append(response.data.html);
				jQuery("[id='tournaments-selector']").append(
					response.data.tournament_entry,
				);

				jQuery("#tournament-name").val("");
				jQuery("#tournament-days").multiDatesPicker("resetDates");
				jQuery("#hours-container").html("");
				jQuery("#fields-5v5").val(1);
				jQuery("#fields-7v7").val(0);
				jQuery("#organizer").val("0");
				jQuery("#tournament-address").val("");
				jQuery("#tournament-city").val("");
				jQuery("#tournament-state").val("");
				jQuery("#tournament-country").val("");
				jQuery("#tournament-rules").val("");
				jQuery("#tournament-categories").val("");

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
});

jQuery(document).on("click", "#edit-tournament-button", function (e) {
	e.preventDefault();
	const tournamentID = jQuery(this).data("tournament-id");

	jQuery.ajax({
		type: "POST",
		url: cuicpro.ajax_url,
		data: {
			action: "edit_tournament",
			tournament_id: tournamentID,
		},
		success: function (response) {
			if (response.success) {
				jQuery("#tournament-name").val(
					response.data.tournament.tournament_name,
				);
				jQuery("#fields-5v5").val(
					response.data.tournament.tournament_fields_5v5,
				);
				jQuery("#fields-7v7").val(
					response.data.tournament.tournament_fields_7v7,
				);

				jQuery("#organizer").attr("disabled", true);

				jQuery("#tournament-days").multiDatesPicker("resetDates");
				jQuery("#hours-container").html("");
				jQuery("#tournament-address").val(
					response.data.tournament.tournament_address,
				);
				jQuery("#tournament-city").val(
					response.data.tournament.tournament_city,
				);
				jQuery("#tournament-state").val(
					response.data.tournament.tournament_state,
				);
				jQuery("#tournament-country").val(
					response.data.tournament.tournament_country,
				);
				jQuery("#tournament-rules").val(
					response.data.tournament.tournament_rules,
				);
				jQuery("#tournament-categories").val(
					response.data.tournament.tournament_categories,
				);

				const days = response.data.tournament.tournament_days.split(",");
				for (let i = 0; i < days.length; i++) {
					jQuery("#tournament-days").multiDatesPicker("addDates", days[i]);

					jQuery("#hours-container").append(
						`<div class='hours-slider'>
							<div class='hours-slider-header'>
								<label for='hours-range-${i}'>${days[i]}</label>
								<input type='text' id='hours-range-${i}' readonly style='border:0; color:black; font-weight:bold; width: 100%;'>
							</div>
							<div id='slider-hours-${i}' class='tournament-slider'></div>
						</div>`,
					);

					const hours = response.data.tournament_hours[i];
					jQuery(`#hours-range-${i}`).val(
						hours.tournament_hours_start +
							":00 - " +
							hours.tournament_hours_end +
							":00",
					);

					jQuery(`#slider-hours-${i}`).slider({
						range: true,
						min: 7,
						max: 23,
						step: 1,
						values: [hours.tournament_hours_start, hours.tournament_hours_end],
						slide: function (event, ui) {
							const startHour = (ui.values[0] < 10 ? "0" : "") + ui.values[0];
							const endHour = (ui.values[1] < 10 ? "0" : "") + ui.values[1];
							jQuery(`#hours-range-${i}`).val(
								startHour + ":00 - " + endHour + ":00",
							);
						},
					});
				}

				jQuery("#add-tournament-button").addClass("hidden");
				jQuery("#update-tournament-button").removeClass("hidden");
				jQuery("#cancel-tournament-button").removeClass("hidden");

				jQuery("#update-tournament-button").data("tournament-id", tournamentID);

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
});

jQuery(document).on("click", "#update-tournament-button", function (e) {
	e.preventDefault();
	const tournamentID = jQuery(this).data("tournament-id");
	const tournamentName = jQuery("#tournament-name").val();
	const tournamentAddress = jQuery("#tournament-address").val();
	const tournamentCity = jQuery("#tournament-city").val();
	const tournamentState = jQuery("#tournament-state").val();
	const tournamentCountry = jQuery("#tournament-country").val();
	const tournamentRules = jQuery("#tournament-rules").val();
	const tournamentCategories = jQuery("#tournament-categories").val();
	const tournamentDays =
		jQuery("#tournament-days").multiDatesPicker("getDates");

	const tournamentHours = [];
	jQuery("#hours-container")
		.children()
		.each(function (index) {
			const dayHours = jQuery(`#slider-hours-${index}`).slider(
				"option",
				"values",
			);
			tournamentHours.push(dayHours);
		});

	const tournamentFields5v5 = jQuery("#fields-5v5").val();
	const tournamentFields7v7 = jQuery("#fields-7v7").val();

	if (tournamentName === "") {
		jQuery("#tournament-result-table")
			.removeClass("success")
			.addClass("error")
			.html("Agregar Nombre");
		return;
	}

	if (tournamentAddress === "") {
		jQuery("#tournament-result-table")
			.removeClass("success")
			.addClass("error")
			.html("Agregar Lugar");
		return;
	}

	if (
		tournamentCity === "" ||
		tournamentState === "" ||
		tournamentCountry === ""
	) {
		jQuery("#tournament-result-table")
			.removeClass("success")
			.addClass("error")
			.html("Agregar Ciudad, Estado y Pais");
		return;
	}

	if (tournamentRules === "") {
		jQuery("#tournament-result-table")
			.removeClass("success")
			.addClass("error")
			.html("Agregar Reglamento");
		return;
	}

	if (tournamentCategories === "") {
		jQuery("#tournament-result-table")
			.removeClass("success")
			.addClass("error")
			.html("Agregar Categorias");
		return;
	}

	if (tournamentDays.length === 0) {
		jQuery("#tournament-result-table")
			.removeClass("success")
			.addClass("error")
			.html("Agregar Dias");
		return;
	}

	if (tournamentFields5v5.length === 0 && tournamentFields7v7.length === 0) {
		jQuery("#tournament-result-table")
			.removeClass("success")
			.addClass("error")
			.html("Agregar almenos un campo.");
		return;
	}

	jQuery.ajax({
		type: "POST",
		url: cuicpro.ajax_url,
		data: {
			action: "update_tournament",
			tournament_id: tournamentID,
			tournament_name: tournamentName,
			tournament_address: tournamentAddress,
			tournament_city: tournamentCity,
			tournament_state: tournamentState,
			tournament_country: tournamentCountry,
			tournament_rules: tournamentRules,
			tournament_categories: tournamentCategories,
			tournament_days: tournamentDays.join(","),
			tournament_hours: tournamentHours,
			tournament_fields_5v5: tournamentFields5v5,
			tournament_fields_7v7: tournamentFields7v7,
		},
		success: function (response) {
			if (response.success) {
				jQuery("#tournament-" + tournamentID).replaceWith(response.data.html);

				jQuery(`[id='tournament-${tournamentID}']`).each((index, item) => {
					if (item.classList.contains("tournament-item")) {
						item.children[0].innerHTML = tournamentName;
					}
				});

				jQuery("#tournament-name").val("");
				jQuery("#tournament-days").multiDatesPicker("resetDates");
				jQuery("#fields-5v5").val(1);
				jQuery("#fields-7v7").val(0);
				jQuery("#hours-container").html("");
				jQuery("#organizer").attr("disabled", false);
				jQuery("#tournament-address").val("");
				jQuery("#tournament-city").val("");
				jQuery("#tournament-state").val("");
				jQuery("#tournament-country").val("");
				jQuery("#tournament-rules").val("");
				jQuery("#tournament-categories").val("");

				jQuery("#add-tournament-button").removeClass("hidden");
				jQuery("#update-tournament-button").addClass("hidden");
				jQuery("#cancel-tournament-button").addClass("hidden");

				jQuery("#tournament-result-table")
					.removeClass("error")
					.addClass("success")
					.html("Torneo actualizado");
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
});

jQuery(document).on("click", "#cancel-tournament-button", function (e) {
	e.preventDefault();
	jQuery("#tournament-name").val("");
	jQuery("#tournament-days").multiDatesPicker("resetDates");
	jQuery("#fields-5v5").val("1");
	jQuery("#fields-7v7").val("0");
	jQuery("#hours-container").html("");
	jQuery("#organizer").attr("disabled", false);
	jQuery("#tournament-address").val("");
	jQuery("#tournament-city").val("");
	jQuery("#tournament-state").val("");
	jQuery("#tournament-country").val("");
	jQuery("#tournament-rules").val("");
	jQuery("#tournament-categories").val("");

	jQuery("#add-tournament-button").removeClass("hidden");
	jQuery("#update-tournament-button").addClass("hidden");
	jQuery("#cancel-tournament-button").addClass("hidden");

	jQuery("#tournament-result-table")
		.removeClass("error")
		.addClass("success")
		.html("Modificacion cancelada");
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
					jQuery("#teams-data-by-division").html(
						response.data.teams_by_division,
					);

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

					jQuery("#division-preferred-days").multiDatesPicker("destroy");
					jQuery("#division-preferred-days").val(
						response.data.tournament_days.replaceAll(",", ", "),
					);

					jQuery("#division-preferred-days").multiDatesPicker({
						minDate: date1,
						maxDate: date2,
						dateFormat: "d/m/y",
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

jQuery(document).on("click", "#create-general-tournament-button", function (e) {
	e.preventDefault();
	const tournamentID = jQuery(this).data("tournament-id");
	const buttonsContainer = jQuery(this).parent();

	jQuery.ajax({
		type: "POST",
		url: cuicpro.ajax_url,
		data: {
			action: "create_general_tournament",
			tournament_id: tournamentID,
		},
		success: function (response) {
			console.log(response.data);
			if (response.success) {
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

jQuery(document).on("click", "#finish-tournament-button", function (e) {
	e.preventDefault();
	const tournamentID = jQuery(this).data("tournament-id");

	const confirmationBoxText =
		"¿Estas seguro de finalizar el torneo? Esta acción hará que el torneo se archive.";

	const onResponse = function (response) {
		if (response.success) {
			jQuery(`#tournament-result-table`)
				.removeClass("error")
				.addClass("success")
				.html(response.data.message);
			jQuery(`#tournament-${tournamentID}`).remove();
		} else {
			jQuery(`#tournament-result-table`)
				.removeClass("success")
				.addClass("error")
				.html(response.data.message);
		}
	};

	confirmateActionBox(
		this,
		confirmationBoxText,
		"end_tournament",
		{ tournament_id: tournamentID },
		onResponse,
	);
});

jQuery(document).on("click", "#archive-tournament-button", function (e) {
	e.preventDefault();
	const tournamentID = jQuery(this).data("tournament-id");

	const confirmationBoxText =
		"¿Estas seguro de archivar el torneo? Esta acción hará que el torneo se archive.";

	const onResponse = function (response) {
		if (response.success) {
			jQuery(`#tournament-result-table`)
				.removeClass("error")
				.addClass("success")
				.html(response.data.message);
			jQuery(`#tournament-${tournamentID}`).remove();
		} else {
			jQuery(`#tournament-result-table`)
				.removeClass("success")
				.addClass("error")
				.html(response.data.message);
		}
	};

	confirmateActionBox(
		this,
		confirmationBoxText,
		"archive_tournament",
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

jQuery(document).on("change", "#tournament-organizer-dropdown", function () {
	const tournamentID = jQuery(this).data("tournament-id");
	const selectedOrganizer = jQuery(this).val();

	jQuery.ajax({
		type: "POST",
		url: cuicpro.ajax_url,
		data: {
			action: "assign_organizer",
			tournament_id: tournamentID,
			organizer_id: selectedOrganizer,
		},
		success: function (response) {
			if (response.success) {
				jQuery("#tournament-result-table-" + tournamentID)
					.removeClass("error")
					.addClass("success")
					.html(response.data.message);
			} else {
				jQuery("#tournament-result-table-" + tournamentID)
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

jQuery(document).on("click", "#tournament-show-on-front", function () {
	const tournamentID = jQuery(this).data("tournament-id");
	const checked = jQuery(this).is(":checked") ? 1 : 0;
	jQuery.ajax({
		type: "POST",
		url: cuicpro.ajax_url,
		data: {
			action: "update_tournament_show_on_front",
			tournament_id: tournamentID,
			show_on_front: checked,
		},
		success: function (response) {
			if (response.success) {
				jQuery("#tournament-result-table-" + tournamentID)
					.removeClass("error")
					.addClass("success")
					.html(response.data.message);
			} else {
				jQuery("#tournament-result-table-" + tournamentID)
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

// toggle buttons when selected type of tournament
function toggleButtonsWhenSelectedTypeOfTournament(element) {
	element.find("#edit-tournament-button").attr("disabled", true);
	element.find("#create-general-tournament-button").attr("disabled", true);
	element.find("#create-round-robin-button").attr("disabled", true);
	element.find("#create-brackets-button").attr("disabled", true);
	element.find("#assign-officials-button").attr("disabled", false);
	element.find("#delete-matches-button").attr("disabled", false);
}

// toggle buttons deleting matches of tournament
function toggleButtonsWhenDeletingMatchesOfTournament(element) {
	element.find("#edit-tournament-button").attr("disabled", false);
	element.find("#create-general-tournament-button").attr("disabled", false);
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
