jQuery(document).ready(function ($) {
	$(document).on("submit", "#complete-profile-form", function (e) {
		e.preventDefault();

		const formData = new FormData(this);
		formData.append("action", "handle_complete_profile_form");

		$.ajax({
			url: cuicpro.ajax_url,
			type: "POST",
			data: formData,
			processData: false,
			contentType: false,
			success: function (response) {
				if (response.success) {
					alert("Perfil completado exitosamente");
					jQuery(".profile-container").html(response.data.html);
				} else {
					alert(
						"Error al completar perfil, si el problema persiste contacta al administrador.",
					);
				}
			},
			error: function (xhr, status, error) {
				console.error("Error:", error);
			},
		});
	});

	$(document).on("click", "#back-button", function () {
		const toScreen = $(this).data("screen");
		let teamID = $(this).data("team-id");
		if (!teamID) teamID = 0;
		$.ajax({
			url: cuicpro.ajax_url,
			type: "POST",
			data: {
				action: "handle_back_button",
				screen: toScreen,
				team_id: teamID,
			},
			success: function (response) {
				if (response.success) {
					$(".user-data-container").html(response.data.html);
				} else {
					alert(
						"Error al regresar, si el problema persiste contacta al administrador.",
					);
				}
			},
			error: function (xhr, status, error) {
				console.error("Error:", error);
			},
		});
	});

	$(document).on("submit", "#user-data-form", function (e) {
		e.preventDefault();
		const formData = new FormData(this);
		formData.append("action", "handle_update_profile");

		$.ajax({
			url: cuicpro.ajax_url,
			type: "POST",
			data: formData,
			processData: false,
			contentType: false,
			success: function (response) {
				if (response.success) {
					alert("Perfil actualizado exitosamente");
					$(".user-data-container").html(response.data.html);
				} else {
					alert(
						"Error al actualizar perfil, si el problema persiste contacta al administrador.",
					);
				}
			},
			error: function (xhr, status, error) {
				console.error("Error:", error);
			},
		});
	});

	$(document).on("submit", "#join-team-form", function (e) {
		e.preventDefault();
		const formData = new FormData(this);
		formData.append("action", "handle_join_team_form");

		$.ajax({
			url: cuicpro.ajax_url,
			type: "POST",
			data: formData,
			processData: false,
			contentType: false,
			success: function (response) {
				if (response.success) {
					alert("Equipo unido exitosamente");
					$(".user-data-container").html(response.data.html);
				} else {
					alert(
						"Error al unir equipo, si el problema persiste contacta al administrador.",
					);
				}
			},
			error: function (xhr, status, error) {
				console.error("Error:", error);
			},
		});
	});

	if (
		window.location.href.includes("mi-perfil") ||
		window.location.href.includes("sample-page")
	) {
		$.ajax({
			url: cuicpro.ajax_url,
			type: "POST",
			data: {
				action: "handle_user_logged_in",
			},
			success: function (response) {
				if (response.success) {
					jQuery(".profile-container").html(response.data.html);
				} else {
					window.location.replace("https://cuic.pro/wp-login.php");
				}
			},
			error: function (xhr, status, error) {
				console.error("Error:", error);
			},
		});
	}

	$.ajax({
		url: cuicpro.ajax_url,
		type: "POST",
		data: {
			action: "user_logged_in",
		},
		success: function (response) {
			if (response.success) {
				jQuery(".wp-block-navigation-link")[4].remove();
			}
		},
		error: function (xhr, status, error) {
			console.error("Error:", error);
		},
	});

	$(document).on("change", "#coach_dropdown_id", function (e) {
		const coachID = $(this).val();
		$.ajax({
			url: cuicpro.ajax_url,
			type: "POST",
			data: {
				action: "fetch_teams_by_coach",
				coach_id: coachID,
			},
			success: function (response) {
				if (response.success) {
					$("#team_dropdown_id").html(response.data.html);
				} else {
					alert(
						"Error al obtener equipos, si el problema persiste contacta al administrador.",
					);
				}
			},
			error: function (xhr, status, error) {
				console.error("Error:", error);
			},
		});
	});

	$(document).on("click", ".profile-menu div", function (e) {
		e.preventDefault();
		const menu = $(this).attr("id");
		const isActive = $(this).hasClass("active");

		if (isActive) return;

		$.ajax({
			url: cuicpro.ajax_url,
			type: "POST",
			data: {
				action: "handle_switch_menu",
				menu: menu,
			},
			success: function (response) {
				if (response.success) {
					$(".user-data-container").html(response.data.html);
					$(".profile-menu")
						.children()
						.each(function () {
							$(this).removeClass("active");
						});
					$(e.currentTarget).addClass("active");
				} else {
					alert(
						"Error al cambiar de menu, si el problema persiste contacta al administrador.",
					);
				}
			},
			error: function (xhr, status, error) {
				console.error("Error:", error);
			},
		});
	});

	$(document).on("click", "#player-data", function (e) {
		e.preventDefault();
		const playerID = $(this).data("player-id");
		$.ajax({
			url: cuicpro.ajax_url,
			type: "POST",
			data: {
				action: "handle_fetch_player_info",
				player_id: playerID,
			},
			success: function (response) {
				if (response.success) {
					$(".user-data-container").html(response.data.html);
				} else {
					alert(
						"Error al acceder a la informacion del jugador, si el problema persiste contacta al administrador.",
					);
				}
			},
			error: function (xhr, status, error) {
				console.error("Error:", error);
			},
		});
	});

	$(document).on("click", "#team-item", function (e) {
		e.preventDefault();
		const teamID = $(this).data("team-id");
		$.ajax({
			url: cuicpro.ajax_url,
			type: "POST",
			data: {
				action: "handle_fetch_team_info",
				team_id: teamID,
			},
			success: function (response) {
				if (response.success) {
					$(".user-data-container").html(response.data.html);
				} else {
					alert(
						"Error al acceder a la informacion del equipo, si el problema persiste contacta al administrador.",
					);
				}
			},
			error: function (xhr, status, error) {
				console.error("Error:", error);
			},
		});
	});

	$(document).on("click", "#team-item-matches", function (e) {
		e.preventDefault();
		const teamID = $(this).data("team-id");
		$.ajax({
			url: cuicpro.ajax_url,
			type: "POST",
			data: {
				action: "handle_fetch_matches_by_team",
				team_id: teamID,
			},
			success: function (response) {
				if (response.success) {
					$(".user-data-container").html(response.data.html);
				} else {
					alert(
						"Error al acceder a los partidos, si el problema persiste contacta al administrador.",
					);
				}
			},
			error: function (xhr, status, error) {
				console.error("Error:", error);
			},
		});
	});

	$(document).on("click", "#register-team-button", function (e) {
		e.preventDefault();
		$.ajax({
			url: cuicpro.ajax_url,
			type: "POST",
			data: {
				action: "handle_register_team",
			},
			success: function (response) {
				if (response.success) {
					$(".user-data-container").html(response.data.html);
				} else {
					alert(
						"Error al acceder a los torneos, si el problema persiste contacta al administrador del torneo.",
					);
				}
			},
			error: function (xhr, status, error) {
				console.error("Error:", error);
			},
		});
	});

	$(document).on("click", "#add-team-button", function (e) {
		e.preventDefault();
		$.ajax({
			url: cuicpro.ajax_url,
			type: "POST",
			data: {
				action: "handle_create_team_screen",
			},
			success: function (response) {
				if (response.success) {
					$(".user-data-container").html(response.data.html);
				} else {
					alert(
						"Error al acceder a la creacion de equipos, si el problema persiste contacta al administrador.",
					);
				}
			},
			error: function (xhr, status, error) {
				console.error("Error:", error);
			},
		});
	});

	$(document).on("click", "#add-player-button", function (e) {
		e.preventDefault();
		const teamID = $("#team-id").data("team-id");
		$.ajax({
			url: cuicpro.ajax_url,
			type: "POST",
			data: {
				action: "handle_add_player_screen",
				team_id: teamID,
			},
			success: function (response) {
				if (response.success) {
					$(".user-data-container").html(response.data.html);
				} else {
					alert(
						"Error al acceder a la creacion de jugadores, si el problema persiste contacta al administrador.",
					);
				}
			},
			error: function (xhr, status, error) {
				console.error("Error:", error);
			},
		});
	});

	$(document).on("click", "#delete-player-button", function (e) {
		e.preventDefault();
		const playerID = $(this).data("player-id");
		const teamID = $(this).data("team-id");
		$.ajax({
			url: cuicpro.ajax_url,
			type: "POST",
			data: {
				action: "handle_delete_player",
				player_id: playerID,
				team_id: teamID,
			},
			success: function (response) {
				if (response.success) {
					alert("Jugador eliminado exitosamente.");
					$(".user-data-container").html(response.data.html);
				} else {
					alert(
						"Error al eliminar jugador, si el problema persiste contacta al administrador.",
					);
				}
			},
			error: function (xhr, status, error) {
				console.error("Error:", error);
			},
		});
	});

	$(document).on("submit", "#create-team-form", function (e) {
		e.preventDefault();

		const formData = new FormData(this);
		formData.append("action", "handle_create_team");

		$.ajax({
			url: cuicpro.ajax_url,
			type: "POST",
			data: formData,
			processData: false,
			contentType: false,
			success: function (response) {
				if (response.success) {
					$(".user-data-container").html(response.data.html);
				} else {
					alert(
						"Error al crear equipo, si el problema persiste contacta al administrador.",
					);
				}
			},
			error: function (xhr, status, error) {
				console.error("Error:", error);
			},
		});
	});

	$(document).on("submit", "#add-player-form", function (e) {
		e.preventDefault();
		const teamID = $("#team-id-form").data("team-id");
		const formData = new FormData(this);
		formData.append("action", "handle_add_player");
		formData.append("team_id", teamID);

		$.ajax({
			url: cuicpro.ajax_url,
			type: "POST",
			data: formData,
			processData: false,
			contentType: false,
			success: function (response) {
				if (response.success) {
					alert("Jugador agregado exitosamente.");
					$(".user-data-container").html(response.data.html);
				} else {
					alert(
						"Error al agregar jugador, si el problema persiste contacta al administrador.",
					);
				}
			},
			error: function (xhr, status, error) {
				console.error("Error:", error);
			},
		});
	});

	$(document).on("submit", "#player-data-form", function (e) {
		e.preventDefault();
		const teamID = $("#back-button").data("team-id");
		const playerID = $("#player_id").val();
		const formData = new FormData(this);
		formData.append("action", "handle_update_player");
		formData.append("team_id", teamID);
		formData.append("player_id", playerID);

		$.ajax({
			url: cuicpro.ajax_url,
			type: "POST",
			data: formData,
			processData: false,
			contentType: false,
			success: function (response) {
				if (response.success) {
					alert("Jugador actualizado exitosamente.");
					$(".user-data-container").html(response.data.html);
				} else {
					alert(
						"Error al actualizar jugador, si el problema persiste contacta al administrador.",
					);
				}
			},
			error: function (xhr, status, error) {
				console.error("Error:", error);
			},
		});
	});

	$(document).on("click", "#join-tournament-button", function (e) {
		e.preventDefault();
		const tournamentID = $(this).data("tournament-id");

		$.ajax({
			url: cuicpro.ajax_url,
			type: "POST",
			data: {
				action: "handle_join_tournament_form",
				tournament_id: tournamentID,
			},
			success: function (response) {
				if (response.success) {
					$(".user-data-container").html(response.data.html);
				} else {
					alert(
						"Error al acceder a la unión al torneo, si el problema persiste contacta al administrador.",
					);
				}
			},
			error: function (xhr, status, error) {
				console.error("Error:", error);
			},
		});
	});

	$(document).on("submit", "#join-tournament-form", function (e) {
		e.preventDefault();
		const formData = new FormData(this);
		const tournamentID = $(this).data("tournament-id");
		formData.append("action", "handle_join_tournament");
		formData.append("tournament_id", tournamentID);

		$.ajax({
			url: cuicpro.ajax_url,
			type: "POST",
			data: formData,
			processData: false,
			contentType: false,
			success: function (response) {
				if (response.success) {
					alert(
						"Registro exitoso, accede a tu equipo desde el apartado de equipo para ver el estatus del registro en cualquier momento.",
					);
					$(".user-data-container").html(response.data.html);
				} else {
					alert(
						"Error al unirse al torneo, si el problema persiste contacta al administrador.",
					);
				}
			},
			error: function (xhr, status, error) {
				console.error("Error:", error);
			},
		});
	});

	$(document).on("change", "#logo", function () {
		readURL(this, "logo-preview");
	});

	function readURL(input, id) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();

			reader.onload = function (e) {
				jQuery("#" + id).attr("src", e.target.result);
			};

			reader.readAsDataURL(input.files[0]);
		}
	}

	function readURL2(input, element) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();

			reader.onload = function (e) {
				element.attr("src", e.target.result);
			};

			reader.readAsDataURL(input.files[0]);
		}
	}
});
