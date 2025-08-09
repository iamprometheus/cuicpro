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

if (!function_exists('render_tournament_home_blocks')) {
	function render_tournament_home_blocks() {
		global $wpdb;

		$tournaments = TournamentsDatabase::get_active_tournaments();
		if (empty($tournaments)) {
			echo apply_filters('the_content', "");
		}

		// Example: Get all published post titles

		$blocks = "<!-- wp:group {\"layout\":{\"type\":\"constrained\"}} -->\n<div class=\"wp-block-group\">";
		foreach ($tournaments as $index => $tournament) {
			$location = match(strtolower($tournament->tournament_state)) {
				'chihuahua' => 'cuu',
				'cuu' => 'cuu',
				'madrid' => 'mad',
				'texas' => 'elp',
				'el paso' => 'elp',
				default => 'cuu',
			};

			$post_title = 'torneo-' . $location;
			if ($index % 2 == 1) {
				$post_title .= "-inverso";
			}

			$post = $wpdb->get_row( "SELECT post_content FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_type = 'wp_block' AND post_name = '$post_title'" );
			$blocks .= $post->post_content;
		}
		$blocks .= "</div>\n<!-- /wp:group -->";

		echo apply_filters('the_content', $blocks);
	}
}

?>

<div
	<?php echo get_block_wrapper_attributes(); ?>
	data-wp-interactive="cuicpro-home"
	>
	<div class="divisions-container"
	>
		<?php
			echo render_tournament_home_blocks();
		?>
	</div>
	
</div>
