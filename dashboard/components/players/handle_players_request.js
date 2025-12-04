// jQuery(document).on("click", "#add-player-button", function (e) {
// 	e.preventDefault();

// 	const playerName = jQuery("#player-name").val();
// 	const playerPhoto = jQuery("#player-photo-input").val();

// 	if (playerName === "") {
// 		jQuery("#player-result-table")
// 			.removeClass("success")
// 			.addClass("error")
// 			.html("Agregar un nombre al jugador");
// 		return;
// 	}

// 	jQuery.ajax({
// 		type: "POST",
// 		url: cuicpro.ajax_url,
// 		data: {
// 			action: "add_player_admin",
// 			player_name: playerName,
// 			player_photo: playerPhoto,
// 		},
// 		success: function (response) {
// 			if (response.success) {
// 				// add player to the table
// 				jQuery("#players-data").append(response.data.html);

// 				clearPlayerInputs(response.data.tournament_days);

// 				jQuery("#player-result-table")
// 					.removeClass("error")
// 					.addClass("success")
// 					.html(response.data.message);
// 			} else {
// 				jQuery("#player-result-table")
// 					.removeClass("success")
// 					.addClass("error")
// 					.html(response.data.message);
// 			}
// 		},
// 		error: function (xhr, status, error) {
// 			console.error("Error:", error);
// 		},
// 	});
// });

jQuery(document).on("click", "#delete-player-button", function () {
	const playerID = jQuery(this).data("player-id");

	const confirmationBoxText =
		"¿Estas seguro de eliminar el jugador? Esta accion podria ser irreversible.";

	const onResponse = function (response) {
		if (response.success) {
			jQuery(`#player-${playerID}`).remove();

			jQuery("#player-result-table")
				.removeClass("error")
				.addClass("success")
				.html(response.data.message);
		} else {
			jQuery("#player-result-table")
				.removeClass("success")
				.addClass("error")
				.html(response.data.message);
		}
	};

	confirmateActionBox(
		this,
		confirmationBoxText,
		"delete_player_admin",
		{ player_id: playerID },
		onResponse,
	);
});

jQuery(document).on("click", "#edit-player-button", function () {
	const playerID = jQuery(this).data("player-id");

	jQuery.ajax({
		type: "POST",
		url: cuicpro.ajax_url,
		data: {
			action: "edit_player_admin",
			player_id: playerID,
		},
		success: function (response) {
			if (response.success) {
				const player = response.data.player;
				const playerName = player.player_name;

				jQuery("#player-name").val(playerName);

				// jQuery("#add-player-button").addClass("hidden");
				jQuery("#update-player-button").removeClass("hidden");
				jQuery("#cancel-player-button").removeClass("hidden");

				jQuery("#update-player-button").data("player-id", playerID);

				jQuery("#player-result-table")
					.removeClass("error")
					.addClass("success")
					.html(response.data.message);
			} else {
				jQuery("#player-result-table")
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

jQuery(document).on("click", "#cancel-player-button", function (e) {
	e.preventDefault();

	clearPlayerInputs();

	// jQuery("#add-player-button").removeClass("hidden");
	jQuery("#update-player-button").addClass("hidden");
	jQuery("#cancel-player-button").addClass("hidden");

	jQuery("#player-result-table")
		.removeClass("error")
		.addClass("success")
		.html("Edicion cancelada.");
});

jQuery(document).on("click", "#update-player-button", function (e) {
	e.preventDefault();

	const playerID = jQuery(this).data("player-id");
	const playerName = jQuery("#player-name").val();
	// const rawLogo = jQuery("#player-photo-input");
	// const logo = rawLogo[0].files[0];

	if (playerName === "") {
		jQuery("#player-result-table")
			.removeClass("success")
			.addClass("error")
			.html("Agregar un nombre al jugador");
		return;
	}

	const form = new FormData();
	form.append("action", "update_player_admin");
	form.append("player_id", playerID);
	form.append("player_name", playerName);
	//form.append("player_photo", logo);

	jQuery.ajax({
		type: "POST",
		url: cuicpro.ajax_url,
		data: form,
		processData: false,
		contentType: false,
		success: function (response) {
			if (response.success) {
				jQuery(`#player-${playerID}`).html(response.data.html);

				clearPlayerInputs();
				// jQuery("#add-player-button").removeClass("hidden");
				jQuery("#update-player-button").addClass("hidden");
				jQuery("#cancel-player-button").addClass("hidden");

				jQuery("#player-result-table")
					.removeClass("error")
					.addClass("success")
					.html(response.data.message);
			} else {
				jQuery("#player-result-table")
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

jQuery(document).on("change", "#filter-by-coach", function () {
	const filter = jQuery(this).val();
	jQuery.ajax({
		type: "POST",
		url: cuicpro.ajax_url,
		data: {
			action: "filter_by_coach",
			filter: filter,
		},
		success: function (response) {
			console.log(response.data)
			if (response.success) {
				jQuery("#filter-by-team").html(response.data.filters);
				jQuery("#players-data-2").html(response.data.players);
			} else {
				jQuery("#player-result-table")
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

jQuery(document).on("change", "#filter-by-team", function () {
	const filter = jQuery(this).val();
	const coach = jQuery("#filter-by-coach").val();
	jQuery.ajax({
		type: "POST",
		url: cuicpro.ajax_url,
		data: {
			action: "filter_by_team",
			filter: filter,
			coach: coach,
		},
		success: function (response) {
			if (response.success) {
				jQuery("#players-data-2").html(response.data.players);
			} else {
				jQuery("#player-result-table")
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

function clearPlayerInputs() {
	jQuery("#player-name").val("");
	//jQuery("#player-photo-input").val(null);
}
