jQuery(document).on(
	"click",
	"#tournaments-selector-schedule .tournament-item",
	function () {
		const selectedTournament = jQuery(this);
		const tournamentID = selectedTournament[0].id.replace("tournament-", "");

		jQuery.ajax({
			type: "POST",
			url: cuicpro.ajax_url,
			data: {
				action: "switch_selected_tournament_matches_schedule",
				tournament_id: tournamentID,
			},
			success: function (response) {
				if (response.success) {
					selectedTournament
						.attr("selected", true)
						.siblings()
						.attr("selected", false);

					jQuery("#matches-schedule-container").html(response.data.schedule);
				}
			},
			error: function (xhr, status, error) {
				console.error("Error:", error);
			},
		});
	},
);

const matchesSwapped = new Set();

function dragstartHandler(ev) {
	ev.dataTransfer.setData("text/plain", ev.target.id);
}

function dragoverHandler(ev) {
	ev.preventDefault(); // Allow dropping
}

function dropHandler(ev) {
	ev.preventDefault();
	const data = ev.dataTransfer.getData("text/plain");
	const draggedElement = document.getElementById(data);
	const targetCell = ev.target.closest("td"); // Ensure target is a td

	// Check if both draggedElement and targetCell are valid
	if (draggedElement && targetCell && targetCell !== draggedElement) {
		// Swap the two elements
		const draggedParent = draggedElement.parentNode;
		targetCell.appendChild(draggedElement);
		draggedParent.appendChild(targetCell.children[0]);
		if (draggedElement.id !== "") matchesSwapped.add(draggedElement.id);

		if (targetCell.id !== "") matchesSwapped.add(targetCell.id);
	} else if (draggedElement && !targetCell) {
		// If empty, just move the dragged element
		targetCell.appendChild(draggedElement);

		if (draggedElement.id !== "") matchesSwapped.add(draggedElement.id);
	}
}

jQuery(document).on("click", "#modify_matches_button", function () {
	if (this.innerHTML !== "Cancelar") {
		this.innerHTML = "Cancelar";
		jQuery("#save_matches_button").prop("disabled", false);
		const cells = jQuery("[movable_element=true]");
		cells.attr("draggable", true);
	} else {
		this.innerHTML = "Modificar";
		jQuery("#save_matches_button").prop("disabled", true);
		const cells = jQuery("[movable_element=true]");
		cells.attr("draggable", false);
	}
});

