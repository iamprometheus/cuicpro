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
	'cuicpro-schedule'
);

if (!function_exists('render_team_selector')) {
	function render_team_selector() {
		$tournaments = TournamentsDatabase::get_tournaments_started();
		if (empty($tournaments)) {
			$html = "<div>
								<span>No hay torneos</span>
							</div>";
			return $html;
		}
		$html = "";
		$first_tournament = $tournaments[0];

		$html .= "<div class='selectors'>";

		$html .= "<select id='tournament-select' value='" . esc_attr($first_tournament->tournament_id) . "'>";
		foreach ($tournaments as $tournament) {
			$html .= "<option value='" . esc_attr($tournament->tournament_id) . "'>" . esc_html($tournament->tournament_name) . "</option>";
		}
		$html .= "</select>";

		$divisions_last_tournament = DivisionsDatabase::get_active_divisions_by_tournament($first_tournament->tournament_id);
		
		$html .= "<select id='division-select' value='" . esc_attr($divisions_last_tournament[0]->division_id) . "'>";
		foreach ($divisions_last_tournament as $division) {
			$html .= "<option value='" . esc_attr($division->division_id) . "'>" . esc_html($division->division_name) . "</option>";
		}
		$html .= "</select>";

		$teams = TeamsDatabase::get_enrolled_teams_by_division($divisions_last_tournament[0]->division_id);
		$html .= "<select id='team-select'>";
		$html .= "<option value=''>Selecciona un equipo</option>";
		foreach ($teams as $team) {
			$html .= "<option value='" . esc_attr($team->team_id) . "'>" . esc_html($team->team_name) . "</option>";
		}
		$html .= "</select>";

		$html .= "</div>";
		return $html;
	}
}

add_action('wp_enqueue_scripts', function() {
	wp_enqueue_script('jquery');
});

?>


<div
	<?php echo get_block_wrapper_attributes(); ?>
	data-wp-interactive="cuicpro-schedule"
	>
	<div class="schedules-container">
		<?php
			echo render_team_selector();
		?>
		<div id="team-schedules" class="team-schedules">
			
		</div>
	</div>
	
</div>
