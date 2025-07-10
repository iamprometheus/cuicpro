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
	'cuicpro-profile'
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

if (!function_exists('render_user_data')) {
	function render_user_data() {
		$user_id = get_current_user_id();
		$coach = CoachesUserDatabase::get_coach_by_id($user_id);

		$html = "<div class='user-data-container'>";
		$html .= "<h2 style='text-align: center;'>Mis Datos</h2>";
		$html .= "<form id='user-data-form' class='user-data-form'>
							<div class='form-group'>
								<label for='name'>ID:</label>
								<span class='form-input' id='coach_id'>" . $coach->coach_id . "</span>
							</div>
							<div class='form-group'>
								<label for='name'>Nombre:</label>
								<input name='name' class='form-input' type='text' value='" . $coach->coach_name . "' required/>
							</div>
							<div class='form-group'>
								<label for='phone'>Telefono:</label>
								<input name='phone' class='form-input' type='text' value='" . $coach->coach_contact . "' required/>
							</div>
							<div class='form-group'>
								<label for='location'>Ubicacion:</label>
								<div class='multiple-fields-container'>
									<input name='city' class='form-input' type='text' value='" . $coach->coach_city . "' required/>
									<input name='state' class='form-input' type='text' value='" . $coach->coach_state . "' required/>
									<input name='country' class='form-input' type='text' value='" . $coach->coach_country . "' required/>
								</div>
							</div>
							<button type='submit'>Guardar cambios</button>
						</form>
						</div>";
		return $html;
	}
}

if (!function_exists('render_profile_menu')) {
	function render_profile_menu() {
		$html = "";
		$html .= "<div class='profile-menu'>";
		$html .= "<div class='profile-menu-item active' id='profile'>
								<span>Mis Datos</span>
							</div>";
		$html .= "<div class='profile-menu-item' id='teams'>
								<span>Mis Equipos</span>
							</div>";
		$html .= "<div class='profile-menu-item' id='matches'>
								<span>Partidos</span>
							</div>";
		$html .= "<div class='profile-menu-item' id='tournaments'>
								<span>Torneos</span>
							</div>";
		$html .= "<div class='profile-menu-item' id='results'>
								<span>Resultados</span>
							</div>";
		$html .= "</div>";
		return $html;
	}
}

if (!function_exists('render_complete_profile_form')) {
	function render_complete_profile_form($has_profile) {
		if ($has_profile) {
			return render_profile_menu();
		}
		// profile completion form
		$html = "<h2 style='text-align: center;'>Completar perfil</h2>";
		$html .= "<div class='complete-profile-form-container'>";
		$html .= "<form id='complete-profile-form' class='complete-profile-form'>
							<div class='form-group'>
								<label for='name'>Nombre:</label>
								<input name='name' class='form-input' type='text' placeholder='Nombre' required/>
							</div>
							<div class='form-group'>
								<label for='last_name'>Apellido:</label>
								<input name='last_name' class='form-input' type='text' placeholder='Apellido' required/>
							</div>
							<div class='form-group'>
								<label for='phone'>Telefono:</label>
								<input name='phone' class='form-input' type='text' placeholder='Telefono' required/>
							</div>
							<div class='form-group'>
								<label for='location'>Ubicacion:</label>
								<div class='multiple-fields-container'>
									<input name='city' class='form-input' type='text' placeholder='Ciudad' required/>
									<input name='state' class='form-input' type='text' placeholder='Estado' required/>
									<input name='country' class='form-input' type='text' placeholder='Pais' required/>
								</div>
							</div>
							<button type='submit'>Guardar y continuar</button>
						</form>
						</div>";
		return $html;
	}
}

if (!function_exists('render_slot')) {
	function render_slot($has_profile) {
		if ($has_profile) {
			$html = "";
			$html .= "<div class='user-data-container'>";
			$html .= render_user_data();
			$html .= "</div>";
			return $html;
		}
		return "";
	}
}

add_action('wp_enqueue_scripts', function() {
	wp_enqueue_script('jquery');
});

if (!is_user_logged_in()) {
	wp_redirect(home_url('/login'));
	exit;
}

$user = wp_get_current_user();
$has_profile = CoachesUserDatabase::get_coach_by_id($user->ID);
?>

<div
	<?php echo get_block_wrapper_attributes(); ?>
	data-wp-interactive="cuicpro-profile"
	id="cuicpro-profile"
>
	<div class="profile-container">
		<?php
			echo render_complete_profile_form($has_profile);
		?>
		<?php
			echo render_slot($has_profile);
		?>
	</div>
</div>
