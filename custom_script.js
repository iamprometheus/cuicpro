jQuery(document).ready(function ($) {
	$("#button_1").on("click", function () {
		$.ajax({
			url: cuicpro.ajax_url, // WordPress AJAX URL
			type: "POST",
			data: {
				action: "handle_event",
				event_type: "click",
				//block_id: $(this).attr("id"),
			},
			success: function (response) {
				console.log("Event handled:", response);
			},
			error: function (xhr, status, error) {
				console.error("Error:", error);
			},
		});
	});
});
