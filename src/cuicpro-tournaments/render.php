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

if (!function_exists('get_next_content')) {
  function get_next_content($tournament, $index) {
		$date = explode(",", $tournament->tournament_days);
		$has_5v5 = $tournament->tournament_fields_5v5 > 0;
		$has_7v7 = $tournament->tournament_fields_7v7 > 0;
		$categories = $tournament->tournament_categories;
		$rules = $tournament->tournament_rules;

		$location = strtoupper($tournament->tournament_address);
		$city = strtoupper($tournament->tournament_city);
		$country = strtoupper($tournament->tournament_country);

		// set modes
		$modes = ($has_5v5 && $has_7v7) ? "5V5 &amp; 7V7" : ($has_5v5 ? "5V5" : "7V7");

		// convert day to date
    $date_start = DateTime::createFromFormat('d/m/y', $date[0]);
		$date_end = DateTime::createFromFormat('d/m/y', $date[count($date) - 1]);

    // Set locale to Spanish
    $formatter = new IntlDateFormatter(
      'es_419',              // Spanish (Mexico)
      IntlDateFormatter::FULL,
      IntlDateFormatter::NONE,
      $date_start->getTimezone(),
      IntlDateFormatter::GREGORIAN,
      "d"                 // 'EEEE' gives full day name
    );

    // Set locale to Spanish
    $formatter2 = new IntlDateFormatter(
      'es_419',              // Spanish (Mexico)
      IntlDateFormatter::FULL,
      IntlDateFormatter::NONE,
      $date_end->getTimezone(),
      IntlDateFormatter::GREGORIAN,
      "d 'de' MMMM"                 // 'EEEE' gives full day name
    );

		$formatter3 = new IntlDateFormatter(
      'es_419',              // Spanish (Mexico)
      IntlDateFormatter::FULL,
      IntlDateFormatter::NONE,
      $date_start->getTimezone(),
      IntlDateFormatter::GREGORIAN,
      "d"                 // 'EEEE' gives full day name
    );

		$formatter4 = new IntlDateFormatter(
      'es_419',              // Spanish (Mexico)
      IntlDateFormatter::FULL,
      IntlDateFormatter::NONE,
      $date_end->getTimezone(),
      IntlDateFormatter::GREGORIAN,
      "MMM"                 // 'EEEE' gives full day name
    );

		$date_formatted_1 = esc_html(strtoupper($formatter->format($date_start) . " al " . $formatter2->format($date_end)));
		$date_formatted_2 = esc_html(strtoupper($formatter4 ->format($date_end) . ". " . $formatter3->format($date_start) . "-" . $formatter3->format($date_end)));

		global $wpdb;

		if ($index % 2 == 0) {
			$post = $wpdb->get_row( "SELECT post_content FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_type = 'wp_block' AND post_name = 'detalles-torneo'" );
			if ( !$post ) return "";
			
			$content = $post->post_content;
			$content = str_replace("#Locacion", $location, $content);
			$content = str_replace("#Ciudad", $city . ", " . $country, $content);
			$content = str_replace("#Ubicacion", $city, $content);
			$content = str_replace("#Dias_1", $date_formatted_1, $content);
			$content = str_replace("#Modos", $modes, $content);
			$content = str_replace("#Dias_2", $date_formatted_2, $content);
			$content = str_replace("#Reglamento", $rules, $content);
			$content = str_replace("#Categorias", $categories, $content);
			return $content;
		}

		$post = $wpdb->get_row( "SELECT post_content FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_type = 'wp_block' AND post_name = 'detalles-torneo-inverso'" );
			if ( !$post ) return "";
			
			$content = $post->post_content;
			$content = str_replace("#Locacion", $location, $content);
			$content = str_replace("#Ciudad", $city . ", " . $country, $content);
			$content = str_replace("#Ubicacion", $city, $content);
			$content = str_replace("#Dias_1", $date_formatted_1, $content);
			$content = str_replace("#Modos", $modes, $content);
			$content = str_replace("#Dias_2", $date_formatted_2, $content);
			$content = str_replace("#Reglamento", $rules, $content);
			$content = str_replace("#Categorias", $categories, $content);
			return $content;
	}
}

if (!function_exists('render_tournament_blocks')) {
	function render_tournament_blocks() {
		$tournaments = TournamentsDatabase::get_active_tournaments_not_started();

		$blocks = "<!-- wp:group {\"layout\":{\"type\":\"constrained\"}} -->\n<div class=\"wp-block-group\">";
		foreach ($tournaments as $index => $tournament) {
			$blocks .= get_next_content($tournament, $index);
		}
		$blocks .= "</div>\n<!-- /wp:group -->";

		echo apply_filters('the_content', $blocks);
	}
}

if (!function_exists('render_sections')) {
	function render_sections() {
		global $wpdb;

		$tournaments = TournamentsDatabase::get_active_tournaments_frontend();
		$post_title = empty($tournaments) ? 'tabla-de-secciones-parcial' : 'tabla-de-secciones-completa';

		// Example: Get all published post titles
		$post = $wpdb->get_row( "SELECT post_content FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_type = 'wp_block' AND post_name = '$post_title'" );
		if ( $post ) echo apply_filters('the_content', $post->post_content);
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
			echo render_tournament_blocks();
		?>
	</div>
	<?php echo render_sections(); ?>
	
</div>
