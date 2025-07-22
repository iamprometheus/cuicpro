jQuery(document).ready(function ($) {
	$("#brackets-dropdown").change(function () {
		const bracketID = $(this).val();

		$(".leader-line").remove();
		$("#leader-line-defs").remove();

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

						if (elements !== null) {
							for (let playoff in elements) {
								$("#playoff_" + playoff).off("scroll");
								for (let i = elements[playoff].length - 1; i > 0; i--) {
									for (let match in elements[playoff][i]) {
										for (
											let j = 0;
											j < elements[playoff][i][match].length;
											j++
										) {
											const line = new LeaderLine(
												document.getElementById(elements[playoff][i][match][j]),
												document.getElementById(match),
												{
													color: "#000000",
													size: 2,
													endPlug: "behind",
													endPlugSize: 2,
													path: "grid",
													endSocket: "left",
													startSocket: "right",
												},
											);
											$("#playoff_" + playoff).on("scroll", function () {
												line.position();
											});
										}
									}
								}
							}
						}
					} else {
						alert(response.data.message);
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

jQuery(document).on("click", "#brackets-reload-button", function () {
	const bracketID = jQuery("#brackets-dropdown").val();

	jQuery(".leader-line").remove();
	jQuery("#leader-line-defs").remove();

	if (bracketID !== "0") {
		jQuery.ajax({
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

					if (elements !== null) {
						for (let playoff in elements) {
							jQuery("#playoff_" + playoff).off("scroll");
							for (let i = elements[playoff].length - 1; i > 0; i--) {
								for (let match in elements[playoff][i]) {
									for (let j = 0; j < elements[playoff][i][match].length; j++) {
										const line = new LeaderLine(
											document.getElementById(elements[playoff][i][match][j]),
											document.getElementById(match),
											{
												color: "#000000",
												size: 2,
												endPlug: "behind",
												endPlugSize: 2,
												path: "grid",
												endSocket: "left",
												startSocket: "right",
											},
										);
										jQuery("#playoff_" + playoff).on("scroll", function () {
											line.position();
										});
									}
								}
							}
						}
					}
				}
			},
			error: function (xhr, status, error) {
				console.error("Error:", error);
			},
		});
	} else {
		jQuery("#brackets-data").html("");
	}
});

jQuery(document).on("change", "#team-winner", function () {
	const matchID = jQuery(this).data("match-id");
	const teamID = jQuery(this).val();
	const prevMatch = jQuery(this).data("prev-match");
	const teamNumber = jQuery(this).data("team-number");
	const bracket_id = jQuery("#brackets-dropdown").val();

	jQuery.ajax({
		type: "POST",
		url: cuicpro.ajax_url,
		data: {
			action: "update_match_winner_single_elimination",
			match_id: matchID,
			team_id: teamID,
			prev_match_id: prevMatch,
			team_number: teamNumber,
			bracket_id: bracket_id,
		},
		success: function (response) {
			console.log(response);
		},
		error: function (xhr, status, error) {
			console.error("Error:", error);
		},
	});
});

jQuery(document).on("click", "#save-match", function () {
	const matchID = jQuery(this).data("match-id");

	const matchWinner = jQuery("#team-winner-" + matchID).val();
	const team1Score = jQuery("#team-1-score-" + matchID).val();
	const team1ID = jQuery("#team-1-score-" + matchID).data("team-id");
	const team2Score = jQuery("#team-2-score-" + matchID).val();
	const team2ID = jQuery("#team-2-score-" + matchID).data("team-id");
	const team1ScoreInt = parseInt(team1Score);
	const team2ScoreInt = parseInt(team2Score);

	if (matchWinner === "-1") {
		alert("Debe seleccionar un ganador");
		return;
	}

	if (team1Score === "" || team2Score === "") {
		alert("Debe ingresar las anotaciones");
		return;
	}

	if (team1ScoreInt < 0 || team2ScoreInt < 0) {
		alert("Las anotaciones no pueden ser negativas");
		return;
	}

	if (team1Score === team2Score && matchWinner !== "0") {
		alert("Los equipos no pueden empatar si las anotaciones son iguales.");
		return;
	}

	if (team1Score !== team2Score && matchWinner === "0") {
		alert("Debe seleccionar un ganador.");
		return;
	}

	if (matchWinner === team1ID.toString() && team1ScoreInt <= team2ScoreInt) {
		alert(
			"Diferencia entre el ganador y la cantidad de anotaciones ingresadas.",
		);
		return;
	}

	if (matchWinner === team2ID.toString() && team1ScoreInt >= team2ScoreInt) {
		alert(
			"Diferencia entre el ganador y la cantidad de anotaciones ingresadas.",
		);
		return;
	}

	const confirmationBoxText =
		"¿Estas seguro de que el resultado es correcto? Al aceptar, el partido sera guardado y es posible que no pueda ser modificado. Cualquier modificacion a las anotaciones y el ganador no se veran reflejadas mientras este recuadro este activo.";

	const onResponse = function (response) {
		if (response.success) {
			jQuery("#match_" + matchID).remove();
		} else {
			alert(response.data.message);
		}
	};

	confirmateActionBox(
		this,
		confirmationBoxText,
		"update_match_winner_round_robin",
		{
			match_id: matchID,
			match_winner: matchWinner,
			team_1_score: team1Score,
			team_2_score: team2Score,
		},
		onResponse,
	);
});

jQuery(document).on("click", "#save-match-single-elimination", function () {
	const matchID = jQuery(this).data("match-id");

	const matchWinner = jQuery("#team-winner-" + matchID).val();
	const team1Score = jQuery("#team-1-score-" + matchID).val();
	const team1ID = jQuery("#team-1-score-" + matchID).data("team-id");
	const team2Score = jQuery("#team-2-score-" + matchID).val();
	const team2ID = jQuery("#team-2-score-" + matchID).data("team-id");
	const team1ScoreInt = parseInt(team1Score);
	const team2ScoreInt = parseInt(team2Score);

	if (matchWinner === "-1") {
		alert("Debe seleccionar un ganador");
		return;
	}

	if (team1Score === "" || team2Score === "") {
		alert("Debe ingresar las anotaciones");
		return;
	}

	if (team1ScoreInt < 0 || team2ScoreInt < 0) {
		alert("Las anotaciones no pueden ser negativas");
		return;
	}

	if (matchWinner === team1ID.toString() && team1ScoreInt < team2ScoreInt) {
		alert(
			"Diferencia entre el ganador y la cantidad de anotaciones ingresadas.",
		);
		return;
	}

	if (matchWinner === team2ID.toString() && team1ScoreInt > team2ScoreInt) {
		alert(
			"Diferencia entre el ganador y la cantidad de anotaciones ingresadas.",
		);
		return;
	}

	const confirmationBoxText =
		"¿Estas seguro de que el resultado es correcto? Al aceptar, el partido sera guardado y es posible que no pueda ser modificado.";

	const onResponse = function (response) {
		if (response.success) {
			jQuery("#match_playoff_" + matchID)
				.children(".match-data-end-data")
				.html(response.data.html);
		} else {
			alert(response.data.message);
		}
	};

	confirmateActionBox(
		this,
		confirmationBoxText,
		"update_match_winner_single_elimination",
		{
			match_id: matchID,
			match_winner: matchWinner,
			team_1_score: team1Score,
			team_2_score: team2Score,
		},
		onResponse,
	);
});

jQuery(document).on("change", "#match-official-select", function () {
	const matchID = jQuery(this).data("match-id");
	const officialID = jQuery(this).val();

	jQuery.ajax({
		type: "POST",
		url: cuicpro.ajax_url,
		data: {
			action: "switch_assigned_official",
			match_id: matchID,
			official_id: officialID,
		},
		success: function (response) {
			if (response.success) {
				alert(response.data.message);
			} else {
				alert(response.data.message);
			}
		},
		error: function (xhr, status, error) {
			console.error("Error:", error);
		},
	});
});
