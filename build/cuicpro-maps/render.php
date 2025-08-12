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

if (!function_exists('render_tournament_map_blocks')) {
	function render_tournament_map_blocks() {
		$tournaments = TournamentsDatabase::get_active_tournaments_frontend();
		if (empty($tournaments)) {
			echo apply_filters('the_content', "");
		}

		$blocks = "<!-- wp:group {\"layout\":{\"type\":\"constrained\"}} -->\n<div class=\"wp-block-group\">";
		foreach ($tournaments as $tournament) {
			if ($tournament->tournament_coordinates == "") {
				continue;
			}
			$blocks .= "<h2 style='text-align: center; padding: 20px'>" . $tournament->tournament_name . ", ubicación de los campos</h2>";
			$blocks .= "<div style='width: 100%'>
										<iframe width='100%' height='600' frameborder='0' scrolling='no' marginheight='0' marginwidth='0' src='https://maps.google.com/maps?width=100%25&amp;height=600&amp;hl=es&amp;q=" . $tournament->tournament_coordinates . "+(Deportiva%20Sur)&amp;t=k&amp;z=18&amp;ie=UTF8&amp;iwloc=B&amp;output=embed'>
										</iframe>
									</div>";
		}
		$blocks .= "</div>\n<!-- /wp:group -->";

		echo apply_filters('the_content', $blocks);
	}
}

?>

<div
	<?php echo get_block_wrapper_attributes(); ?>
	data-wp-interactive="cuicpro-maps"
	>
	<div class="divisions-container"
	>
		<?php
			echo render_tournament_map_blocks();
		?>
	</div>

</div>
