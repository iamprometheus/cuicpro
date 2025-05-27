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

						const result = document.querySelector("#division-result-table");
						result.classList.remove("success");
						result.classList.add("error");
						result.innerHTML = response.data.message;
					} else {
						const result = document.querySelector("#division-result-table");
						result.classList.remove("success");
						result.classList.add("error");
						result.innerHTML = response.data.message;
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
});

jQuery(document).ready(function ($) {
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

						const result = document.querySelector("#team-result-table");
						result.classList.remove("success");
						result.classList.add("error");
						result.innerHTML = response.data.message;
					} else {
						const result = document.querySelector("#team-result-table");
						result.classList.remove("success");
						result.classList.add("error");
						result.innerHTML = response.data.message;
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
});

jQuery(document).on("click", "#add-team-button", function (e) {
	e.preventDefault();

	const divisionID = jQuery("#team-division-table").val();
	const teamName = jQuery("#team-name-table").val();
	const teamCategory = jQuery("#team-category-table").val();
	const teamMode = jQuery("#team-mode-table").val();
	const coachID = jQuery("#coaches-dropdown-tv").val();
	const logo = jQuery("#team-logo-table").val();

	if (teamName === "" || logo === "") {
		alert(
			"Agregar todos los datos del equipo, faltantes: " +
				(teamName === "" ? "Nombre, " : "") +
				(logo === "" ? "Logo" : ""),
		);
		return;
	}

	jQuery.ajax({
		type: "POST",
		url: cuicpro.ajax_url,
		data: {
			action: "add_team",
			division_id: divisionID,
			team_name: teamName,
			team_category: teamCategory,
			team_mode: teamMode,
			coach_id: coachID,
			logo: logo,
		},
		success: function (response) {
			if (response.success) {
				const element = document.querySelector("#dynamic-input-team");
				const newElement = document.createElement("div");
				newElement.innerHTML = response.data.html;
				element.insertAdjacentElement("beforebegin", newElement);

				const inputName = document.querySelector("#team-name-table");
				inputName.value = "";
				const inputLogo = document.querySelector("#team-logo-table");
				inputLogo.value = "";

				const result = document.querySelector("#team-result-table");
				result.classList.remove("success");
				result.classList.add("error");
				result.innerHTML = response.data.message;
			} else {
				const result = document.querySelector("#team-result-table");
				result.classList.remove("success");
				result.classList.add("error");
				result.innerHTML = response.data.message;
			}
		},
		error: function (xhr, status, error) {
			console.error("Error:", error);
		},
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
				const result = document.querySelector("#team-result-table");
				result.classList.remove("error");
				result.classList.add("success");
				result.innerHTML = response.data.message;
			} else {
				const result = document.querySelector("#team-result-table");
				result.classList.remove("success");
				result.classList.add("error");
				result.innerHTML = response.data.message;
			}
		},
		error: function (xhr, status, error) {
			console.error("Error:", error);
		},
	});
}

jQuery(document).on("click", "#update-team-button", function (e) {
	e.preventDefault();

	const teamID = jQuery(this).data("team-id");
	const coachID = jQuery(this).data("team-coach-id");
	const divisionID = jQuery("#team-division-table").val();
	const teamName = jQuery("#team-name-table").val();
	const teamCategory = jQuery("#team-category-table").val();
	const teamMode = jQuery("#team-mode-table").val();
	const logo = jQuery("#team-logo-table").val();

	if (teamName === "" || logo === "") {
		alert(
			"Agregar todos los datos del equipo, faltantes: " +
				(teamName === "" ? "Nombre, " : "") +
				(logo === "" ? "Logo" : ""),
		);
		return;
	}

	jQuery.ajax({
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
				const coachesDropdown = document.querySelector("#coaches-dropdown-tv");
				coachesDropdown.value = response.data.coachID;

				const coachData = document.querySelector("#coach-data");
				coachData.innerHTML = response.data.coachData;

				const inputName = document.querySelector("#team-name-table");
				inputName.value = "";
				const inputLogo = document.querySelector("#team-logo-table");
				inputLogo.value = "";

				jQuery("#update-team-button").data("team-id", 0);
				jQuery("#update-team-button").data("team-coach-id", 0);

				document.querySelector("#add-team-button").classList.remove("hidden");
				document.querySelector("#update-team-button").classList.add("hidden");
				document.querySelector("#cancel-team-button").classList.add("hidden");

				const result = document.querySelector("#team-result-table");
				result.classList.remove("error");
				result.classList.add("success");
				result.innerHTML = response.data.message;
			} else {
				const result = document.querySelector("#team-result-table");
				result.classList.remove("success");
				result.classList.add("error");
				result.innerHTML = response.data.message;
			}
		},
		error: function (xhr, status, error) {
			console.error("Error:", error);
		},
	});
});

