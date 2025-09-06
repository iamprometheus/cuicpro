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

if (!function_exists('render_account_type_selection')) {
  function render_account_type_selection() {
	$html = "";

	$img_coach = wp_get_attachment_image_url(318, 'full');
	$img_player = wp_get_attachment_image_url(317, 'full');
	$html .= "<div class='account-type-selection'>";
	$html .= "<div>
					<p id='account-coach'>Soy Coach</p>
					<img src='{$img_coach}'/>
				</div>";
	$html .= "<div>
					<p id='account-player'>Soy Jugador</p>
					<img src='{$img_player}'/>
				</div>";
	$html .= "</div>";

    return $html;
  }
}

if (!function_exists('render_coach_registration')) {
	function render_coach_registration() {
	  $html = "";
  
	  $img_coach = wp_get_attachment_image_url(318, 'full');
	  $html .= "<div class='coach-registration hidden'>
					<img src='{$img_coach}'/>
					<form id='coach-registration-form'>
					<span class='title'>¡Registra tu equipo!</span>
						<div class='form-contact-group'>
							<div class='form-contact-group-field'>
								<label for='name'>Nombre</label>
								<input type='text' placeholder='Nombre' required/>
							</div>
							<div class='form-contact-group-field'>
								<label for='phone'>Telefono</label>
								<input type='text' placeholder='Telefono' required/>
							</div>
						</div>
						<div class='form-contact-group'>
							<div class='form-contact-group-field'>
								<label for='city'>Ciudad</label>
								<input type='text' placeholder='Ciudad' required/>
							</div>
							<div class='form-contact-group-field'>
								<label for='state'>Estado</label>
								<input type='text' placeholder='Estado' required/>
							</div>
							<div class='form-contact-group-field'>
								<label for='country'>Pais</label>
								<input type='text' placeholder='Pais' required/>
							</div>
						</div>
						<div class='form-contact-group'>
							<div class='form-contact-group-field'>
								<label for='team-name'>Nombre del equipo</label>
								<input type='text' placeholder='Nombre del equipo' required/>
							</div>
							<div class='form-contact-group-field-logo'>
								<label for='team-logo' class='logo-preview' id='logo-preview'>Logo</label>
								<input type='file' placeholder='Logo' id='team-logo' accept='image/*' required/>
							</div>
						</div>
						<button type='submit'>Registrarse</button>
					</form>
				</div>";
  
	  return $html;
	}
}

if (!function_exists('render_player_registration')) {
	function render_player_registration() {
	  $html = "";

	  $current_year = date('Y');
	  $tournaments = TournamentsDatabase::get_active_tournaments_frontend();
  
	  $img_coach = wp_get_attachment_image_url(317, 'full');
	  $html .= "<div class='player-registration hidden'>";
	  $html .= "<img src='{$img_coach}'/>
				<form id='player-registration-form'>
					<span class='title'>Bienvenido a INTERFLAG {$current_year}</span>
					<div class='form-contact-group-field'>
						<label for='player-name'>Nombre</label>
						<input name='player-name' type='text' placeholder='Nombre' required/>
					</div>

					<div class='form-contact-group-field'>
						<label for='tournament-select'>Torneo</label>
						<select id='tournament-select' name='tournament-select'>
							<option value=''>Selecciona un torneo</option>";

	foreach ($tournaments as $tournament) {
		$html .= "			<option value='{$tournament->tournament_id}'>{$tournament->tournament_name}</option>";
	}
	$html .= "			</select>
					</div>

					<div class='form-contact-group-field'>
						<label for='division-select'>Division</label>
						<select id='division-select' name='division-select'>
							<option value=''>Selecciona una division</option>
						</select>
					</div>

					<div class='form-contact-group-field'>
						<label for='team-select'>Equipo</label>
						<select id='team-select' name='team-select'>
							<option value=''>Selecciona tu equipo</option>
						</select>
					</div>
					<button type='submit'>Unirme</button>
				</form>
			</div>";
  
	  return $html;
	}
  }

?>

<div
	<?php echo get_block_wrapper_attributes(); ?>
	data-wp-interactive="cuicpro"
	>
	<?php
		echo render_account_type_selection();
	?>
	<?php
		echo render_coach_registration();
	?>
	<?php
		echo render_player_registration();
	?>
</div>
