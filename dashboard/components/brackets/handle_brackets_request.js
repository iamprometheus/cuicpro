jQuery(document).ready(function ($) {
	$("#brackets-dropdown").change(function () {
		const divisionID = $(this).val();

		if (divisionID !== "0") {
			$.ajax({
				type: "POST",
				url: cuicpro.ajax_url,
				data: {
					action: "fetch_bracket_data",
					bracket_id: divisionID,
				},
				success: function (response) {
					console.log(response);
					if (response.success) {
						const newElement = document.querySelector("#brackets-data");
						newElement.innerHTML = response.data.html;

						// new LeaderLine(
						// 	document.getElementById("start"),
						// 	document.getElementById("end"),
						// );
					}
				},
				error: function (xhr, status, error) {
					console.error("Error:", error);
				},
			});
		} else {
			const element = document.querySelector("#brackets-data");
			element.innerHTML = "";
		}
	});
});
