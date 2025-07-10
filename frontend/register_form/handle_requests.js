jQuery(document).ready(function ($) {
	$(document).on("submit", "#register-form", function (e) {
		e.preventDefault();

		const formData = new FormData(this);
		formData.append("action", "handle_register_form");

		let players_count = 0;
		$("#players-container")
			.find(".multiple-fields-container-2")
			.each(function () {
				const rawLogo = $(this).find("input[id='player-logo']");
				const logo = rawLogo[0].files[0];
				const name = $(this).find("input[id='player_name']").val();
				const last_name = $(this).find("input[id='player_last_name']").val();

				formData.append(`player_name_${players_count}`, name);
				formData.append(`player_last_name_${players_count}`, last_name);
				formData.append(`player_logo_${players_count}`, logo);
				players_count++;
			});

		formData.append("players_count", players_count);

		console.log(formData);

		$.ajax({
			url: cuicpro.ajax_url,
			type: "POST",
			data: formData,
			processData: false,
			contentType: false,
			success: function (response) {
				if (response.success) {
					$("#register-form")[0].reset();
					$("#logo-preview").attr("src", "#");
					$("#division-select").html(
						"<option value=''>Selecciona una division</option>",
					);
					$(".photo-container").find("img").attr("src", "#");
					alert("Equipo registrado exitosamente");
				} else {
					alert(
						"Error al registrar equipo, si el problema persiste contacta al administrador.",
					);
				}
			},
			error: function (xhr, status, error) {
				console.error("Error:", error);
			},
		});
	});

	$("#tournament-select").change(function () {
		const tournamentID = $(this).val();

		$.ajax({
			url: cuicpro.ajax_url,
			type: "POST",
			data: {
				action: "fetch_tournament_divisions",
				tournament_id: tournamentID,
			},
			success: function (response) {
				if (response.success) {
					$("#division-select").html(response.data.html);
				} else {
					console.log(response.data);
				}
			},
			error: function (xhr, status, error) {
				console.error("Error:", error);
			},
		});
	});

	$("#add-player").click(function () {
		$("#players-container").append(
			`<div class="register-form-group-2">
				<label for="player_name"></label>
				<div class="multiple-fields-container-2">
					<input id="player_name" class="form-input-2" type="text" placeholder="Nombre" required/>
					<input id="player_last_name" class="form-input-2" type="text" placeholder="Apellido" required/>
					<div class="photo-container">
						<input id="player-logo" type="file" placeholder="Foto" required/>
						<img src="#" width="100" height="100" alt="Foto" />
					</div>
					<button type="button" id="remove-player" class="remove-player">&times;</button>
				</div>
			</div>`,
		);
	});

	$(document).on("click", "#remove-player", function () {
		$(this).parent().parent().remove();
	});

	$("#logo").change(function () {
		readURL(this, "logo-preview");
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
});

jQuery(document).on("change", "#player-logo", function () {
	readURL2(this, jQuery(this).siblings("img"));
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
