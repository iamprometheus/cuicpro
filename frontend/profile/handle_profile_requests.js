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

	$.ajax({
		url: cuicpro.ajax_url,
		type: "POST",
		data: {
			action: "user_logged_in",
		},
		success: function (response) {
			if (response.success) {
				jQuery(".login-button").html("Cerrar sesión");
				jQuery(".login-button").attr(
					"href",
					"https://cuic.pro/wp-login.php?action=logout",
				);
			}
		},
		error: function (xhr, status, error) {
			console.error("Error:", error);
		},
	});

	$(".profile-menu div").on("click", function (e) {
		e.preventDefault();
		const menu = $(this).attr("id");
		const isActive = $(this).hasClass("active");

		if (isActive) {
			return;
		}
		$.ajax({
			url: cuicpro.ajax_url,
			type: "POST",
			data: {
				action: "handle_switch_menu",
				menu: menu,
			},
			success: function (response) {
				if (response.success) {
					jQuery(".user-data-container").html(response.data.html);
					jQuery(".profile-menu div").removeClass("active");
					jQuery(e.currentTarget).addClass("active");
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

	$(document).on("click", ".team-item", function (e) {
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
