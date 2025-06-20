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
	function cuicpro_teams_render_teams() {

		global $wpdb;
		$teams = $wpdb->get_results("SELECT * FROM wp_cuicpro_teams");

		$tddata = "";
		$base_url = "https://cuic.pro/";


		foreach ($teams as $team) {
			// check if image exists in db
			$logo = str_replace(" ", "-", $team->logo);
			// if (!MediaFileAlreadyExists($base_url.$logo)) {
			// 	$logo = "india";
			// }
			
			$team_name = $team->team_name;
			$tddata.="<div class='teams-grid-item'>
								<img src='".$base_url.$logo."' width='25' height='25' />
								<p>"
									.$team_name
								."</p>
							</div>";
		}
		echo $tddata;
	}
}
?>


<div
	<?php echo get_block_wrapper_attributes(); ?>
	data-wp-interactive="cuicpro-teams"
	>
	<div class="teams-grid-title">
			<h2>Equipos</h2>
		</div>
	<div class="teams-grid-container"
	>
		
		<?php
			cuicpro_teams_render_teams();
		?>
	</div>
	
</div>
