jQuery(document).ready(function ($) {
	$("#leagues-dropdown-tv").change(function () {
		const leagueID = $(this).val();

		if (leagueID !== "0") {
			$.ajax({
				type: "POST",
				url: cuicpro.ajax_url,
				data: {
					action: "fetch_league_data",
					league_id: leagueID,
				},
				success: function (response) {
					if (response.success) {
						const newElement = document.querySelector("#league-data");
						newElement.innerHTML = response.data.html;
					} else {
						alert(response.data.message);
					}
				},
				error: function (xhr, status, error) {
					console.error("Error:", error);
				},
			});
		} else {
			const element = document.querySelector("#league-data");
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
					} else {
						alert(response.data.message);
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

jQuery(document).on("click", "#delete-team-button-tv", function () {
	const teamID = jQuery(this).data("team-id");

	jQuery.ajax({
		type: "POST",
		url: cuicpro.ajax_url,
		data: {
			action: "delete_team",
			team_id: teamID,
		},
		success: function (response) {
			alert(response.data.message);
			if (response.success) {
				const element = document.querySelector(`#team-${teamID}`);
				element.remove();
			}
		},
		error: function (xhr, status, error) {
			console.error("Error:", error);
		},
	});
});

jQuery(document).on("click", "#add-team-button-tv", function (e) {
	e.preventDefault();

	const leagueID = jQuery("#leagues-dropdown-tv").val();
	const teamName = jQuery("#team-name-ta").val();
	const city = jQuery("#team-city-ta").val();
	const state = jQuery("#team-state-ta").val();
	const country = jQuery("#team-country-ta").val();
	const coachID = jQuery("#team-coach-ta").val();
	const logo = jQuery("#team-logo-ta").val();

	if (
		teamName === "" ||
		city === "" ||
		state === "" ||
		country === "" ||
		coachID === "" ||
		logo === ""
	) {
		alert("Agregar todos los datos del equipo");
		return;
	}

	jQuery.ajax({
		type: "POST",
		url: cuicpro.ajax_url,
		data: {
			action: "add_team",
			league_id: leagueID,
			team_name: teamName,
			city: city,
			state: state,
			country: country,
			coach_id: coachID,
			logo: logo,
		},
		success: function (response) {
			if (response.success) {
				alert(response.data.message);
				const element = document.querySelector("#dynamic-input-team");
				const newElement = document.createElement("div");
				newElement.innerHTML = `
				<div class="team-wrapper" id="team-${response.data.team.team_id}">
					<span class="team-cell">${response.data.team.team_name}</span>
					<span class="team-cell">${response.data.team.city}</span>
					<span class="team-cell">${response.data.team.state}</span>
					<span class="team-cell">${response.data.team.country}</span>
					<span class="team-cell">${response.data.team.coach_name}</span>
					<div class="team-cell">
						<img src="${response.data.team.logo}">
					</div>
				<div class="team-cell">
					<button id="delete-team-button-tv" data-team-id="${response.data.team.team_id}">Eliminar</button>
				</div></div>
				`;
				element.insertAdjacentElement("beforebegin", newElement);

				const inputName = document.querySelector("#team-name-ta");
				inputName.value = "";
				const inputCity = document.querySelector("#team-city-ta");
				inputCity.value = "";
				const inputState = document.querySelector("#team-state-ta");
				inputState.value = "";
				const inputCountry = document.querySelector("#team-country-ta");
				inputCountry.value = "";
				const inputCoachID = document.querySelector("#team-coach-ta");
				inputCoachID.value = "";
				const inputLogo = document.querySelector("#team-logo-ta");
				inputLogo.value = "";
			} else {
				alert(response.data.message);
			}
		},
		error: function (xhr, status, error) {
			console.error("Error:", error);
		},
	});
});