jQuery(document).on("click", "#delete-team-button", function () {
	const teamID = jQuery(this).data("team-id");

	jQuery.ajax({
		type: "POST",
		url: cuicpro.ajax_url,
		data: {
			action: "delete_team",
			team_id: teamID,
		},
		success: function (response) {
			if (response.success) {
				const element = document.querySelector(`#team-${teamID}`);
				element.remove();

				const result = document.querySelector("#team-result-table");
				result.classList.remove("success");
				result.classList.add("error");
				result.innerHTML = response.data.message;
			} else {
				const result = document.querySelector("#team-result-table");
				result.classList.remove("success");
				result.classList.add("error");
				result.innerHTML = response.data.message;
			}
		},
		error: function (xhr, status, error) {
			console.error("Error:", error);
		},
	});
});

jQuery(document).on("click", "#edit-team-button", function (e) {
	e.preventDefault();
	const teamID = jQuery(this).data("team-id");

	jQuery.ajax({
		type: "POST",
		url: cuicpro.ajax_url,
		data: {
			action: "edit_team",
			team_id: teamID,
		},
		success: function (response) {
			if (response.success) {
				const divisionsDropdown = document.querySelector(
					"#team-division-table",
				);
				divisionsDropdown.innerHTML = response.data.dropdown;

				const inputName = document.querySelector("#team-name-table");
				inputName.value = response.data.team.team_name;
				const inputLogo = document.querySelector("#team-logo-table");
				inputLogo.value = response.data.team.logo;
				const inputCategory = document.querySelector("#team-category-table");
				inputCategory.value = response.data.team.team_category;
				const inputMode = document.querySelector("#team-mode-table");
				inputMode.value = response.data.team.team_mode;

				document.querySelector("#add-team-button").classList.add("hidden");
				document
					.querySelector("#update-team-button")
					.classList.remove("hidden");
				document
					.querySelector("#cancel-team-button")
					.classList.remove("hidden");

				jQuery("#update-team-button").data(
					"team-id",
					response.data.team.team_id,
				);
				jQuery("#update-team-button").data(
					"team-coach-id",
					response.data.team.coach_id,
				);

				const result = document.querySelector("#team-result-table");
				result.classList.remove("error");
				result.classList.add("success");
				result.innerHTML = response.data.message;
			} else {
				const result = document.querySelector("#team-result-table");
				result.classList.remove("success");
				result.classList.add("error");
				result.innerHTML = response.data.message;
			}
		},
		error: function (xhr, status, error) {
			console.error("Error:", error);
		},
	});
});

jQuery(document).on("click", "#cancel-team-button", function (e) {
	e.preventDefault();

	const inputName = document.querySelector("#team-name-table");
	inputName.value = "";
	const inputLogo = document.querySelector("#team-logo-table");
	inputLogo.value = "";
	const inputDivision = document.querySelector("#team-division-table");
	inputDivision.value = "0";
	const inputCategory = document.querySelector("#team-category-table");
	inputCategory.value = "1";
	const inputMode = document.querySelector("#team-mode-table");
	inputMode.value = "1";

	document.querySelector("#add-team-button").classList.remove("hidden");
	document.querySelector("#update-team-button").classList.add("hidden");
	document.querySelector("#cancel-team-button").classList.add("hidden");

	const result = document.querySelector("#team-result-table");
	result.classList.remove("error");
	result.classList.add("success");
	result.innerHTML = "Edicion cancelada.";
});
