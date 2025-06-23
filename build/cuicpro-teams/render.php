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
	'cuicpro-teams'
);

if (!function_exists('cuicpro_teams_render_teams')) {
	function cuicpro_teams_render_teams($active_tournament) {
		if ($active_tournament == null) {
			return;
		}

		$teams = TeamsDatabase::get_enrolled_teams_by_tournament($active_tournament->tournament_id);
		if (empty($teams)) {
			return "<span>No hay equipos inscritos</span>";
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
			$tddata.="<div class='teams-grid-item'>
								<img src='".$logo_url."' width='25' height='25' />
								<p>"
									.$team_name
								."</p>
							</div>";
		}
		return $tddata;
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
	data-wp-interactive="cuicpro-teams"
	>
	<div>
		<h2 style="text-align: center; margin-bottom: 10px; margin-top: 0px;">Torneos</h2>
		<?php
			render_active_tournaments();
		?>
	</div>	
	<div class="teams-grid-title">
			<h2>Equipos</h2>
		</div>
	<div class="teams-grid-container"
	>
		<?php
			echo cuicpro_teams_render_teams($active_tournament);
		?>
	</div>
	
</div>
