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

if (!function_exists('MediaFileAlreadyExists')) {
	function MediaFileAlreadyExists($filename){
		global $wpdb;
		$filename = strtolower($filename);
		$query = "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_title = '$filename'";
		return ($wpdb->get_var($query)  > 0) ;
	}
}

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
	function render_divisions() {
			$divisions = DivisionsDatabase::get_divisions();
			$active_tournament = TournamentsDatabase::get_active_tournament();

			foreach ($divisions as $division) {
					$division_name = $division->division_name;
					$division_id = $division->division_id;
					$division_category = $division->division_category == 1 ? "Varonil" : ($division->division_category == 2 ? "Femenil" : "Mixto");
					$division_mode = $division->division_mode == 1 ? "5v5" : "7v7";
					echo "<div class='division-container' ".wp_interactivity_data_wp_context(array('isOpen' => false)).">
									<div data-wp-on--click='actions.toggleOpen' class='division-title'>
										<span>".$division_name." ".$division_category." ".$division_mode."</span>
									</div>
									<div data-wp-bind--hidden='!context.isOpen' class='teams-container'>"
											.render_teams_for_division($division_id)
									."</div>"
									.render_matches($division_id, $active_tournament) 
								."</div>";
			}
	}
}

if (!function_exists('render_teams_for_division')) {
	function render_teams_for_division($division_id) {
		$teams = TeamsDatabase::get_teams_by_division($division_id);

		$tddata = "";
		foreach ($teams as $team) {
			// check if image exists in db
			$logo = str_replace(" ", "-", $team->logo);
			if (!MediaFileAlreadyExists($logo)) {
				$logo = "india";
			}
			
			$team_name = $team->team_name;
			$tddata.="<div class='team-container'>
								<img src='".base_url()."$logo' width='50' height='50' />
								<p>"
									.$team_name
								."</p>
							</div>";
		}
		return $tddata;
	}
}

if (!function_exists('render_matches')) {
	function render_matches($division_id, $active_tournament) {
		// check if tournament is active
		if ($active_tournament == null) {
			return "";
		}
		// check if bracket exists
		$bracket = BracketsDatabase::get_bracket_by_division($division_id, $active_tournament->tournament_id); 
		if ($bracket == null) {
			return "";
		}
		// check if there's matches for this bracket
		$matches = PendingMatchesDatabase::get_matches_by_division($division_id, $active_tournament->tournament_id);
		if ($matches == null) {
			return "";
		}

		$matchcontainer = "<div class='division-matches' ".wp_interactivity_data_wp_context(array('isOpen' => false)).">
										<div data-wp-on--click='actions.toggleOpen' class='division-matches-title'>
											<span>Proximos partidos</span>
										</div>
										<div class='matches-container' data-wp-bind--hidden='!context.isOpen'>";
										
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


?>


<div
	<?php echo get_block_wrapper_attributes(); ?>
	data-wp-interactive="cuicpro"
	>
	<div class="divisions-container"
	>
		<?php
			render_divisions();
		?>
	</div>
	
</div>
