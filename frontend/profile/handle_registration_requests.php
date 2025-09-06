<?php

if (!function_exists('render_account_type_selection')) {
	function render_account_type_selection() {
	  $html = "";
  
	  $img_coach = 'https://cuic.pro/wp-content/uploads/2025/08/COACH-scaled.jpg';
	  $img_player = 'https://cuic.pro/wp-content/uploads/2025/09/PORRA-SKILLS-scaled.jpg';
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
	
		$img_coach = 'https://cuic.pro/wp-content/uploads/2025/08/COACH-scaled.jpg';
		$html .= "<div class='coach-registration hidden'>
					  <img src='{$img_coach}'/>
					  <form id='coach-registration-form'>
						  <span class='title'>¡Registra tu equipo!</span>
						  <div class='form-contact-group'>
							  <div class='form-contact-group-field'>
								  <label for='name'>Nombre</label>
								  <input name='name' type='text' placeholder='Nombre' required/>
							  </div>
							  <div class='form-contact-group-field'>
								  <label for='phone'>Telefono</label>
								  <input name='phone' type='text' placeholder='Telefono' required/>
							  </div>
						  </div>
						  <div class='form-contact-group'>
							  <div class='form-contact-group-field'>
								  <label for='city'>Ciudad</label>
								  <input name='city' type='text' placeholder='Ciudad' required/>
							  </div>
							  <div class='form-contact-group-field'>
								  <label for='state'>Estado</label>
								  <input name='state' type='text' placeholder='Estado' required/>
							  </div>
							  <div class='form-contact-group-field'>
								  <label for='country'>Pais</label>
								  <input name='country' type='text' placeholder='Pais' required/>
							  </div>
						  </div>
						  <div class='form-contact-group'>
							  <div class='form-contact-group-field'>
								  <label for='team-name'>Nombre del equipo</label>
								  <input name='team-name' type='text' placeholder='Nombre del equipo' required/>
							  </div>
							  <div class='form-contact-group-field-logo'>
								  <label for='team-logo' class='logo-preview' id='logo-preview'>Logo</label>
								  <input name='team-logo' type='file' placeholder='Logo' id='team-logo' accept='image/*' required/>
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
	
		$img_player = 'https://cuic.pro/wp-content/uploads/2025/09/PORRA-SKILLS-scaled.jpg';
		$html .= "<div class='player-registration hidden'>";
		$html .= "<img src='{$img_player}'/>
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

function handle_couch_account_selected() {
	if (!isset($_POST['name']) || !isset($_POST['phone']) || !isset($_POST['city']) || !isset($_POST['state']) || !isset($_POST['country'])) {
		wp_send_json_error(array('message' => 'Faltan datos'));
		return;
	}
	$user_name = sanitize_text_field($_POST['name']);
	$user_contact = sanitize_text_field($_POST['phone']);
	$user_city = sanitize_text_field($_POST['city']);
	$user_state = sanitize_text_field($_POST['state']);
	$user_country = sanitize_text_field($_POST['country']);

	$user_full_name = $user_name;
	$user = wp_get_current_user();

	$result = [false, null];

	$result = CoachesUserDatabase::insert_coach(
		$user->ID, 
		$user_full_name, 
		$user_contact, 
		$user_city, 
		$user_state, 
		$user_country
	);
	if ($result[0]) {
		$user->add_role('coach');
	}

	$team_name = sanitize_text_field($_POST['team-name']);
	$logo = $_FILES['team-logo'];
	// upload image to wordpress and get the attachment id to link to team
	$attachment_id = null;
	addImageToWordPressMediaLibrary($logo['tmp_name'], $logo['name'], $logo['name'], $attachment_id);

	$result2 = TeamsUserDatabase::insert_team($team_name, $user->ID, $attachment_id);
	
	if ($result2[0]) {
		wp_send_json_success(array('message' => 'Perfil guardado exitosamente!', 'html' => render_profile_menu()));
	} 
	wp_send_json_error(array('message' => 'Profile not saved'));
}

add_action('wp_ajax_handle_couch_account_selected', 'handle_couch_account_selected');
add_action('wp_ajax_nopriv_handle_couch_account_selected', 'handle_couch_account_selected');