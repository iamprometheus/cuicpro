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

		$content = "<!-- wp:group {\"layout\":{\"type\":\"constrained\"}} -->\n<div class=\"wp-block-group\"><!-- wp:columns -->\n<div class=\"wp-block-columns\"><!-- wp:column {\"verticalAlignment\":\"center\",\"style\":{\"spacing\":{\"padding\":{\"right\":\"0\",\"left\":\"0\"}}}} -->\n<div class=\"wp-block-column is-vertically-aligned-center\" style=\"padding-right:0;padding-left:0\"><!-- wp:group {\"layout\":{\"type\":\"constrained\"}} -->\n<div class=\"wp-block-group\"><!-- wp:paragraph {\"align\":\"center\",\"style\":{\"typography\":{\"fontStyle\":\"normal\",\"fontWeight\":\"1000\"},\"color\":{\"text\":\"#646464\"},\"elements\":{\"link\":{\"color\":{\"text\":\"#646464\"}}}},\"fontSize\":\"superbfont-xlarge\"} -->\n<p class=\"has-text-align-center has-text-color has-link-color has-superbfont-xlarge-font-size\" style=\"color:#646464;font-style:normal;font-weight:1000\">INTERFLAG</p>\n<!-- /wp:paragraph --></div>\n<!-- /wp:group -->\n\n<!-- wp:group {\"style\":{\"spacing\":{\"blockGap\":\"0\"},\"color\":{\"text\":\"#646464\"},\"elements\":{\"link\":{\"color\":{\"text\":\"#646464\"}}},\"typography\":{\"fontSize\":\"28px\"}},\"layout\":{\"type\":\"constrained\"}} -->\n<div class=\"wp-block-group has-text-color has-link-color\" style=\"color:#646464;font-size:28px\"><!-- wp:paragraph {\"align\":\"center\"} -->\n<p class=\"has-text-align-center\">Ubicacion</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:paragraph {\"align\":\"center\"} -->\n<p class=\"has-text-align-center\">Dias_2</p>\n<!-- /wp:paragraph --></div>\n<!-- /wp:group -->\n\n<!-- wp:group {\"layout\":{\"type\":\"constrained\"}} -->\n<div class=\"wp-block-group\"><!-- wp:buttons {\"layout\":{\"type\":\"flex\",\"justifyContent\":\"center\"}} -->\n<div class=\"wp-block-buttons\"><!-- wp:button {\"backgroundColor\":\"custom-color-3\",\"textColor\":\"custom-color-1\",\"className\":\"is-style-outline\",\"style\":{\"elements\":{\"link\":{\"color\":{\"text\":\"var:preset|color|custom-color-1\"}}},\"border\":{\"radius\":\"0px\"}}} -->\n<div class=\"is-style-outline wp-block-button\"><a class=\"wp-block-button__link has-custom-color-1-color has-custom-color-3-background-color has-text-color has-background has-link-color wp-element-button\" href=\"https://cuic.pro/mi-perfil/\" style=\"border-radius:0px\">Registrarme</a></div>\n<!-- /wp:button --></div>\n<!-- /wp:buttons --></div>\n<!-- /wp:group --></div>\n<!-- /wp:column -->\n\n<!-- wp:column {\"width\":\"50%\",\"style\":{\"spacing\":{\"blockGap\":\"0\"}}} -->\n<div class=\"wp-block-column\" style=\"flex-basis:50%\"><!-- wp:group {\"style\":{\"spacing\":{\"blockGap\":\"0\"}},\"layout\":{\"type\":\"constrained\"}} -->\n<div class=\"wp-block-group\"><!-- wp:group {\"style\":{\"spacing\":{\"padding\":{\"right\":\"var:preset|spacing|superbspacing-large\",\"left\":\"var:preset|spacing|superbspacing-large\",\"top\":\"var:preset|spacing|superbspacing-medium\",\"bottom\":\"var:preset|spacing|superbspacing-medium\"},\"blockGap\":\"var:preset|spacing|superbspacing-xsmall\"},\"elements\":{\"link\":{\"color\":{\"text\":\"var:preset|color|white\"}}},\"typography\":{\"fontSize\":\"26px\"}},\"backgroundColor\":\"custom-color-4\",\"textColor\":\"white\",\"layout\":{\"type\":\"flex\",\"orientation\":\"vertical\",\"justifyContent\":\"stretch\",\"verticalAlignment\":\"center\"}} -->\n<div class=\"wp-block-group has-white-color has-custom-color-4-background-color has-text-color has-background has-link-color\" style=\"padding-top:var(--wp--preset--spacing--superbspacing-medium);padding-right:var(--wp--preset--spacing--superbspacing-large);padding-bottom:var(--wp--preset--spacing--superbspacing-medium);padding-left:var(--wp--preset--spacing--superbspacing-large);font-size:26px\"><!-- wp:group {\"style\":{\"layout\":{\"selfStretch\":\"fit\",\"flexSize\":null}},\"layout\":{\"type\":\"flex\",\"flexWrap\":\"nowrap\",\"verticalAlignment\":\"center\",\"justifyContent\":\"left\"}} -->\n<div class=\"wp-block-group\"><!-- wp:image {\"id\":378,\"width\":\"100px\",\"height\":\"100px\",\"scale\":\"cover\",\"sizeSlug\":\"full\",\"linkDestination\":\"none\",\"style\":{\"color\":{\"duotone\":[\"#ffeb00\",\"#fff278\"]}}} -->\n<figure class=\"wp-block-image size-full is-resized\"><img src=\"https://cuic.pro/wp-content/uploads/2025/07/location-pin-alt-1-svgrepo-com.svg\" alt=\"\" class=\"wp-image-378\" style=\"object-fit:cover;width:100px;height:100px\"/></figure>\n<!-- /wp:image -->\n\n<!-- wp:group {\"style\":{\"spacing\":{\"blockGap\":\"var:preset|spacing|superbspacing-xxsmall\"}},\"layout\":{\"type\":\"flex\",\"orientation\":\"vertical\"}} -->\n<div class=\"wp-block-group\"><!-- wp:paragraph -->\n<p class=\"\">Locacion</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:paragraph {\"fontSize\":\"superbfont-medium\"} -->\n<p class=\"has-superbfont-medium-font-size\">Ciudad</p>\n<!-- /wp:paragraph --></div>\n<!-- /wp:group --></div>\n<!-- /wp:group -->\n\n<!-- wp:group {\"style\":{\"layout\":{\"selfStretch\":\"fit\",\"flexSize\":null}},\"layout\":{\"type\":\"flex\",\"flexWrap\":\"nowrap\",\"verticalAlignment\":\"center\",\"justifyContent\":\"left\"}} -->\n<div class=\"wp-block-group\"><!-- wp:image {\"id\":382,\"width\":\"100px\",\"height\":\"100px\",\"scale\":\"cover\",\"sizeSlug\":\"full\",\"linkDestination\":\"none\",\"style\":{\"color\":{\"duotone\":[\"#ffeb00\",\"#fff278\"]}}} -->\n<figure class=\"wp-block-image size-full is-resized\"><img src=\"https://cuic.pro/wp-content/uploads/2025/07/calendar-days-svgrepo-com.svg\" alt=\"\" class=\"wp-image-382\" style=\"object-fit:cover;width:100px;height:100px\"/></figure>\n<!-- /wp:image -->\n\n<!-- wp:group {\"style\":{\"spacing\":{\"blockGap\":\"var:preset|spacing|superbspacing-xxsmall\"}},\"layout\":{\"type\":\"flex\",\"orientation\":\"vertical\"}} -->\n<div class=\"wp-block-group\"><!-- wp:paragraph -->\n<p class=\"\">Fecha</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:paragraph {\"fontSize\":\"superbfont-medium\"} -->\n<p class=\"has-superbfont-medium-font-size\">Dias_1</p>\n<!-- /wp:paragraph --></div>\n<!-- /wp:group --></div>\n<!-- /wp:group -->\n\n<!-- wp:group {\"style\":{\"layout\":{\"selfStretch\":\"fit\",\"flexSize\":null}},\"layout\":{\"type\":\"flex\",\"flexWrap\":\"nowrap\",\"verticalAlignment\":\"center\",\"justifyContent\":\"left\"}} -->\n<div class=\"wp-block-group\"><!-- wp:image {\"id\":380,\"width\":\"100px\",\"height\":\"100px\",\"scale\":\"cover\",\"sizeSlug\":\"full\",\"linkDestination\":\"none\",\"style\":{\"color\":{\"duotone\":[\"#ffeb00\",\"#fff278\"]}}} -->\n<figure class=\"wp-block-image size-full is-resized\"><img src=\"https://cuic.pro/wp-content/uploads/2025/07/strategy-play-svgrepo-com.svg\" alt=\"\" class=\"wp-image-380\" style=\"object-fit:cover;width:100px;height:100px\"/></figure>\n<!-- /wp:image -->\n\n<!-- wp:group {\"style\":{\"spacing\":{\"blockGap\":\"var:preset|spacing|superbspacing-xxsmall\"}},\"layout\":{\"type\":\"flex\",\"orientation\":\"vertical\"}} -->\n<div class=\"wp-block-group\"><!-- wp:paragraph -->\n<p class=\"\">MODALIDADES</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:paragraph {\"fontSize\":\"superbfont-medium\"} -->\n<p class=\"has-superbfont-medium-font-size\">Modos</p>\n<!-- /wp:paragraph --></div>\n<!-- /wp:group --></div>\n<!-- /wp:group -->\n\n<!-- wp:group {\"style\":{\"layout\":{\"selfStretch\":\"fit\",\"flexSize\":null}},\"layout\":{\"type\":\"flex\",\"flexWrap\":\"nowrap\",\"verticalAlignment\":\"center\",\"justifyContent\":\"left\"}} -->\n<div class=\"wp-block-group\"><!-- wp:image {\"id\":381,\"width\":\"100px\",\"height\":\"100px\",\"scale\":\"cover\",\"sizeSlug\":\"full\",\"linkDestination\":\"none\",\"style\":{\"color\":{\"duotone\":[\"#ffeb00\",\"#fff278\"]}}} -->\n<figure class=\"wp-block-image size-full is-resized\"><img src=\"https://cuic.pro/wp-content/uploads/2025/07/whistle-game-referee-svgrepo-com.svg\" alt=\"\" class=\"wp-image-381\" style=\"object-fit:cover;width:100px;height:100px\"/></figure>\n<!-- /wp:image -->\n\n<!-- wp:group {\"style\":{\"spacing\":{\"blockGap\":\"var:preset|spacing|superbspacing-xxsmall\"}},\"layout\":{\"type\":\"flex\",\"orientation\":\"vertical\"}} -->\n<div class=\"wp-block-group\"><!-- wp:paragraph -->\n<p class=\"\">REGLAMENTO</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:paragraph {\"fontSize\":\"superbfont-medium\"} -->\n<p class=\"has-superbfont-medium-font-size\">IFAF + PITCH / IFAF</p>\n<!-- /wp:paragraph --></div>\n<!-- /wp:group --></div>\n<!-- /wp:group -->\n\n<!-- wp:group {\"style\":{\"layout\":{\"selfStretch\":\"fit\",\"flexSize\":null}},\"layout\":{\"type\":\"flex\",\"flexWrap\":\"nowrap\",\"verticalAlignment\":\"center\",\"justifyContent\":\"left\"}} -->\n<div class=\"wp-block-group\"><!-- wp:image {\"id\":379,\"width\":\"100px\",\"height\":\"100px\",\"scale\":\"cover\",\"sizeSlug\":\"full\",\"linkDestination\":\"none\",\"style\":{\"color\":{\"duotone\":[\"#ffeb00\",\"#fff278\"]}}} -->\n<figure class=\"wp-block-image size-full is-resized\"><img src=\"https://cuic.pro/wp-content/uploads/2025/07/people-in-line-people-waiting-in-line-svgrepo-com.svg\" alt=\"\" class=\"wp-image-379\" style=\"object-fit:cover;width:100px;height:100px\"/></figure>\n<!-- /wp:image -->\n\n<!-- wp:group {\"style\":{\"spacing\":{\"blockGap\":\"var:preset|spacing|superbspacing-xxsmall\"}},\"layout\":{\"type\":\"flex\",\"orientation\":\"vertical\"}} -->\n<div class=\"wp-block-group\"><!-- wp:paragraph -->\n<p class=\"\">CATEGORÍAS</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:paragraph {\"fontSize\":\"superbfont-medium\"} -->\n<p class=\"has-superbfont-medium-font-size\">ADULTOS &amp; YOUTH</p>\n<!-- /wp:paragraph --></div>\n<!-- /wp:group --></div>\n<!-- /wp:group --></div>\n<!-- /wp:group --></div>\n<!-- /wp:group --></div>\n<!-- /wp:column --></div>\n<!-- /wp:columns --></div>\n<!-- /wp:group -->";
		$content = str_replace("Locacion", $location, $content);
		$content = str_replace("Ciudad", $city . ", " . $country, $content);
		$content = str_replace("Ubicacion", $city, $content);
		$content = str_replace("Dias_1", $date_formatted_1, $content);
		$content = str_replace("Modos", $modes, $content);
		$content = str_replace("Dias_2", $date_formatted_2, $content);

		$inverse_content = "<!-- wp:group {\"layout\":{\"type\":\"constrained\"}} -->\n<div class=\"wp-block-group\"><!-- wp:columns -->\n<div class=\"wp-block-columns\"><!-- wp:column {\"width\":\"50%\",\"style\":{\"spacing\":{\"blockGap\":\"0\"}}} -->\n<div class=\"wp-block-column\" style=\"flex-basis:50%\"><!-- wp:group {\"style\":{\"spacing\":{\"blockGap\":\"0\"}},\"layout\":{\"type\":\"constrained\"}} -->\n<div class=\"wp-block-group\"><!-- wp:group {\"style\":{\"spacing\":{\"padding\":{\"right\":\"var:preset|spacing|superbspacing-large\",\"left\":\"var:preset|spacing|superbspacing-large\",\"top\":\"var:preset|spacing|superbspacing-medium\",\"bottom\":\"var:preset|spacing|superbspacing-medium\"},\"blockGap\":\"var:preset|spacing|superbspacing-xsmall\"},\"elements\":{\"link\":{\"color\":{\"text\":\"var:preset|color|white\"}}},\"typography\":{\"fontSize\":\"26px\"}},\"backgroundColor\":\"custom-color-4\",\"textColor\":\"white\",\"layout\":{\"type\":\"flex\",\"orientation\":\"vertical\",\"justifyContent\":\"stretch\",\"verticalAlignment\":\"center\"}} -->\n<div class=\"wp-block-group has-white-color has-custom-color-4-background-color has-text-color has-background has-link-color\" style=\"padding-top:var(--wp--preset--spacing--superbspacing-medium);padding-right:var(--wp--preset--spacing--superbspacing-large);padding-bottom:var(--wp--preset--spacing--superbspacing-medium);padding-left:var(--wp--preset--spacing--superbspacing-large);font-size:26px\"><!-- wp:group {\"style\":{\"layout\":{\"selfStretch\":\"fit\",\"flexSize\":null}},\"layout\":{\"type\":\"flex\",\"flexWrap\":\"nowrap\",\"verticalAlignment\":\"center\",\"justifyContent\":\"left\"}} -->\n<div class=\"wp-block-group\"><!-- wp:image {\"id\":378,\"width\":\"100px\",\"height\":\"100px\",\"scale\":\"cover\",\"sizeSlug\":\"full\",\"linkDestination\":\"none\",\"style\":{\"color\":{\"duotone\":[\"#ffeb00\",\"#fff278\"]}}} -->\n<figure class=\"wp-block-image size-full is-resized\"><img src=\"https://cuic.pro/wp-content/uploads/2025/07/location-pin-alt-1-svgrepo-com.svg\" alt=\"\" class=\"wp-image-378\" style=\"object-fit:cover;width:100px;height:100px\"/></figure>\n<!-- /wp:image -->\n\n<!-- wp:group {\"style\":{\"spacing\":{\"blockGap\":\"var:preset|spacing|superbspacing-xxsmall\"}},\"layout\":{\"type\":\"flex\",\"orientation\":\"vertical\"}} -->\n<div class=\"wp-block-group\"><!-- wp:paragraph -->\n<p class=\"\">Locacion</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:paragraph {\"fontSize\":\"superbfont-medium\"} -->\n<p class=\"has-superbfont-medium-font-size\">Ciudad</p>\n<!-- /wp:paragraph --></div>\n<!-- /wp:group --></div>\n<!-- /wp:group -->\n\n<!-- wp:group {\"style\":{\"layout\":{\"selfStretch\":\"fit\",\"flexSize\":null}},\"layout\":{\"type\":\"flex\",\"flexWrap\":\"nowrap\",\"verticalAlignment\":\"center\",\"justifyContent\":\"left\"}} -->\n<div class=\"wp-block-group\"><!-- wp:image {\"id\":382,\"width\":\"100px\",\"height\":\"100px\",\"scale\":\"cover\",\"sizeSlug\":\"full\",\"linkDestination\":\"none\",\"style\":{\"color\":{\"duotone\":[\"#ffeb00\",\"#fff278\"]}}} -->\n<figure class=\"wp-block-image size-full is-resized\"><img src=\"https://cuic.pro/wp-content/uploads/2025/07/calendar-days-svgrepo-com.svg\" alt=\"\" class=\"wp-image-382\" style=\"object-fit:cover;width:100px;height:100px\"/></figure>\n<!-- /wp:image -->\n\n<!-- wp:group {\"style\":{\"spacing\":{\"blockGap\":\"var:preset|spacing|superbspacing-xxsmall\"}},\"layout\":{\"type\":\"flex\",\"orientation\":\"vertical\"}} -->\n<div class=\"wp-block-group\"><!-- wp:paragraph -->\n<p class=\"\">Fecha</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:paragraph {\"fontSize\":\"superbfont-medium\"} -->\n<p class=\"has-superbfont-medium-font-size\">Dias_1</p>\n<!-- /wp:paragraph --></div>\n<!-- /wp:group --></div>\n<!-- /wp:group -->\n\n<!-- wp:group {\"style\":{\"layout\":{\"selfStretch\":\"fit\",\"flexSize\":null}},\"layout\":{\"type\":\"flex\",\"flexWrap\":\"nowrap\",\"verticalAlignment\":\"center\",\"justifyContent\":\"left\"}} -->\n<div class=\"wp-block-group\"><!-- wp:image {\"id\":380,\"width\":\"100px\",\"height\":\"100px\",\"scale\":\"cover\",\"sizeSlug\":\"full\",\"linkDestination\":\"none\",\"style\":{\"color\":{\"duotone\":[\"#ffeb00\",\"#fff278\"]}}} -->\n<figure class=\"wp-block-image size-full is-resized\"><img src=\"https://cuic.pro/wp-content/uploads/2025/07/strategy-play-svgrepo-com.svg\" alt=\"\" class=\"wp-image-380\" style=\"object-fit:cover;width:100px;height:100px\"/></figure>\n<!-- /wp:image -->\n\n<!-- wp:group {\"style\":{\"spacing\":{\"blockGap\":\"var:preset|spacing|superbspacing-xxsmall\"}},\"layout\":{\"type\":\"flex\",\"orientation\":\"vertical\"}} -->\n<div class=\"wp-block-group\"><!-- wp:paragraph -->\n<p class=\"\">MODALIDADES</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:paragraph {\"fontSize\":\"superbfont-medium\"} -->\n<p class=\"has-superbfont-medium-font-size\">Modos</p>\n<!-- /wp:paragraph --></div>\n<!-- /wp:group --></div>\n<!-- /wp:group -->\n\n<!-- wp:group {\"style\":{\"layout\":{\"selfStretch\":\"fit\",\"flexSize\":null}},\"layout\":{\"type\":\"flex\",\"flexWrap\":\"nowrap\",\"verticalAlignment\":\"center\",\"justifyContent\":\"left\"}} -->\n<div class=\"wp-block-group\"><!-- wp:image {\"id\":381,\"width\":\"100px\",\"height\":\"100px\",\"scale\":\"cover\",\"sizeSlug\":\"full\",\"linkDestination\":\"none\",\"style\":{\"color\":{\"duotone\":[\"#ffeb00\",\"#fff278\"]}}} -->\n<figure class=\"wp-block-image size-full is-resized\"><img src=\"https://cuic.pro/wp-content/uploads/2025/07/whistle-game-referee-svgrepo-com.svg\" alt=\"\" class=\"wp-image-381\" style=\"object-fit:cover;width:100px;height:100px\"/></figure>\n<!-- /wp:image -->\n\n<!-- wp:group {\"style\":{\"spacing\":{\"blockGap\":\"var:preset|spacing|superbspacing-xxsmall\"}},\"layout\":{\"type\":\"flex\",\"orientation\":\"vertical\"}} -->\n<div class=\"wp-block-group\"><!-- wp:paragraph -->\n<p class=\"\">REGLAMENTO</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:paragraph {\"fontSize\":\"superbfont-medium\"} -->\n<p class=\"has-superbfont-medium-font-size\">IFAF + PITCH / IFAF</p>\n<!-- /wp:paragraph --></div>\n<!-- /wp:group --></div>\n<!-- /wp:group -->\n\n<!-- wp:group {\"style\":{\"layout\":{\"selfStretch\":\"fit\",\"flexSize\":null}},\"layout\":{\"type\":\"flex\",\"flexWrap\":\"nowrap\",\"verticalAlignment\":\"center\",\"justifyContent\":\"left\"}} -->\n<div class=\"wp-block-group\"><!-- wp:image {\"id\":379,\"width\":\"100px\",\"height\":\"100px\",\"scale\":\"cover\",\"sizeSlug\":\"full\",\"linkDestination\":\"none\",\"style\":{\"color\":{\"duotone\":[\"#ffeb00\",\"#fff278\"]}}} -->\n<figure class=\"wp-block-image size-full is-resized\"><img src=\"https://cuic.pro/wp-content/uploads/2025/07/people-in-line-people-waiting-in-line-svgrepo-com.svg\" alt=\"\" class=\"wp-image-379\" style=\"object-fit:cover;width:100px;height:100px\"/></figure>\n<!-- /wp:image -->\n\n<!-- wp:group {\"style\":{\"spacing\":{\"blockGap\":\"var:preset|spacing|superbspacing-xxsmall\"}},\"layout\":{\"type\":\"flex\",\"orientation\":\"vertical\"}} -->\n<div class=\"wp-block-group\"><!-- wp:paragraph -->\n<p class=\"\">CATEGORÍAS</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:paragraph {\"fontSize\":\"superbfont-medium\"} -->\n<p class=\"has-superbfont-medium-font-size\">ADULTOS &amp; YOUTH</p>\n<!-- /wp:paragraph --></div>\n<!-- /wp:group --></div>\n<!-- /wp:group --></div>\n<!-- /wp:group --></div>\n<!-- /wp:group --></div>\n<!-- /wp:column -->\n\n<!-- wp:column {\"verticalAlignment\":\"center\",\"width\":\"50%\",\"style\":{\"spacing\":{\"padding\":{\"right\":\"0\",\"left\":\"0\"}}},\"layout\":{\"type\":\"constrained\",\"justifyContent\":\"center\"}} -->\n<div class=\"wp-block-column is-vertically-aligned-center\" style=\"padding-right:0;padding-left:0;flex-basis:50%\"><!-- wp:group {\"layout\":{\"type\":\"constrained\"}} -->\n<div class=\"wp-block-group\"><!-- wp:paragraph {\"align\":\"center\",\"style\":{\"typography\":{\"fontStyle\":\"normal\",\"fontWeight\":\"1000\"},\"color\":{\"text\":\"#646464\"},\"elements\":{\"link\":{\"color\":{\"text\":\"#646464\"}}}},\"fontSize\":\"superbfont-xlarge\"} -->\n<p class=\"has-text-align-center has-text-color has-link-color has-superbfont-xlarge-font-size\" style=\"color:#646464;font-style:normal;font-weight:1000\">INTERFLAG</p>\n<!-- /wp:paragraph --></div>\n<!-- /wp:group -->\n\n<!-- wp:group {\"style\":{\"spacing\":{\"blockGap\":\"0\"},\"color\":{\"text\":\"#646464\"},\"elements\":{\"link\":{\"color\":{\"text\":\"#646464\"}}},\"typography\":{\"fontSize\":\"28px\"}},\"layout\":{\"type\":\"constrained\"}} -->\n<div class=\"wp-block-group has-text-color has-link-color\" style=\"color:#646464;font-size:28px\"><!-- wp:paragraph {\"align\":\"center\"} -->\n<p class=\"has-text-align-center\">Ubicacion</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:paragraph {\"align\":\"center\"} -->\n<p class=\"has-text-align-center\">Dias_2</p>\n<!-- /wp:paragraph --></div>\n<!-- /wp:group -->\n\n<!-- wp:group {\"layout\":{\"type\":\"constrained\"}} -->\n<div class=\"wp-block-group\"><!-- wp:buttons {\"layout\":{\"type\":\"flex\",\"justifyContent\":\"center\"}} -->\n<div class=\"wp-block-buttons\"><!-- wp:button {\"backgroundColor\":\"custom-color-3\",\"textColor\":\"custom-color-1\",\"className\":\"is-style-outline\",\"style\":{\"elements\":{\"link\":{\"color\":{\"text\":\"var:preset|color|custom-color-1\"}}},\"border\":{\"radius\":\"0px\"}}} -->\n<div class=\"is-style-outline wp-block-button\"><a class=\"wp-block-button__link has-custom-color-1-color has-custom-color-3-background-color has-text-color has-background has-link-color wp-element-button\" href=\"https://cuic.pro/mi-perfil/\" style=\"border-radius:0px\">Registrarme</a></div>\n<!-- /wp:button --></div>\n<!-- /wp:buttons --></div>\n<!-- /wp:group --></div>\n<!-- /wp:column --></div>\n<!-- /wp:columns --></div>\n<!-- /wp:group -->";
		$inverse_content = str_replace("Locacion", $location, $inverse_content);
		$inverse_content = str_replace("Ciudad", $city . ", " . $country, $inverse_content);
		$inverse_content = str_replace("Ubicacion", $city, $inverse_content);
		$inverse_content = str_replace("Dias_1", $date_formatted_1, $inverse_content);
		$inverse_content = str_replace("Modos", $modes, $inverse_content);
		$inverse_content = str_replace("Dias_2", $date_formatted_2, $inverse_content);
		
		if ($index % 2 == 1) {
			return $content;
		}
		
		return $inverse_content;
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

		$tournaments = TournamentsDatabase::get_active_tournaments();
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
