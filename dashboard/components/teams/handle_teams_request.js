jQuery(document).ready(function ($) {
	$("#divisions-dropdown-tv").change(function () {
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

	$("#coaches-dropdown-tv").change(function () {
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
						const newElement = document.querySelector("#coach-data");
						newElement.innerHTML = response.data.html;

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
			const element = document.querySelector("#coach-data");
			element.innerHTML = "";
		}
	});

	$("#add-team-button").click(function (e) {
		e.preventDefault();

		const divisionID = $("#team-division-table").val();
		const teamName = $("#team-name-table").val();
		const teamCategory = $("#team-category-table").val();
		const teamMode = $("#team-mode-table").val();
		const coachID = $("#coaches-dropdown-tv").val();
		const rawLogo = $("#team-logo-input");
		const logo = rawLogo[0].files[0];

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
		const divisionID = $("#team-division-table").val();
		const teamName = $("#team-name-table").val();
		const teamCategory = $("#team-category-table").val();
		const teamMode = $("#team-mode-table").val();
		const logo = $("#team-logo-table").val();

		if (teamName === "" || logo === "") {
			alert(
				"Agregar todos los datos del equipo, faltantes: " +
					(teamName === "" ? "Nombre, " : "") +
					(logo === "" ? "Logo" : ""),
			);
			return;
		}

		$.ajax({
			type: "POST",
			url: cuicpro.ajax_url,
			data: {
				action: "update_team",
				team_id: teamID,
				division_id: divisionID,
				team_name: teamName,
				team_category: teamCategory,
				team_mode: teamMode,
				coach_id: coachID,
				logo: logo,
			},
			success: function (response) {
				if (response.success) {
					$("#coaches-dropdown-tv").val(response.data.coachID);

					$("#coach-data").html(response.data.coachData);

					$("#team-name-table").val("");
					$("#team-logo-table").val("");

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

		$.ajax({
			type: "POST",
			url: cuicpro.ajax_url,
			data: {
				action: "delete_team",
				team_id: teamID,
			},
			success: function (response) {
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
			},
			error: function (xhr, status, error) {
				console.error("Error:", error);
			},
		});
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
					$("#team-division-table").html(response.data.dropdown);

					$("#team-name-table").val(response.data.team.team_name);
					$("#team-logo-table").val(response.data.team.logo);
					$("#team-category-table").val(response.data.team.team_category);
					$("#team-mode-table").val(response.data.team.team_mode);

					$("#team-division-table").prop("disabled", true);
					$("#team-division-dropdown").prop("disabled", true);

					$("#add-team-button").addClass("hidden");
					$("#update-team-button").removeClass("hidden");
					$("#cancel-team-button").removeClass("hidden");

					$("#update-team-button").attr(
						"data-team-id",
						response.data.team.team_id,
					);
					$("#update-team-button").attr(
						"data-team-coach-id",
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
});

function updateTeamDivision(teamID) {
	const divisionID = jQuery(
		`#team-division-dropdown[data-team-id=${teamID}]`,
	).val();

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
}
