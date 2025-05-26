jQuery(document).on("click", "#add-official-button", function (e) {
	e.preventDefault();

	const officialName = jQuery("#official-name").val();
	const officialHours = jQuery("#official-hours").val();
	const officialSchedule = jQuery("#official-schedule").val();
	const officialMode = jQuery("#official-mode").val();
	const officialTeamId = jQuery("#official-team-id").val();
	const officialCity = jQuery("#official-city").val();
	const officialState = jQuery("#official-state").val();
	const officialCountry = jQuery("#official-country").val();

	if (officialName === "") {
		alert("Agregar un nombre al arbitro");
		return;
	}

	if (officialHours === "") {
		alert("Agregar las horas al arbitro");
		return;
	}

	if (officialSchedule === "") {
		alert("Agregar al menos un dia al arbitro");
		return;
	}

	if (officialCity === "" || officialState === "" || officialCountry === "") {
		alert("Agregar la ciudad, estado y pais del arbitro");
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
		},
		success: function (response) {
			if (response.success) {
				alert(response.data.message);

				// add official to the table
				const officialsTable = document.querySelector(".officials-table");
				officialsTable.insertAdjacentHTML("beforeend", response.data.html);

				const inputName = document.querySelector("#official-name");
				inputName.value = "";
				const inputHours = document.querySelector("#official-hours");
				inputHours.value = "1";
				const inputSchedule = jQuery("#official-schedule");
				inputSchedule.val(response.data.tournament_days);

				const inputMode = document.querySelector("#official-mode");
				inputMode.value = "1";
				const inputTeamId = document.querySelector("#official-team-id");
				inputTeamId.value = "0";
				const inputCity = document.querySelector("#official-city");
				inputCity.value = "";
				const inputState = document.querySelector("#official-state");
				inputState.value = "";
				const inputCountry = document.querySelector("#official-country");
				inputCountry.value = "";

				// update officials dropdown from team by league viewer
				const dropdown = document.querySelector("#team-official-ta");
				if (dropdown) {
					dropdown.innerHTML += `<option value="${response.data.official.official_id}">${response.data.official.official_name}</option>`;
				}
			} else {
				alert(response.data.message);
			}
		},
		error: function (xhr, status, error) {
			console.error("Error:", error);
		},
	});
});

jQuery(document).on("click", "#delete-official-button-cv", function () {
	const officialID = jQuery(this).data("official-id");

	jQuery.ajax({
		type: "POST",
		url: cuicpro.ajax_url,
		data: {
			action: "delete_official",
			official_id: officialID,
		},
		success: function (response) {
			alert(response.data.message);
			if (response.success) {
				const parent = document
					.querySelector(`#official-${officialID}`)
					.closest("div");
				parent.remove();

				// remove option from teams by league viewer dropdown
				const dropdown = document.querySelector("#team-official-ta");
				if (dropdown) {
					dropdown.querySelector(`option[value="${officialID}"]`).remove();
				}

				// remove option from teams by coach viewer dropdown
				const dropdown2 = document.querySelector("#officials-dropdown-tv");
				dropdown2.querySelector(`option[value="${officialID}"]`).remove();
				const element = document.querySelector("#official-data");
				element.innerHTML = "";
			}
		},
		error: function (xhr, status, error) {
			console.error("Error:", error);
		},
	});
});
