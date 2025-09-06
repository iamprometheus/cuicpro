jQuery(document).ready(function ($) {
	$(document).on("submit", "#contact-form", function (e) {
		e.preventDefault();

		const formData = new FormData(this);
		formData.append("action", "handle_contact_form");

		$.ajax({
			url: cuicpro.ajax_url,
			type: "POST",
			data: formData,
			processData: false,
			contentType: false,
			success: function (response) {
				if (response.success) {
					alert("Email enviado exitosamente");
					$("#contact-form")[0].reset();
				} else {
					alert(
						"Error al enviar email, si el problema persiste contacta al administrador.",
					);
				}
			},
			error: function (xhr, status, error) {
				console.error("Error:", error);
			},
		});
	});
});
