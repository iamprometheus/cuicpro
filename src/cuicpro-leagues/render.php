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

// Generates a unique id for aria-controls.
$unique_id = wp_unique_id( 'p-' ); 

// Adds the global state.
wp_interactivity_state(
	'cuicpro'
);

if (!function_exists('base_url')) {
	function base_url($atRoot=FALSE, $atCore=FALSE, $parse=FALSE){
			if (isset($_SERVER['HTTP_HOST'])) {
					$http = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
					$hostname = $_SERVER['HTTP_HOST'];
					$dir =  str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
					
					$core = preg_split('@/@', str_replace($_SERVER['DOCUMENT_ROOT'], '', realpath(dirname(__FILE__))), -1, PREG_SPLIT_NO_EMPTY);
					$core = $core[0];
					
					$tmplt = $atRoot ? ($atCore ? "%s://%s/%s/" : "%s://%s/") : ($atCore ? "%s://%s/%s/" : "%s://%s%s");
					$end = $atRoot ? ($atCore ? $core : $hostname) : ($atCore ? $core : $dir);
					$base_url = sprintf( $tmplt, $http, $hostname, $end );
			}
			else $base_url = 'http://test.local/';
			
			if ($parse) {
					$base_url = parse_url($base_url);
					if (isset($base_url['path'])) if ($base_url['path'] == '/') $base_url['path'] = '';
			}
			
			return $base_url;
	}
}

if (!function_exists('render_divisions')) {
	function render_divisions($active_tournament) {
			if ($active_tournament == null) {
				return;
			}
			$divisions = DivisionsDatabase::get_divisions_by_tournament($active_tournament->tournament_id);
			if (empty($divisions)) {
				return;
			}
			
			$html = "";
			foreach ($divisions as $division) {
					$division_name = $division->division_name;
					$division_id = $division->division_id;
					$division_category = $division->division_category == 1 ? "Varonil" : ($division->division_category == 2 ? "Femenil" : "Mixto");
					$division_mode = $division->division_mode == 1 ? "5v5" : "7v7";
					$html .= "<div class='division-container' >
									<div id='division-name' class='division-title'>
										<span>".$division_name." ".$division_category." ".$division_mode."</span>
									</div>
									<div hidden class='teams-container'>"
											.render_teams_for_division($division_id)
									."</div>"
									.render_matches($division_id) 
								."</div>";
			}
			return $html;
	}
}

if (!function_exists('render_teams_for_division')) {
	function render_teams_for_division($division_id) {
		$teams = TeamsDatabase::get_enrolled_teams_by_division($division_id);
		if (empty($teams)) {
			return "<span>No hay equipos inscritos en esta division</span>";
		}

		$tddata = "";
		foreach ($teams as $team) {
			// check if image exists in db
			$logo = $team->logo;
			$logo_url = wp_get_attachment_image_url($logo, 'full');
			if (!$logo_url) {
				$logo_url = base_url()."default_team_logo.png";
			}
			
			$team_name = $team->team_name;
			$tddata.="<div class='team-container'>
								<img src='$logo_url' width='50' height='50' />
								<p>"
									.$team_name
								."</p>
							</div>";
		}
		return $tddata;
	}
}

if (!function_exists('render_matches')) {
	function render_matches($division_id) {
		// check if bracket exists
		$bracket = BracketsDatabase::get_bracket_by_division_id($division_id); 
		if ($bracket == null) {
			return "";
		}
		// check if there's matches for this bracket
		$matches = PendingMatchesDatabase::get_matches_by_bracket($bracket->bracket_id);
		if ($matches == null) {
			return "";
		}

		$matchcontainer = "<div class='division-matches' id='division-matches'>
										<div class='division-matches-title'>
											<span>Proximos partidos</span>
										</div>
										<div class='matches-container' id='matches-container' hidden>";
										
		foreach ($matches as $match) {
			$matchcontainer .= "<div class='match-container'>
											".render_match($match)."
										</div>";
			}
										
		$matchcontainer .= "</div></div>";
		return $matchcontainer;
	}
}

if (!function_exists('render_match')) {
	function render_match($match) {
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
		
		$html = "<div class='match-teams'>";
		$html .= "<span>" . $team_2_name . "</span>";
		$html .= "<span>VS</span>";
		$html .= "<span>" . $team_1_name . "</span>";
		$html .= "</div>";
		$html .= "<div class='match-info'>";
		$html .= "<div class='match-date'>";
		$html .= "<span>Fecha: " . $match->match_date . "</span>";
		$html .= "<span>Hora: " . $match_time . "</span>";
		$html .= "</div>";
		$html .= "<div class='match-game'>";
		$html .= "<span>Arbitro: " . $official_name . "</span>";
		$html .= "<span>Campo: " . $match->field_number . "</span>";
		$html .= "</div>";
		$html .= "</div>";
		return $html;
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


$active_tournaments = TournamentsDatabase::get_active_tournaments();
$active_tournament = null;
if (!empty($active_tournaments)) {
	$active_tournament = $active_tournaments[0];
}


add_action('wp_enqueue_scripts', function() {
	wp_enqueue_script('jquery');
});

?>

<div
	<?php echo get_block_wrapper_attributes(); ?>
	data-wp-interactive="cuicpro"
	>
	<div>
		<h2 style="text-align: center; margin-bottom: 20px;">Torneos</h2>
		<?php
			render_active_tournaments();
		?>
	</div>	
	<div class="divisions-container"
	>
		<?php
			echo render_divisions($active_tournament);
		?>
	</div>
	
</div>
