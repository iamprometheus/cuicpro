jQuery(document).on("change", "#notifications-type", function () {
	const notificationID = jQuery(this).val();

	if (notificationID == 0) {
		jQuery("#notifications-message").val(
			"Ingresa el mensaje personalizado que deseas enviar.",
		);
		return;
	}

	jQuery.ajax({
		type: "POST",
		url: cuicpro.ajax_url,
		data: {
			action: "handle_switch_notification_template",
			notification_id: notificationID,
		},
		success: function (response) {
			if (response.success) {
				jQuery("#notifications-message").val(
					response.data.notification_message,
				);
			} else {
				alert(response.data.message);
			}
		},
		error: function (xhr, status, error) {
			console.error("Error:", error);
		},
	});
});

jQuery(document).on(
	"click",
	"#notifications-show-save-modal-button",
	function () {
		const templateID = jQuery("#notifications-type").val();
		if (templateID == 1 || templateID == 2) {
			alert("No puedes modificar esta plantilla de notificación.");
			return;
		}
		const dialog = document.getElementById("save-notifications-dialog");
		dialog.showModal();
	},
);

jQuery(document).on(
	"click",
	"#notifications-cancel-save-template-button",
	function () {
		const dialog = document.getElementById("save-notifications-dialog");
		dialog.close();
	},
);

jQuery(document).on(
	"click",
	"#notifications-save-template-button",
	function () {
		const dialog = document.getElementById("save-notifications-dialog");
		const templateTitle = jQuery("#notifications-template-title").val();
		const templateMessage = jQuery("#notifications-message").val();
		const templateID = jQuery("#notifications-type").val();

		if (templateTitle == "" || templateMessage == "") {
			alert("Por favor, completa todos los campos. (Titulo y mensaje)");
			return;
		}

		console.log(templateTitle, templateMessage, templateID);

		jQuery.ajax({
			type: "POST",
			url: cuicpro.ajax_url,
			data: {
				action: "handle_create_notification_template",
				notification_title: templateTitle,
				notification_message: templateMessage,
				notification_id: templateID,
			},
			success: function (response) {
				if (response.success) {
					// append option to select
					jQuery("#notifications-type").append(
						"<option value='" +
							response.data.notification_id +
							"'>" +
							templateTitle +
							"</option>",
					);
					jQuery("#notifications-type").val(response.data.notification_id);
					alert(response.data.message);
					dialog.close();
				} else {
					alert(response.data.message);
				}
			},
			error: function (xhr, status, error) {
				console.error("Error:", error);
			},
		});
	},
);

jQuery(document).on(
	"click",
	"#notifications-show-delete-modal-button",
	function () {
		const templateID = jQuery("#notifications-type").val();

		if (templateID == 1 || templateID == 2 || templateID == 0) {
			alert("No puedes eliminar esta plantilla de notificación.");
			return;
		}
		const dialog = document.getElementById("delete-notifications-dialog");
		dialog.showModal();
	},
);

jQuery(document).on(
	"click",
	"#notifications-cancel-delete-template-button",
	function () {
		const dialog = document.getElementById("delete-notifications-dialog");
		dialog.close();
	},
);

jQuery(document).on(
	"click",
	"#notifications-delete-template-button",
	function () {
		const dialog = document.getElementById("delete-notifications-dialog");
		const templateID = jQuery("#notifications-type").val();

		if (templateID == 1 || templateID == 2 || templateID == 0) {
			alert("No puedes eliminar esta plantilla de notificación.");
			return;
		}

		jQuery.ajax({
			type: "POST",
			url: cuicpro.ajax_url,
			data: {
				action: "handle_delete_notification_template",
				notification_id: templateID,
			},
			success: function (response) {
				if (response.success) {
					// delete entry of select
					jQuery(
						"#notifications-type option[value='" + templateID + "']",
					).remove();
					jQuery("#notifications-type").val(1);
					jQuery("#notifications-message").val(
						response.data.notification_message,
					);
					alert(response.data.message);
					dialog.close();
				} else {
					alert(response.data.message);
				}
			},
			error: function (xhr, status, error) {
				console.error("Error:", error);
			},
		});
	},
);

jQuery(document).on("click", "#notifications-send-button", function () {
	const dialog = document.getElementById("send-notifications-dialog");
	dialog.showModal();
});

jQuery(document).on("click", "#notifications-cancel-send-button", function () {
	const dialog = document.getElementById("send-notifications-dialog");
	dialog.close();
});

jQuery(document).on("click", "#notifications-confirm-send-button", function () {
	const dialog = document.getElementById("send-notifications-dialog");

	const user_type = jQuery("#notifications-user").val();
	const registered = jQuery("#notifications-registered").val();
	const tournament = jQuery("#notification-tournament").val();
	const type = jQuery("#notifications-type").val();
	const message = jQuery("#notifications-message").val();

	if (registered == 1 && !tournament) {
		alert("Por favor, selecciona un torneo.");
		return;
	}

	jQuery.ajax({
		type: "POST",
		url: cuicpro.ajax_url,
		data: {
			action: "handle_send_notification",
			user_type: user_type,
			registered: registered,
			tournament: tournament,
			type: type,
			message: message,
		},
		success: function (response) {
			console.log(response.data);
			if (response.success) {
				alert(response.data.message);
				dialog.close();
			} else {
				alert(response.data.message);
			}
		},
		error: function (xhr, status, error) {
			console.error("Error:", error);
		},
	});
});