jQuery(document).on("click", "#save_matches_button", function () {
	const modifiedMatches = Array.from(matchesSwapped);
	const matchesData = {};
	const affectedRows = {};
	let hasConflicts = false;
	const tournamentID = jQuery(
		"#tournaments-selector-schedule .tournament-item[selected]",
	)
		.attr("id")
		.replace("tournament-", "");

	// get the new schedule data from the table
	for (let index = 0; index < modifiedMatches.length; index++) {
		const matchHTML = modifiedMatches[index];
		const match = jQuery("#" + matchHTML);
		const matchContainer = match.parent();
		const matchRow = matchContainer.parent();

		const siblings = matchRow.children().toArray();

		const matchIndex = siblings.findIndex(
			(el) => el.children[0]?.id === matchHTML,
		);

		const fields = jQuery("#matches-schedule-thead").find("th").toArray();
		const matchFieldType = fields[matchIndex].getAttribute("data-field-type");
		const matchFieldNumber =
			fields[matchIndex].getAttribute("data-field-number");

		const matchTime = matchRow.find(".hour-cell").text().replace(":00", "");
		const matchDate = matchRow.attr("id").replace("day_", "");
		const matchID = matchHTML.replace("cell_", "");
		const matchType = match.attr("data-match-type");
		const matchPlayoffID = match.attr("data-playoff-id");
		const matchDivisionID = match.attr("data-division-id");
		const matchBracketMatch = match.attr("data-bracket-match");

		matchesData[matchID] = {
			match_id: matchID,
			match_date: matchDate,
			match_time: matchTime,
			match_field: matchFieldNumber,
			match_field_type: matchFieldType,
			match_type: matchType,
			playoff_id: matchPlayoffID,
			division_id: matchDivisionID,
			bracket_match: matchBracketMatch,
		};

		// create entry if it doesn't exist
		if (!affectedRows[matchRow.attr("id")]) {
			affectedRows[matchRow.attr("id")] = [];
		}
		if (!affectedRows[matchRow.attr("id")].includes(matchTime)) {
			affectedRows[matchRow.attr("id")].push(matchTime);
		}
	}

	// check if there is a conflict in the affected rows (multiple matches for a team in a specific hour)
	for (let index = 0; index < Object.keys(affectedRows).length; index++) {
		const row = Object.keys(affectedRows)[index];
		const hours = affectedRows[row];
		for (let j = 0; j < hours.length; j++) {
			const hour = hours[j];
			const matches = jQuery(`[id='${row}']`)
				.filter((index, el) => el.dataset.hour === hour)
				.children()
				.toArray();

			const teams = new Set();
			for (let k = 0; k < matches.length; k++) {
				const match = matches[k].children[0];
				if (!match || !match.getAttribute("id")) continue;
				const team1 = match.getAttribute("data-team-1-id");
				const team2 = match.getAttribute("data-team-2-id");
				if (!team1) continue;
				if (teams.has(team1)) {
					document.getElementById("modify_matches_dialog").showModal();
					jQuery("#modify_matches_dialog p").text(
						"Conflicto detectado: " +
							match.children[1].innerHTML.split(" vs ")[0] +
							" tiene mas de 1 partido en la hora " +
							hour,
					);
					hasConflicts = true;
					break;
				}
				if (!team2) continue;
				if (teams.has(team2)) {
					document.getElementById("modify_matches_dialog").showModal();
					jQuery("#modify_matches_dialog p").text(
						"Conflicto detectado: " +
							match.children[1].innerHTML.split(" vs ")[1] +
							" tiene mas de 1 partido en la hora " +
							hour,
					);
					hasConflicts = true;
					break;
				}

				teams.add(team1);
				teams.add(team2);
			}
		}
	}

	// check integrity of playoff matches
	for (let index = 0; index < Object.keys(matchesData).length; index++) {
		const matchID = Object.keys(matchesData)[index];
		const match = matchesData[matchID];
		if (match.match_type != 2) continue;
		const _matchPlayoffID = jQuery("#cell_" + matchID).attr("data-playoff-id");
		const _matchDivisionID = jQuery("#cell_" + matchID).attr(
			"data-division-id",
		);
		const playoffs = jQuery(
			"[data-playoff-id='" +
				_matchPlayoffID +
				"'][data-division-id='" +
				_matchDivisionID +
				"']",
		).toArray();

		for (let i = 0; i < playoffs.length; i++) {
			const match = playoffs[i];
			const matchLink1 = match.getAttribute("data-match-link-1");
			const matchLink2 = match.getAttribute("data-match-link-2");

			if (!matchLink1 || !matchLink2) continue;
			const matchDivision = match.getAttribute("data-division-id");
			const matchID = match.id.replace("cell_", "");
			const matchTime = jQuery(`#cell_${matchID}`)
				.parent()
				.parent()
				.data("hour");
			const matchDay = parseInt(
				jQuery(`#cell_${matchID}`)
					.parent()
					.parent()
					.attr("id")
					.replace("day_", ""),
			);
			if (matchLink1) {
				const previousMatch = jQuery(
					`[data-bracket-match=${matchLink1}][data-division-id=${matchDivision}]`,
				);

				// Get previous match day and time
				const previousMatchID = previousMatch.children()[0].innerHTML;
				const previousMatchTime = previousMatch.parent().parent().data("hour");
				const previousMatchDay = parseInt(
					previousMatch.parent().parent().attr("id").replace("day_", ""),
				);

				if (
					previousMatchDay > matchDay ||
					(previousMatchDay == matchDay && previousMatchTime >= matchTime)
				) {
					document.getElementById("modify_matches_dialog").showModal();
					jQuery("#modify_matches_dialog p").text(
						"Conflicto detectado: partido con " +
							match.children[0].innerHTML +
							" no puede ir antes o en la misma hora de partido " +
							previousMatchID,
					);
					hasConflicts = true;
				}
			}

			if (matchLink2) {
				const previousMatch = jQuery(
					`[data-bracket-match=${matchLink2}][data-division-id=${matchDivision}]`,
				);

				// Get previous match day and time
				const previousMatchID = previousMatch.children()[0].innerHTML;
				const previousMatchTime = previousMatch.parent().parent().data("hour");
				const previousMatchDay = parseInt(
					previousMatch.parent().parent().attr("id").replace("day_", ""),
				);

				if (
					previousMatchDay > matchDay ||
					(previousMatchDay == matchDay && previousMatchTime >= matchTime)
				) {
					document.getElementById("modify_matches_dialog").showModal();
					jQuery("#modify_matches_dialog p").text(
						"Conflicto detectado: partido con " +
							match.children[0].innerHTML +
							" no puede ir a la misma hora o antes de partido " +
							previousMatchID,
					);
					hasConflicts = true;
				}
			}
		}
	}

	if (hasConflicts) return;

	console.log(matchesData);

	jQuery.ajax({
		type: "POST",
		url: cuicpro.ajax_url,
		data: {
			action: "update_matches_schedule",
			modified_matches: matchesData,
			tournament_id: tournamentID,
		},
		success: function (response) {
			console.log(response.data);
			if (response.success) {
				jQuery("#save_matches_button").prop("disabled", true);
				jQuery("#modify_matches_button").html("Modificar");
				const cells = jQuery("[movable_element=true]");
				cells.attr("draggable", false);
				alert("Partidos actualizados correctamente");
				matchesSwapped.clear();
			} else {
				jQuery("#modify_matches_button").html("Modificar");
				alert(
					"Error al actualizar partidos, si el problema persiste contacte al administrador.",
				);
			}
		},
		error: function (xhr, status, error) {
			alert("Error al actualizar partidos, contacte al administrador.");
			console.error("Error:", error);
		},
	});
});

jQuery(document).on("click", "#modify_matches_dialog button", function () {
	document.getElementById("modify_matches_dialog").close();
});
