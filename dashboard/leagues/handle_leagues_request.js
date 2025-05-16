jQuery(document).on("click", "#add-league-button-lv", function (e) {
	e.preventDefault();

	const leagueName = jQuery("#league-name-lv").val();
	const leagueMode = jQuery("#league-mode-lv").val();

	if (leagueName === "") {
		alert("Agregar un nombre a la liga");
		return;
	}

	jQuery.ajax({
		type: "POST",
		url: cuicpro.ajax_url,
		data: {
			action: "add_league",
			league_name: leagueName,
			league_mode: leagueMode,
		},
		success: function (response) {
			if (response.success) {
				alert(response.data.message);
				// add league to the table
				const element = document.querySelector("#dynamic-input-league");
				const newElement = document.createElement("div");
				newElement.innerHTML = `
				<div class="league-wrapper" id="league-${response.data.league.league_id}">
					<span class="league-cell">${response.data.league.league_name}</span>
					<span class="league-cell">${response.data.league.league_mode}</span>
				<div class="team-cell">
					<button id="delete-league-button-lv" data-league-id="${response.data.league.league_id}">Eliminar</button>
				</div></div>
				`;
				element.insertAdjacentElement("beforebegin", newElement);

				// clear inputs from table
				const inputName = document.querySelector("#league-name-lv");
				inputName.value = "";
				const inputMode = document.querySelector("#league-mode-lv");
				inputMode.value = "5v5";

				// update dropdown
				const dropdown = document.querySelector("#leagues-dropdown-tv");
				dropdown.innerHTML += `<option value="${response.data.league.league_id}">${response.data.league.league_name}</option>`;
			} else {
				alert(response.data.message);
			}
		},
		error: function (xhr, status, error) {
			console.error("Error:", error);
		},
	});
});

jQuery(document).on("click", "#delete-league-button-lv", function () {
	const leagueID = jQuery(this).data("league-id");

	jQuery.ajax({
		type: "POST",
		url: cuicpro.ajax_url,
		data: {
			action: "delete_league",
			league_id: leagueID,
		},
		success: function (response) {
			alert(response.data.message);
			if (response.success) {
				const parent = document
					.querySelector(`#league-${leagueID}`)
					.closest("div");
				parent.remove();

				// remove option from dropdown
				const dropdown = document.querySelector("#leagues-dropdown-tv");
				dropdown.querySelector(`option[value="${leagueID}"]`).remove();

				//remove table data
				const element = document.querySelector("#league-data");
				element.innerHTML = "";
			}
		},
		error: function (xhr, status, error) {
			console.error("Error:", error);
		},
	});
});
