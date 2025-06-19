jQuery(document).on("click", "#add-coach-button", function (e) {
	e.preventDefault();

	const coachName = jQuery("#coach-name").val();
	const coachContact = jQuery("#coach-contact").val();
	const coachCity = jQuery("#coach-city").val();
	const coachState = jQuery("#coach-state").val();
	const coachCountry = jQuery("#coach-country").val();
	const tournamentID = jQuery(".tournament-item[selected]")[0].id.replace(
		"tournament-",
		"",
	);

	if (coachName === "") {
		alert("Agregar un nombre al entrenador");
		return;
	}

	if (coachContact === "") {
		alert("Agregar el contacto al entrenador");
		return;
	}

	jQuery.ajax({
		type: "POST",
		url: cuicpro.ajax_url,
		data: {
			action: "add_coach",
			coach_name: coachName,
			coach_contact: coachContact,
			coach_city: coachCity,
			coach_state: coachState,
			coach_country: coachCountry,
			tournament_id: tournamentID,
		},
		success: function (response) {
			if (response.success) {
				// add coach to the table

				jQuery("#coaches-data").append(response.data.html);

				clearCoachInputs();

				// update coaches dropdown from teams by coach viewer
				jQuery("#coaches-dropdown-tv").append(
					`<option value="${response.data.coach.coach_id}">${response.data.coach.coach_name}</option>`,
				);

				jQuery("#coach-result-table")
					.removeClass("error")
					.addClass("success")
					.html(response.data.message);
			} else {
				jQuery("#coach-result-table")
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

jQuery(document).on("click", "#delete-coach-button", function () {
	const coachID = jQuery(this).data("coach-id");

	const confirmationBoxText =
		"¿Estas seguro de eliminar el coach? Esta accion podria ser irreversible.";

	const onResponse = function (response) {
		if (response.success) {
			const parent = jQuery(`#coach-${coachID}`).closest("div");
			parent.remove();

			// remove option from teams by coach viewer dropdown
			jQuery("#coaches-dropdown-tv")
				.find(`option[value="${coachID}"]`)
				.remove();

			jQuery("#coach-result-table")
				.removeClass("error")
				.addClass("success")
				.html(response.data.message);
		} else {
			jQuery("#coach-result-table")
				.removeClass("success")
				.addClass("error")
				.html(response.data.message);
		}
	};

	confirmateActionBox(
		this,
		confirmationBoxText,
		"delete_coach",
		{ coach_id: coachID },
		onResponse,
	);
});

jQuery(document).on("click", "#edit-coach-button", function (e) {
	e.preventDefault();
	const coachID = jQuery(this).data("coach-id");

	jQuery.ajax({
		type: "POST",
		url: cuicpro.ajax_url,
		data: {
			action: "edit_coach",
			coach_id: coachID,
		},
		success: function (response) {
			if (response.success) {
				jQuery("#coach-name").val(response.data.coach.coach_name);
				jQuery("#coach-contact").val(response.data.coach.coach_contact);
				jQuery("#coach-city").val(response.data.coach.coach_city);
				jQuery("#coach-state").val(response.data.coach.coach_state);
				jQuery("#coach-country").val(response.data.coach.coach_country);

				jQuery("#add-coach-button").addClass("hidden");
				jQuery("#update-coach-button").removeClass("hidden");
				jQuery("#cancel-coach-button").removeClass("hidden");

				jQuery("#update-coach-button").attr(
					"data-coach-id",
					response.data.coach.coach_id,
				);

				jQuery("#coach-result-table")
					.removeClass("error")
					.addClass("success")
					.html(response.data.message);
			} else {
				jQuery("#coach-result-table")
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

jQuery(document).on("click", "#cancel-coach-button", function () {
	clearCoachInputs();

	jQuery("#add-coach-button").removeClass("hidden");
	jQuery("#update-coach-button").addClass("hidden");
	jQuery("#cancel-coach-button").addClass("hidden");

	jQuery("#coach-result-table")
		.removeClass("error")
		.addClass("success")
		.html("Edicion cancelada");
});

jQuery(document).on("click", "#update-coach-button", function () {
	const coachID = jQuery(this).data("coach-id");
	const coachName = jQuery("#coach-name").val();
	const coachContact = jQuery("#coach-contact").val();
	const coachCity = jQuery("#coach-city").val();
	const coachState = jQuery("#coach-state").val();
	const coachCountry = jQuery("#coach-country").val();

	jQuery.ajax({
		type: "POST",
		url: cuicpro.ajax_url,
		data: {
			action: "update_coach",
			coach_id: coachID,
			coach_name: coachName,
			coach_contact: coachContact,
			coach_city: coachCity,
			coach_state: coachState,
			coach_country: coachCountry,
		},
		success: function (response) {
			if (response.success) {
				// update coach in the table
				jQuery(`#coach-${coachID}`).replaceWith(response.data.html);

				clearCoachInputs();

				jQuery("#add-coach-button").removeClass("hidden");
				jQuery("#update-coach-button").addClass("hidden");
				jQuery("#cancel-coach-button").addClass("hidden");

				jQuery("#coach-result-table")
					.removeClass("error")
					.addClass("success")
					.html(response.data.message);
			} else {
				jQuery("#coach-result-table")
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

function clearCoachInputs() {
	jQuery("#coach-name").val("");
	jQuery("#coach-contact").val("");
	jQuery("#coach-city").val("");
	jQuery("#coach-state").val("");
	jQuery("#coach-country").val("");
}
