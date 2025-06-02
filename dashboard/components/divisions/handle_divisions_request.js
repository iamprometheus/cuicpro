jQuery(document).on("click", "#add-division-button", function (e) {
	e.preventDefault();

	const divisionName = jQuery("#division-name").val();
	const divisionMode = jQuery("#division-mode").val();
	const divisionMinTeams = jQuery("#division-min-teams").val();
	const divisionMaxTeams = jQuery("#division-max-teams").val();
	const divisionCategory = jQuery("#division-category").val();

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
				clearDivisionInputs();

				// update dropdown
				const dropdown = document.querySelector("#divisions-dropdown-tv");
				dropdown.innerHTML += response.data.dropdown;

				jQuery("#division-result-table")
					.removeClass("error")
					.addClass("success")
					.html(response.data.message);
			} else {
				jQuery("#division-result-table")
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

jQuery(document).on("click", "#delete-division-button", function () {
	const divisionID = jQuery(this).data("division-id");

	jQuery.ajax({
		type: "POST",
		url: cuicpro.ajax_url,
		data: {
			action: "delete_division",
			division_id: divisionID,
		},
		success: function (response) {
			if (response.success) {
				const parent = document
					.querySelector(`#division-${divisionID}`)
					.closest("div");
				parent.remove();

				// remove option from dropdown
				const dropdown = document.querySelector("#divisions-dropdown-tv");
				dropdown.querySelector(`option[value="${divisionID}"]`).remove();

				jQuery("#division-result-table")
					.removeClass("error")
					.addClass("success")
					.html(response.data.message);
			} else {
				jQuery("#division-result-table")
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

jQuery(document).on("click", "#edit-division-button", function () {
	const divisionID = jQuery(this).data("division-id");

	jQuery.ajax({
		type: "POST",
		url: cuicpro.ajax_url,
		data: {
			action: "edit_division",
			division_id: divisionID,
		},
		success: function (response) {
			if (response.success) {
				const division = response.data.division;
				const divisionName = division.division_name;
				const divisionMode = division.division_mode;
				const divisionMinTeams = division.division_min_teams;
				const divisionMaxTeams = division.division_max_teams;
				const divisionCategory = division.division_category;

				jQuery("#division-name").val(divisionName);
				jQuery("#division-mode").val(divisionMode);
				jQuery("#division-min-teams").val(divisionMinTeams);
				jQuery("#division-max-teams").val(divisionMaxTeams);
				jQuery("#division-category").val(divisionCategory);

				jQuery("#add-division-button").addClass("hidden");
				jQuery("#update-division-button").removeClass("hidden");
				jQuery("#cancel-division-button").removeClass("hidden");

				jQuery("#update-division-button").attr("data-division-id", divisionID);

				jQuery("#division-result-table")
					.removeClass("error")
					.addClass("success")
					.html(response.data.message);
			} else {
				jQuery("#division-result-table")
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

jQuery(document).on("click", "#cancel-division-button", function (e) {
	e.preventDefault();

	clearDivisionInputs();

	jQuery("#add-division-button").removeClass("hidden");
	jQuery("#update-division-button").addClass("hidden");
	jQuery("#cancel-division-button").addClass("hidden");

	jQuery("#division-result-table")
		.removeClass("error")
		.addClass("success")
		.html("Edicion cancelada.");
});

jQuery(document).on("click", "#update-division-button", function (e) {
	e.preventDefault();

	const divisionID = jQuery(this).data("division-id");
	const divisionName = jQuery("#division-name").val();
	const divisionMode = jQuery("#division-mode").val();
	const divisionMinTeams = jQuery("#division-min-teams").val();
	const divisionMaxTeams = jQuery("#division-max-teams").val();
	const divisionCategory = jQuery("#division-category").val();

	if (divisionName === "") {
		jQuery("#division-result-table")
			.removeClass("success")
			.addClass("error")
			.html("Agregar todos los datos de la division, faltantes: Nombre");
		return;
	}

	jQuery.ajax({
		type: "POST",
		url: cuicpro.ajax_url,
		data: {
			action: "update_division",
			division_id: divisionID,
			division_name: divisionName,
			division_mode: divisionMode,
			division_min_teams: divisionMinTeams,
			division_max_teams: divisionMaxTeams,
			division_category: divisionCategory,
		},
		success: function (response) {
			if (response.success) {
				jQuery(`#division-${divisionID}`).html(response.data.html);

				clearDivisionInputs();

				jQuery("#update-division-button").attr("data-division-id", 0);

				jQuery("#add-division-button").removeClass("hidden");
				jQuery("#update-division-button").addClass("hidden");
				jQuery("#cancel-division-button").addClass("hidden");

				jQuery("#division-result-table")
					.removeClass("error")
					.addClass("success")
					.html(response.data.message);
			} else {
				jQuery("#division-result-table")
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

function clearDivisionInputs() {
	jQuery("#division-name").val("");
	jQuery("#division-mode").val("1");
	jQuery("#division-min-teams").val("4");
	jQuery("#division-max-teams").val("30");
	jQuery("#division-category").val("1");
}
