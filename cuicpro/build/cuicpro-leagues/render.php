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

if (!function_exists('render_leagues')) {
	function render_leagues() {
			global $wpdb;
			$leagues = $wpdb->get_results("SELECT * FROM wp_cuicpro_leagues");

			foreach ($leagues as $league) {
					$league_name = $league->league_name;
					$league_id = $league->league_id;
					echo "<div class='league-wrapper' data-wp-on--click='actions.toggleOpen' 
													".wp_interactivity_data_wp_context(array('isOpen' => false))."
											>
											<p>".$league_name."</p>
											<div class='teams-wrapper' data-wp-bind--hidden='!context.isOpen'>"
													.render_teams($league_id)
											."</div>"
									."</div>";
			}
	}
}

if (!function_exists('render_teams')) {
	function render_teams($league_id) {
		global $wpdb;
		$teams = $wpdb->get_results($wpdb->prepare("SELECT * FROM wp_cuicpro_teams WHERE league_id = %d", $league_id));

		$tddata = "";

		foreach ($teams as $team) {
			$team_name = $team->team_name;
			$tddata.="<div class='team-wrapper'>"
								."<img src='http://test.local/india/' width='50' height='50' />"
							."<p>"
								.$team_name
							."</p>"
							."</div>";
		}
		return $tddata;
	}
}
?>


<div
	<?php echo get_block_wrapper_attributes(); ?>
	data-wp-interactive="cuicpro"
>
	<div class="leagues-wrapper"
	>
		<?php
			render_leagues();
		?>
	</div>
	
</div>
