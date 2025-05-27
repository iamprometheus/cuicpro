jQuery(document).ready(function ($) {
	$("#brackets-dropdown").change(function () {
		const bracketID = $(this).val();

		$(".leader-line").remove();

		if (bracketID !== "0") {
			$.ajax({
				type: "POST",
				url: cuicpro.ajax_url,
				data: {
					action: "fetch_bracket_data",
					bracket_id: bracketID,
				},
				success: function (response) {
					if (response.success) {
						const newElement = document.querySelector("#brackets-data");
						newElement.innerHTML = response.data.html;

						const elements = response.data.elements;
						$("#brackets-data").off("scroll");

						for (let i = elements.length - 2; i >= 0; i--) {
							for (let j = 0; j < elements[i].length; j++) {
								if (elements[i][j] === null) {
									continue;
								}
								const linkElement = Math.floor(j / 2);
								const line = new LeaderLine(
									document.getElementById(elements[i][j]),
									document.getElementById(elements[i + 1][linkElement]),
									{
										color: "#f6931f",
										size: 2,
										endPlug: "behind",
										endPlugSize: 2,
										path: "grid",
									},
								);
								$("#brackets-data").on("scroll", function () {
									line.position();
								});
							}
						}
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
