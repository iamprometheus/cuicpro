jQuery(document).ready(function ($) {
	$(document).on("change", "#divisions-dropdown-tv", function () {
		const divisionID = $(this).val();

		if (divisionID !== "0") {
			$.ajax({
				type: "POST",
				url: cuicpro.ajax_url,
				data: {
					action: "fetch_division_data",
					division_id: divisionID,
				},
				success: function (response) {
					if (response.success) {
						const newElement = document.querySelector("#division-data");
						newElement.innerHTML = response.data.html;

						$("#division-result-table")
							.removeClass("error")
							.addClass("success")
							.html(response.data.message);
					} else {
						$("#division-result-table")
							.removeClass("success")
							.addClass("error")
							.html(response.data.message);
					}
				},
				error: function (xhr, status, error) {
					console.error("Error:", error);
				},
			});
		} else {
			const element = document.querySelector("#division-data");
			element.innerHTML = "";
		}
	});

	$(document).on("change", "#coaches-dropdown-tv", function () {
		const coachID = $(this).val();

		if (coachID !== "0") {
			$.ajax({
				type: "POST",
				url: cuicpro.ajax_url,
				data: {
					action: "fetch_coach_data",
					coach_id: coachID,
				},
				success: function (response) {
					if (response.success) {
						$("#coach-data").html(response.data.html);
						$("#players-data").html(response.data.players);
						$("#team-players-dropdown").html(
							response.data.team_players_dropdown,
						);

						$("#team-result-table")
							.removeClass("error")
							.addClass("success")
							.html(response.data.message);
					} else {
						$("#team-result-table")
							.removeClass("success")
							.addClass("error")
							.html(response.data.message);
					}
				},
				error: function (xhr, status, error) {
					console.error("Error:", error);
				},
			});
		} else {
			$("#coach-data").html("");
			$("#players-data").html("");
			$("#team-players-dropdown").html(
				"<option value='0'>Seleccione un Equipo</option>",
			);
		}
	});

	$(document).on("click", "#add-team-button", function (e) {
		e.preventDefault();

		const divisionID = $("#team-division-table").val();
		const teamName = $("#team-name-table").val();
		const teamCategory = $("#team-category-table").val();
		const teamMode = $("#team-mode-table").val();
		const coachID = $("#coaches-dropdown-tv").val();
		const rawLogo = $("#team-logo-input");
		const logo = rawLogo[0].files[0];
		const tournaments = jQuery(".tournament-item[selected]");
		if (tournaments.length === 0) {
			jQuery("#team-result-table")
				.removeClass("success")
				.addClass("error")
				.html(
					"No hay torneos disponibles, agrega un torneo para agregar equipos.",
				);
			return;
		}

		const tournamentID = tournaments[0].id.replace("tournament-", "");

		if (coachID === "0") {
			$("#team-result-table").removeClass("success").addClass("error");
			$("#team-result-table").html(
				"Agregar todos los datos del equipo, faltantes: Entrenador",
			);
			return;
		}
		if (teamName === "" || logo === undefined) {
			$("#team-result-table").removeClass("success").addClass("error");
			$("#team-result-table").html(
				"Agregar todos los datos del equipo, faltantes: " +
					(teamName === "" ? "Nombre, " : "") +
					(logo === undefined ? "Logo" : ""),
			);
			return;
		}

		const form = new FormData();
		form.append("action", "add_team");
		form.append("division_id", divisionID);
		form.append("team_name", teamName);
		form.append("team_category", teamCategory);
		form.append("team_mode", teamMode);
		form.append("coach_id", coachID);
		form.append("logo", logo);
		form.append("tournament_id", tournamentID);

		$.ajax({
			type: "POST",
			url: cuicpro.ajax_url,
			data: form,
			processData: false,
			contentType: false,
			success: function (response) {
				if (response.success) {
					// add team to coach data
					$("#teams-coach-data").append(response.data.html);

					// clear inputs
					$("#team-name-table").val("");
					$("#team-logo-input").val(null);

					// update result box
					$("#team-result-table")
						.removeClass("error")
						.addClass("success")
						.html(response.data.message);
				} else {
					// update result box
					$("#team-result-table")
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

	$(document).on("click", "#update-team-button", function (e) {
		e.preventDefault();

		const teamID = $(this).data("team-id");
		const coachID = $(this).data("team-coach-id");
		const teamName = $("#team-name-table").val();
		const teamCategory = $("#team-category-table").val();
		const teamMode = $("#team-mode-table").val();
		const rawLogo = $("#team-logo-input");
		let logo = { name: "" };

		if (rawLogo[0].files.length > 0) {
			logo = rawLogo[0].files[0];
		}

		if (teamName === "") {
			$("#team-result-table")
				.removeClass("success")
				.addClass("error")
				.html("Agregar todos los datos del equipo, faltantes: Nombre");
			return;
		}

		const form = new FormData();
		form.append("action", "update_team");
		form.append("team_id", teamID);
		form.append("team_name", teamName);
		form.append("team_category", teamCategory);
		form.append("team_mode", teamMode);
		form.append("coach_id", coachID);
		form.append("logo", logo);

		$.ajax({
			type: "POST",
			url: cuicpro.ajax_url,
			data: form,
			processData: false,
			contentType: false,
			success: function (response) {
				if (response.success) {
					$("#coaches-dropdown-tv").val(response.data.coachID);

					$("#coach-data").html(response.data.coachData);

					$("#team-name-table").val("");
					$("#team-logo-input").val(null);

					$("#update-team-button").data("team-id", 0);
					$("#update-team-button").data("team-coach-id", 0);

					$("#add-team-button").removeClass("hidden");
					$("#update-team-button").addClass("hidden");
					$("#cancel-team-button").addClass("hidden");

					$("#team-division-table").prop("disabled", false);
					$("#team-division-dropdown").prop("disabled", false);

					$("#team-result-table")
						.removeClass("error")
						.addClass("success")
						.html(response.data.message);
				} else {
					$("#team-result-table")
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

	$(document).on("click", "#delete-team-button", function () {
		const teamID = $(this).attr("data-team-id");

		const confirmationBoxText =
			"¿Estas seguro de eliminar el equipo? Esta accion podria ser irreversible.";

		const onResponse = function (response) {
			if (response.success) {
				$(`#team-${teamID}`).remove();

				$("#team-result-table")
					.removeClass("error")
					.addClass("success")
					.html(response.data.message);
			} else {
				$("#team-result-table")
					.removeClass("success")
					.addClass("error")
					.html(response.data.message);
			}
		};

		confirmateActionBox(
			this,
			confirmationBoxText,
			"delete_team",
			{ team_id: teamID },
			onResponse,
		);
	});

	$(document).on("click", "#edit-team-button", function (e) {
		e.preventDefault();
		const teamID = $(this).data("team-id");

		$.ajax({
			type: "POST",
			url: cuicpro.ajax_url,
			data: {
				action: "edit_team",
				team_id: teamID,
			},
			success: function (response) {
				if (response.success) {
					$("#team-name-table").val(response.data.team.team_name);
					$("#team-logo-table").val(response.data.team.logo);
					$("#team-category-table").val(response.data.team.team_category);
					$("#team-mode-table").val(response.data.team.team_mode);

					$("#add-team-button").addClass("hidden");
					$("#update-team-button").removeClass("hidden");
					$("#cancel-team-button").removeClass("hidden");

					$("#update-team-button").data("team-id", teamID);
					$("#update-team-button").data(
						"team-coach-id",
						response.data.team.coach_id,
					);

					$("#team-result-table")
						.removeClass("error")
						.addClass("success")
						.html(response.data.message);
				} else {
					$("#team-result-table")
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

	$(document).on("click", "#cancel-team-button", function (e) {
		e.preventDefault();

		$("#team-name-table").val("");
		$("#team-logo-table").val("");
		$("#team-division-table").val("0");
		$("#team-category-table").val("1");
		$("#team-mode-table").val("1");

		$("#add-team-button").removeClass("hidden");
		$("#update-team-button").addClass("hidden");
		$("#cancel-team-button").addClass("hidden");

		$("#team-division-table").prop("disabled", false);
		$("#team-division-dropdown").prop("disabled", false);

		$("#team-result-table")
			.removeClass("error")
			.addClass("success")
			.html("Edicion cancelada.");
	});

	$(document).on("change", "#team-division-dropdown", function (e) {
		e.preventDefault();
		const teamID = $(this).data("team-id");
		const divisionID = $(this).val();

		jQuery.ajax({
			type: "POST",
			url: cuicpro.ajax_url,
			data: {
				action: "update_team_division",
				team_id: teamID,
				division_id: divisionID,
			},
			success: function (response) {
				if (response.success) {
					jQuery("#team-result-table")
						.removeClass("error")
						.addClass("success")
						.html(response.data.message);
				} else {
					jQuery("#team-result-table")
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

	$(document).on("change", "#team-players-dropdown", function (e) {
		e.preventDefault();
		const teamID = $(this).val();

		jQuery.ajax({
			type: "POST",
			url: cuicpro.ajax_url,
			data: {
				action: "fetch_team_players_data",
				team_id: teamID,
			},
			success: function (response) {
				if (response.success) {
					jQuery("#players-data").html(response.data.html);

					jQuery("#team-result-table")
						.removeClass("error")
						.addClass("success")
						.html(response.data.message);
				} else {
					jQuery("#team-result-table")
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
});

jQuery(document).on("click", "#enrolled-team-button", function () {
	const teamID = jQuery(this).data("team-id");
	const teamIsEnrolled = jQuery(this).is(":checked") ? 1 : 0;

	console.log(teamID, teamIsEnrolled);
	jQuery.ajax({
		type: "POST",
		url: cuicpro.ajax_url,
		data: {
			action: "update_team_enrolled",
			team_id: teamID,
			team_is_enrolled: teamIsEnrolled,
		},
		success: function (response) {
			if (response.success) {
				jQuery(`#enrolled-team-button[data-team-id=${teamID}]`).prop(
					"checked",
					teamIsEnrolled,
				);

				jQuery("#team-result-table")
					.removeClass("error")
					.addClass("success")
					.html(response.data.message);
			} else {
				jQuery("#team-result-table")
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

// function updateTeamDivision(teamID) {
// 	const divisionDropdown = jQuery("[id='team-division-dropdown']");

// 	const divisionID = jQuery(
// 		`#team-division-dropdown[data-team-id=${teamID}]`,
// 	).val();

// 	const selectedDivisions = {};
// 	let duplicatedDivisions = false;

// 	for (let dropdown of divisionDropdown) {
// 		if (!selectedDivisions[dropdown.value]) {
// 			selectedDivisions[dropdown.value] = true;
// 		} else {
// 			duplicatedDivisions = true;
// 		}
// 	}

// 	console.log(teamID, divisionID);

// 	return;
// 	jQuery.ajax({
// 		type: "POST",
// 		url: cuicpro.ajax_url,
// 		data: {
// 			action: "update_team_division",
// 			team_id: teamID,
// 			division_id: divisionID,
// 		},
// 		success: function (response) {
// 			if (response.success) {
// 				jQuery("#team-result-table")
// 					.removeClass("error")
// 					.addClass("success")
// 					.html(response.data.message);
// 			} else {
// 				jQuery("#team-result-table")
// 					.removeClass("success")
// 					.addClass("error")
// 					.html(response.data.message);
// 			}
// 		},
// 		error: function (xhr, status, error) {
// 			console.error("Error:", error);
// 		},
// 	});
// }
