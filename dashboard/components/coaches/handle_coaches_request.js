jQuery(document).on("click", "#add-coach-button-cv", function (e) {
	e.preventDefault();

	const coachName = jQuery("#coach-name-cv").val();
	const coachContact = jQuery("#coach-contact-cv").val();
	const coachCity = jQuery("#coach-city-cv").val();
	const coachState = jQuery("#coach-state-cv").val();
	const coachCountry = jQuery("#coach-country-cv").val();

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
		},
		success: function (response) {
			if (response.success) {
				alert(response.data.message);

				// add coach to the table
				const element = document.querySelector("#dynamic-input-coach");
				const newElement = document.createElement("div");
				newElement.innerHTML = `
				<div class="coach-wrapper" id="coach-${response.data.coach.coach_id}">
					<span class="coach-cell">${response.data.coach.coach_name}</span>
					<span class="coach-cell">${response.data.coach.coach_contact}</span>
					<span class="coach-cell">${response.data.coach.coach_city}</span>
					<span class="coach-cell">${response.data.coach.coach_state}</span>
					<span class="coach-cell">${response.data.coach.coach_country}</span>
				<div class="team-cell">	
					<button id="delete-coach-button-cv" data-coach-id="${response.data.coach.coach_id}">Eliminar</button>
				</div></div>
				`;
				element.insertAdjacentElement("beforebegin", newElement);

				const inputName = document.querySelector("#coach-name-cv");
				inputName.value = "";
				const inputContact = document.querySelector("#coach-contact-cv");
				inputContact.value = "";
				const inputCity = document.querySelector("#coach-city-cv");
				inputCity.value = "";
				const inputState = document.querySelector("#coach-state-cv");
				inputState.value = "";
				const inputCountry = document.querySelector("#coach-country-cv");
				inputCountry.value = "";

				// update coaches dropdown from team by league viewer
				const dropdown = document.querySelector("#team-coach-ta");
				if (dropdown) {
					dropdown.innerHTML += `<option value="${response.data.coach.coach_id}">${response.data.coach.coach_name}</option>`;
				}

				// update coaches dropdown from teams by coach viewer
				const dropdown2 = document.querySelector("#coaches-dropdown-tv");
				dropdown2.innerHTML += `<option value="${response.data.coach.coach_id}">${response.data.coach.coach_name}</option>`;
			} else {
				alert(response.data.message);
			}
		},
		error: function (xhr, status, error) {
			console.error("Error:", error);
		},
	});
});

jQuery(document).on("click", "#delete-coach-button", function () {
	const coachID = jQuery(this).data("coach-id");

	jQuery.ajax({
		type: "POST",
		url: cuicpro.ajax_url,
		data: {
			action: "delete_coach",
			coach_id: coachID,
		},
		success: function (response) {
			alert(response.data.message);
			if (response.success) {
				const parent = document
					.querySelector(`#coach-${coachID}`)
					.closest("div");
				parent.remove();

				// remove option from teams by league viewer dropdown
				const dropdown = document.querySelector("#team-coach-ta");
				if (dropdown) {
					dropdown.querySelector(`option[value="${coachID}"]`).remove();
				}

				// remove option from teams by coach viewer dropdown
				const dropdown2 = document.querySelector("#coaches-dropdown-tv");
				dropdown2.querySelector(`option[value="${coachID}"]`).remove();
				const element = document.querySelector("#coach-data");
				element.innerHTML = "";
			}
		},
		error: function (xhr, status, error) {
			console.error("Error:", error);
		},
	});
});
