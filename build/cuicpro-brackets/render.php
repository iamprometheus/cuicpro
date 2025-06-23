<?php
/**
 * PHP file to use when rendering the block type on the server to show on the front end.
 *
 * The following variables are exposed to the file:
 *     $attributes (array): The block attributes.
 *     $content (string): The block default content.
 *     $block (WP_Block): The block instance.
 *
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */

// Adds the global state.
wp_interactivity_state(
	'cuicpro-brackets'
);

if (!function_exists('render_brackets_fe')) {
	function render_brackets_fe($active_tournament) {
		$html = "";
		if (!$active_tournament) {
			$html .= "<h3>No hay brackets para mostrar</h3>";
			return $html;
		}
		$brackets = BracketsDatabase::get_brackets_by_tournament($active_tournament->tournament_id);

		$html .= "<div class='brackets-list-container'>";
		if (empty($brackets)) {
			$html .= "<h3>No hay brackets para mostrar</h3>";
			$html .= "</div>";
			return $html;
		}
		foreach ($brackets as $bracket) {
			$division = DivisionsDatabase::get_division_by_id(intval($bracket->division_id));
			$mode = ModesDatabase::get_mode_by_id($division->division_mode);
			$category = CategoriesDatabase::get_category_by_id($division->division_category);
			
			$html .= 
			"<div class='brackets-list-item' ".wp_interactivity_data_wp_context(array('bracketId' => $bracket->bracket_id))." data-wp-on--click='actions.toggleBracket'>
				<span>" . $division->division_name . " " . $mode->mode_description. " " . $category->category_description . "</span>
			</div>";
		}
		$html .= "</div>";
		return $html;
	}
}

if (!function_exists('render_brackets_diagram_fe')) {
	function render_brackets_diagram_fe($active_tournament) {
		// $brackets = BracketsDatabase::get_brackets_by_tournament($active_tournament->tournament_id);
		// $hardcoded_match_positions =[
		// 	2 => [2, 1],
		// 	4 => [4,2,3,1],
		// 	8 => [8,4,6,2,7,3,5,1],
		// 	16 => [16,8,12,4,14,6,10,2,15,7,11,3,13,5,9,1],
		// 	32 => [32,16,24,8,28,12,20,4,31,15,23,7,27,13,19,5,30,14,22,6,26,10,25,3,29,9,11,1,33,17,21,18],
		// ];
		// $html = "<div id='brackets-data' class='brackets-diagrams-container'>";
		// foreach ($brackets as $bracket) {
		// 	$bracket_id = $bracket->bracket_id;
		// 	$matches = PendingMatchesDatabase::get_matches_by_bracket($bracket_id);

		// 	$bracket_rounds = array_unique(array_map(function($match) {
		// 		return $match->bracket_round;
		// 	}, $matches));

		// 	$html .= "<div class='bracket-container' 
		// 									data-wp-bind--hidden='!context.bracketsState.bracketId_" . $bracket_id . "' id='bracket_" . $bracket_id . "'
		// 									data-wp-run='callbacks.drawLines'
		// 									" . wp_interactivity_data_wp_context(array('bracketId' => $bracket_id)) .
		// 									">";
		// 	$total_rounds = count($bracket_rounds);
		// 	foreach ($bracket_rounds as $round) {
		// 		$html .= "<div id='round_" . $round . "' class='bracket-round'>";

		// 		if ($round != 0) {
		// 			foreach ($matches as $match) {
		// 				if ($match->bracket_round == $round) {
		// 					$line_required_up = !$match->team_id_1 ? "line-required-up" : "" ;
		// 					$line_required_down = !$match->team_id_2 ? "line-required-down" : "" ;

		// 					$html .= "<div id='match_" . $match->match_id . "' class='bracket-match-container $line_required_up $line_required_down'>";
		// 					$html .= create_bracket_match_fe($match);
		// 					$html .= "</div>";
		// 				}
		// 			}
		// 		} else {
		// 			$matches_this_round = [];
		// 			$maximum_matches_this_round = pow(2, $total_rounds - $round - 1);

		// 			$temp_matches = [];

		// 			foreach ($matches as $match) {
		// 				if ($match->bracket_round == $round) {
		// 					$matches_this_round[] = $match;
		// 				}
		// 			}

		// 			if ($maximum_matches_this_round == count($matches_this_round)) {
		// 				foreach ($matches_this_round as $match) {
		// 					$html .= "<div id='match_" . $match->match_id . "' class='bracket-match-container'>";
		// 					$html .= create_bracket_match_fe($match);
		// 					$html .= "</div>";
		// 				}
		// 			} else {
		// 				for ($i = 0; $i < $maximum_matches_this_round-1; $i++) {
		// 					$temp_matches[] = "<div id='match_null' class='bracket-match-container-empty'></div>";
		// 				}

		// 				$match_index = 0; 
		// 				for ($index = count($matches_this_round)-1; $index >= 0; $index--) {
		// 					$temp_matches[$hardcoded_match_positions[$maximum_matches_this_round][$index]-1] =  "<div id='match_" . $matches_this_round[$match_index]->match_id . "' class='bracket-match-container'>" . create_bracket_match_fe($matches_this_round[$match_index]) . "</div>";
		// 					$match_index++;
		// 				}

		// 				$html .= implode("", $temp_matches);
		// 			}
		// 		}
		// 		$html .= "</div>";
		// 	}

		// 	$html .= "</div>";
		// }
		// $html .= "</div>";
		// echo $html;
	}
}

