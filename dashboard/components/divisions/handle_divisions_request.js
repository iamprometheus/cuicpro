jQuery(document).on("click", "#add-division-button-lv", function (e) {
	e.preventDefault();

	const divisionName = jQuery("#division-name-lv").val();
	const divisionMode = jQuery("#division-mode-lv").val();
	const divisionMinTeams = jQuery("#division-min-teams-lv").val();
	const divisionMaxTeams = jQuery("#division-max-teams-lv").val();
	const divisionCategory = jQuery("#division-category-lv").val();

	if (divisionName === "") {
		alert("Agregar un nombre a la division");
		return;
	}

	if (divisionMinTeams === "") {
		alert("Agregar un numero minimo de equipos");
		return;
	}

	if (parseInt(divisionMinTeams) < 3) {
		alert("Numero minimo de equipos debe ser 4 o mayor");
		return;
	}

	if (divisionMaxTeams === "") {
		alert("Agregar un numero maximo de equipos");
		return;
	}

	if (parseInt(divisionMaxTeams) < parseInt(divisionMinTeams)) {
		alert("Numero maximo de equipos debe ser mayor al minimo");
		return;
	}

	jQuery.ajax({
		type: "POST",
		url: cuicpro.ajax_url,
		data: {
			action: "add_division",
			division_name: divisionName,
			division_mode: divisionMode,
			division_min_teams: divisionMinTeams,
			division_max_teams: divisionMaxTeams,
			division_category: divisionCategory,
		},
		success: function (response) {
			console.log(response);
			if (response.success) {
				// add division to the table
				const divisionsData = document.querySelector("#divisions-data");
				divisionsData.classList.remove("cell-hidden");
				divisionsData.insertAdjacentHTML("beforeend", response.data.html);

				// clear inputs from table
				const inputName = document.querySelector("#division-name-lv");
				const inputMode = document.querySelector("#division-mode-lv");
				const inputMinTeams = document.querySelector("#division-min-teams-lv");
				const inputMaxTeams = document.querySelector("#division-max-teams-lv");
				const inputCategory = document.querySelector("#division-category-lv");
				inputName.value = "";
				inputMode.value = "1";
				inputMinTeams.value = "4";
				inputMaxTeams.value = "30";
				inputCategory.value = "1";

				// update dropdown
				const dropdown = document.querySelector("#divisions-dropdown-tv");
				dropdown.innerHTML += response.data.dropdown;
			} else {
				alert(response.data.message);
			}
		},
		error: function (xhr, status, error) {
			console.error("Error:", error);
		},
	});
});

jQuery(document).on("click", "#delete-division-button-lv", function () {
	const divisionID = jQuery(this).data("division-id");

	jQuery.ajax({
		type: "POST",
		url: cuicpro.ajax_url,
		data: {
			action: "delete_division",
			division_id: divisionID,
		},
		success: function (response) {
			alert(response.data.message);
			if (response.success) {
				const parent = document
					.querySelector(`#division-${divisionID}`)
					.closest("div");
				parent.remove();

				// remove option from dropdown
				const dropdown = document.querySelector("#divisions-dropdown-tv");
				dropdown.querySelector(`option[value="${divisionID}"]`).remove();

				//remove table data
				const divisionsData = document.querySelector(`#division-${divisionID}`);
				divisionsData.remove();
			}
		},
		error: function (xhr, status, error) {
			console.error("Error:", error);
		},
	});
});
