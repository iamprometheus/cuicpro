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

if (!function_exists('render_contact_form')) {
  function render_contact_form() {

	$html = "";

	$html .= "<form id='contact-form' class='contact-form'>";
	
	$html .= "<div class='form-contact-group'>";
	$html .= "<div class='form-contact-group-field'>";
	$html .= "<label for='name'>Nombre:</label>";
	$html .= "<input name='name' class='form-input' type='text' value='' required/>";
	$html .= "</div>";

	$html .= "<div class='form-contact-group-field'>";
	$html .= "<label for='last_name'>Apellido:</label>";
	$html .= "<input name='last_name' class='form-input' type='text' value='' required/>";
	$html .= "</div>";
	$html .= "</div>";
	
	$html .= "<div class='form-contact-group-field'>";
	$html .= "<label for='tournament_name'>Nombre del torneo:</label>";
	$html .= "<input name='tournament_name' class='form-input' type='text' value='' required/>";
	$html .= "</div>";
	
	
	$html .= "<div class='form-contact-group'>";
	$html .= "<div class='form-contact-group-field'>";
	$html .= "<label for='tournament_date'>Fecha del torneo:</label>";
	$html .= "<input name='tournament_date' class='form-input' type='date' value='' required/>";
	$html .= "</div>";
	$html .= "<div class='form-contact-group-field'>";
	$html .= "<label for='tournament_location'>Ubicación:</label>";
	$html .= "<input name='tournament_location' class='form-input' type='text' value='' required/>";
	$html .= "</div>";
	$html .= "</div>";
	
	$html .= "<div class='form-contact-group-field'>";
	$html .= "<label for='tournament_contact'>Email:</label>";
	$html .= "<input name='tournament_contact' class='form-input' type='email' value='' required/>";
	$html .= "</div>";

	$html .= "<button type='submit'>Enviar</button>";
	$html .= "</form>";

    return $html;
  }
}

?>

<div
	<?php echo get_block_wrapper_attributes(); ?>
	data-wp-interactive="cuicpro"
	>
	<div class="contact-form-container"
	>
		<?php
			echo render_contact_form();
		?>
	</div>
	
</div>