if (!function_exists('create_bracket_match_fe')) {
	function create_bracket_match_fe($match) {
		$team_1_name = "TBD";
		$team_2_name = "TBD";

		if ($match->team_id_1) {
			$team_1_name = TeamsDatabase::get_team_by_id($match->team_id_1)->team_name;
		}

		if ($match->team_id_2) {
			$team_2_name = TeamsDatabase::get_team_by_id($match->team_id_2)->team_name;
		}

		$official = OfficialsDatabase::get_official_by_id($match->official_id);
		$official_name = "Asignacion Pendiente";
		if ($official) {
			$official_name = $official->official_name;
		}

		$match_time = $match->match_time . ":00";

		$html = "<div id='match_" . $match->match_id . "' class='bracket-match'>";
		$html .= "<span>" . $team_2_name . "</span>";
		$html .= "<span>VS</span>";
		$html .= "<span>" . $team_1_name . "</span>";
		$html .= "</div>";
		$html .= "<div class='match-data-container'>";
		$html .= "<div class='match-data'>";
		$html .= "<span>Fecha: " . $match->match_date . "</span>";
		$html .= "<span>Hora: " . $match_time . "</span>";
		$html .= "</div>";
		$html .= "<div class='match-data'>";
		$html .= "<span>Arbitro: " . $official_name . "</span>";
		$html .= "<span>Campo: " . $match->field_number . "</span>";
		$html .= "</div>";
		$html .= "</div>";
		return $html;
	}
}
$active_tournaments = TournamentsDatabase::get_active_tournaments();
$brackets_state = [];
$active_tournament = null;
if (empty($active_tournaments)) {
	$brackets_state = ["bracketId" => false];
}
else {
	$active_tournament = $active_tournaments[0];
	$brackets = BracketsDatabase::get_brackets($active_tournament->tournament_id);

	foreach ($brackets as $bracket) {
		$brackets_state["bracketId_".$bracket->bracket_id] = false;
	}
}

if (!function_exists('render_active_tournaments')) {
	function render_active_tournaments() {
		$tournaments = TournamentsDatabase::get_active_tournaments();
		$html = "<div class='tournaments-list-container' id='tournaments-selector'>";
		if (empty($tournaments)) {
			$html .= "<div class='tournament-item-header'>";
			$html .= "<span class='tournament-item-name'>No hay torneos activos</span>";
			$html .= "</div>";
		} else {
			foreach ($tournaments as $index => $tournament) {
				$selected = $index === 0 ? "selected" : "";
				$html .= "<div class='tournament-item' id='tournament-" . esc_attr($tournament->tournament_id) . "' $selected>";
				$html .= "<span class='tournament-item-name'>" . esc_html($tournament->tournament_name) . "</span>";
				$html .= "</div>";
			}
		}
		$html .= "</div>";
		echo $html;
	}
}

add_action('wp_enqueue_scripts', function() {
	wp_enqueue_script('jquery');
});

?>


<div
	<?php echo get_block_wrapper_attributes(); ?>
	data-wp-interactive="cuicpro-brackets"
	<?php echo wp_interactivity_data_wp_context(array("bracketsState" => $brackets_state)); ?>
	>
	
	<div>
		<h2 style="text-align: center; margin-bottom: 10px; margin-top: 0px;">Torneos</h2>
		<?php
			render_active_tournaments();
		?>
	</div>	
	<div class="brackets-list" >
		<?php echo render_brackets_fe($active_tournament); ?>
	</div>
	<div id="bracket-container" class="bracket-container"></div>
</div>
