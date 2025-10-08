jQuery(document).ready(function ($) {
	$(document).on("click", "#join-tournament-official-button", function (e) {
		e.preventDefault();
		const tournamentID = $(this).data("tournament-id");

		$.ajax({
			url: cuicpro.ajax_url,
			type: "POST",
			data: {
				action: "render_join_tournament_official",
				tournament_id: tournamentID,
			},
			success: function (response) {
				if (response.success) {
					$(".user-data-container").html(response.data.html);
				} else {
					alert(
						"Error al unirte al torneo, si el problema persiste contacta al administrador.",
					);
				}
			},
			error: function (xhr, status, error) {
				console.error("Error:", error);
			},
		});
	});

	$(document).on("submit", "#join-tournament-official-form", function (e) {
		e.preventDefault();
		const tournamentID = $(this).data("tournament-id");
		const officialDays = $("#tournament-selected-days")
			.data("tournament-days")
			.split(",");

		const officialSchedule = $("#tournament-selected-days").data(
			"tournament-days",
		);

		const officialMode = $(".form-radio-group")
			.children()
			.filter(function () {
				return $(this).children()[0].checked;
			})
			.children()[0].value;

		const officialHours = {};
		for (let i = 0; i < officialDays.length; i++) {
			const rawDay = officialDays[i];
			officialHours[rawDay] = [];
			const day = rawDay.replaceAll("/", "-");

			if (jQuery(`#official-day-${day}`).hasClass("hidden")) {
				continue;
			}

			const hoursContainer = jQuery(
				`#hours-selector-${day} .hours-selector-item`,
			);
			for (let j = 0; j < hoursContainer.length; j++) {
				if (hoursContainer[j].children[0].id === "hours-selector-all") continue;

				if (hoursContainer[j].children[0].checked) {
					officialHours[rawDay].push(hoursContainer[j].children[0].value);
				}
			}
		}

		let hasHours = false;
		for (let day in officialHours) {
			if (officialHours[day].length !== 0) {
				hasHours = true;
			} else {
				officialDays.splice(officialDays.indexOf(day), 1);
			}
		}

		if (!hasHours) {
			alert("Agregar al menos una hora a tu horario");
			return;
		}

		$.ajax({
			url: cuicpro.ajax_url,
			type: "POST",
			data: {
				action: "handle_join_tournament_official",
				tournament_id: tournamentID,
				official_hours: officialHours,
				official_days: officialSchedule,
				official_mode: officialMode,
			},
			success: function (response) {
				if (response.success) {
					alert(
						"¡Te has registrado exitosamente! Podrás ver tus partidos en la sección de partidos una vez que aprueben tu solicitud.",
					);
					$(".user-data-container").html(response.data.html);
				} else {
					alert(
						"Error al registrarte como arbitro en el torneo, si el problema persiste contacta al administrador.",
					);
				}
			},
			error: function (xhr, status, error) {
				console.error("Error:", error);
			},
		});
	});

	$(document).on(
		"click",
		"#show-tournament-official-matches-button",
		function (e) {
			e.preventDefault();
			const tournamentID = $(this).data("tournament-id");

			$.ajax({
				url: cuicpro.ajax_url,
				type: "POST",
				data: {
					action: "handle_render_official_matches_fe",
					tournament_id: tournamentID,
				},
				success: function (response) {
					if (response.success) {
						$(".user-data-container").html(response.data.html);
					} else {
						alert(
							"Error al mostrar tus partidos, si el problema persiste contacta al administrador.",
						);
					}
				},
				error: function (xhr, status, error) {
					console.error("Error:", error);
				},
			});
		},
	);
});

jQuery(document).on("click", ".hours-selector-container", function () {
	const id = jQuery(this).attr("id").replace("official-day-", "");
	jQuery("#hours-selector-" + id).toggleClass("hidden");
});

jQuery(document).on("click", ".hours-viewer-container", function () {
	const id = jQuery(this).children("span").text().replaceAll("/", "-");

	jQuery(this)
		.next("#hours-viewer-" + id)
		.toggleClass("hidden");
});

jQuery(document).on("click", "#hours-selector-all", function () {
	const allHours = jQuery(this).prop("checked");
	const parent = jQuery(this).parent().parent();
	jQuery(parent).find(".hours-selector-item input").prop("checked", allHours);
});

jQuery(document).on("click", "#hour-checkbox", function () {
	const parent = jQuery(this).parent().parent();
	const allHours =
		jQuery(parent).find(".hours-selector-item input:checked").length ===
		jQuery(parent).find(".hours-selector-item input").length;
	jQuery("#hours-selector-all").prop("checked", allHours);
});
