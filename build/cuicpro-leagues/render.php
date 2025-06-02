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

if (!function_exists('render_divisions')) {
	function render_divisions() {
			global $wpdb;
			$divisions = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}cuicpro_divisions");

			foreach ($divisions as $division) {
					$division_name = $division->division_name;
					$division_id = $division->division_id;
					echo "<div class='division-container' 
													".wp_interactivity_data_wp_context(array('isOpen' => false))."
											>
											<div data-wp-on--click='actions.toggleOpen'>
												<p>".$division_name."</p>
											</div>
											<div class='teams-container' data-wp-bind--hidden='!context.isOpen'>"
													.render_teams_for_division($division_id)
											."</div>"
									."</div>";
			}
	}
}

if (!function_exists('render_teams_for_division')) {
	function render_teams_for_division($division_id) {
		global $wpdb;
		$teams = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}cuicpro_teams WHERE division_id = %d", $division_id));

		$tddata = "";

		foreach ($teams as $team) {
			$team_name = $team->team_name;
			$logo = str_replace(" ", "-", $team->logo);
			$tddata.="<div class='team-container'>
								<img src='http://cuic.pro/$logo' width='50' height='50' />
								<p>"
									.$team_name
								."</p>
							</div>";
		}
		return $tddata;
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
