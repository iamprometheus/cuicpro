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
	'cuicpro-register'
);

if (!function_exists('MediaFileAlreadyExists')) {
	function MediaFileAlreadyExists($filename){
		global $wpdb;
		$filename = strtolower($filename);
		$query = "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_title = '$filename'";
		return ($wpdb->get_var($query)  > 0) ;
	}
}

if (!function_exists('base_url')) {
	function base_url($atRoot=FALSE, $atCore=FALSE, $parse=FALSE){
			if (isset($_SERVER['HTTP_HOST'])) {
					$http = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
					$hostname = $_SERVER['HTTP_HOST'];
					$dir =  str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
					
					$core = preg_split('@/@', str_replace($_SERVER['DOCUMENT_ROOT'], '', realpath(dirname(__FILE__))), -1, PREG_SPLIT_NO_EMPTY);
					$core = $core[0];
					
					$tmplt = $atRoot ? ($atCore ? "%s://%s/%s/" : "%s://%s/") : ($atCore ? "%s://%s/%s/" : "%s://%s%s");
					$end = $atRoot ? ($atCore ? $core : $hostname) : ($atCore ? $core : $dir);
					$base_url = sprintf( $tmplt, $http, $hostname, $end );
			}
			else $base_url = 'http://test.local/';
			
			if ($parse) {
					$base_url = parse_url($base_url);
					if (isset($base_url['path'])) if ($base_url['path'] == '/') $base_url['path'] = '';
			}
			
			return $base_url;
	}
}

if (!function_exists('render_tournaments_dropdown')) {
	function render_tournaments_dropdown($tournaments) {
		$html = "";
		$html .= "<option value=''>Selecciona un torneo</option>";
		foreach ($tournaments as $tournament) {
			$html .= "<option value='".$tournament->tournament_id."'>".$tournament->tournament_name."</option>";
		}
		return $html;
	}
}

if (!function_exists('render_register_form')) {
	function render_register_form() {
		if(!is_user_logged_in()) {
			return "<div class='register-container'>";
		}

		$tournaments = TournamentsDatabase::get_active_tournaments_not_started();
		return "<div class='register-container'>
							<h3>Datos del coach</h3>
							<form id='register-form' class='register-form'>
							<div class='register-form-group'>
								<label for='name'>Nombre:</label>
								<input name='name' class='form-input' type='text' placeholder='Nombre' required/>
							</div>
							<div class='register-form-group'>
								<label for='last_name'>Apellido:</label>
								<input name='last_name' class='form-input' type='text' placeholder='Apellido' required/>
							</div>
							<div class='register-form-group'>
								<label for='phone'>Telefono:</label>
								<input name='phone' class='form-input' type='text' placeholder='Telefono' required/>
							</div>
							<div class='register-form-group'>
								<label for='location'>Ubicacion:</label>
								<div class='multiple-fields-container'>
									<input name='city' class='form-input' type='text' placeholder='Ciudad' required/>
									<input name='state' class='form-input' type='text' placeholder='Estado' required/>
									<input name='country' class='form-input' type='text' placeholder='Pais' required/>
								</div>
							</div>
							<hr style='width: 100%; border: 1px solid;'/>
							<h3>Datos del equipo</h3>
							<div class='register-form-group'>
								<label for='team_name'>Nombre del equipo:</label>
								<input name='team_name' class='form-input' type='text' placeholder='Nombre del equipo' required/>
							</div>
							<div class='register-form-group'>
								<label for='logo'>Logo:</label>
								<div class='logo-container'>
									<input type='file' id='logo' name='logo' required/>
									<div class='logo-preview'>
										<img id='logo-preview' src='#' width='100' height='100' alt='logo' />
									</div>
								</div>
							</div>
							<div class='register-form-group'>
								<label for='tournament'>Torneo:</label>
								<select name='tournament' id='tournament-select' required>
									".render_tournaments_dropdown($tournaments)."
								</select>
							</div>
							<div class='register-form-group'>
								<label for='division'>Division:</label>
								<select name='division' id='division-select'>
									<option value=''>Selecciona una division</option>
								</select>
							</div>
							<hr style='width: 100%; border: 1px solid;'/>
							<h3>Jugadores</h3>
							<div id='players-container' class='players-container'>
								<div class='register-form-group-2'>
									<label for='player_name'>Jugadores:</label>
									<div class='multiple-fields-container-2'>
										<input id='player_name' class='form-input-2' type='text' placeholder='Nombre' required/>
										<input id='player_last_name' class='form-input-2' type='text' placeholder='Apellido' required/>
										<div class='photo-container'>
											<input type='file' id='player-logo' placeholder='Foto' required/>
											<img src='#' width='100' height='100' alt='logo' />
										</div>
										<div class='remove-player-placeholder'></div>
									</div>
								</div>
							</div>
							<div class='register-form-group-2'>
								<label></label>
								<div class='multiple-fields-container-2'>
									<button type='button' id='add-player' class='add-player'>Agregar jugador</button>
								</div>
							</div>
							<button type='submit'>Registrar equipo</button>
							</form>
						</div>";
	}
}

if (!function_exists('render_register_title')) {
	function render_register_title() {
		if (!is_user_logged_in()) return "";
		return "<h2 style='text-align: center;'>¡Registra tu equipo!</h2>";
	}
}

add_action('wp_enqueue_scripts', function() {
	wp_enqueue_script('jquery');
});

?>

<div
	<?php echo get_block_wrapper_attributes(); ?>
	data-wp-interactive="cuicpro-register"
	>
	<div class="register-team-container">
		<?php echo render_register_title(); ?>
		<?php
			echo render_register_form();
		?>
	</div>
	
</div>
