jQuery(document).ready(function ($) {
	$("#account-player").click(function () {
		$(".account-type-selection").addClass("hidden");
		$(".player-registration").removeClass("hidden");
	});

	$("#account-coach").click(function () {
		$(".account-type-selection").addClass("hidden");
		$(".coach-registration").removeClass("hidden");
	});

	$("#team-logo").change(function () {
		const file = this.files[0];
		const imagePreview = document.getElementById("logo-preview");
		if (file && file.type.startsWith("image/")) {
			const reader = new FileReader();

			reader.onload = function (e) {
				imagePreview.style.backgroundImage = `url(${e.target.result})`;
				imagePreview.innerHTML = ""; // Remove "Upload Image" text
			};

			reader.readAsDataURL(file);
		} else {
			alert("Por favor, sube una imagen valida.");
		}
	});

	$("#player-registration-form").submit(function (e) {
		e.preventDefault();
		console.log("Formulario enviado");
	});

	$(document).on("submit", "#coach-registration-form", function (e) {
		e.preventDefault();
		const formData = new FormData(this);
		formData.append("action", "handle_couch_account_selected");

		$.ajax({
			url: cuicpro.ajax_url,
			type: "POST",
			data: formData,
			processData: false,
			contentType: false,
			success: function (response) {
				if (response.success) {
					alert("Perfil completado exitosamente");
					jQuery("#cuicpro-profile").html(response.data.html);
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
});
