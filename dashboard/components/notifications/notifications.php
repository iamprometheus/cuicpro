<?php

function cuicpro_notifications_viewer()
{

  $tournaments = TournamentsDatabase::get_active_tournaments();

  $tournament_options = "";
  foreach ($tournaments as $tournament) {
    $tournament_options .= "<option value='$tournament->tournament_id'>" . esc_html($tournament->tournament_name) . "</option>";
  }

  $templates = NotificationsDatabase::get_notifications();

  $templates_options = "";
  foreach ($templates as $template) {
    $templates_options .= "<option value='$template->notification_id'>" . esc_html($template->notification_title) . "</option>";
  }

  $notification_message = $templates[0]->notification_message;

  $html = "";
  $html .= "<div id='cuicpro-notifications'>";
  $html .= "<h2>Notificaciones</h2>";
  $html .= "<div class='notifications-data-container'>
              <div>
                <span>Enviar recordatorio/aviso a:</span>
                <select id='notifications-user'>
                  <option value='0'>Todos</option>
                  <option value='1'>Coaches</option>
                  <option value='2'>Jugadores</option>
                  <option value='3'>Arbitros</option>
                </select>
              </div>

              <div>
                <span>Registrados en:</span>
                <select id='notifications-registered'>
                  <option value='1'>Torneo</option>
                  <option value='2'>Sitio</option>
                </select>
              </div>

              <div>
                <span>Torneo:</span>
                <select id='notification-tournament'>
                  $tournament_options
                </select>
              </div>

              <div>
                <span>Tipo de notificación:</span>
                <select id='notifications-type'>
                  $templates_options
                  <option value='0'>Otro</option>
                </select>
              </div>

              <div>
                <span>Mensaje:</span>
                <textarea id='notifications-message'>$notification_message</textarea>
              </div>

              <span class='notifications-note'>Nota: Las palabras dentro de corchetes [ ] se reemplazarán por datos del sistema, por ejemplo \"[tournament]\" se reemplazará por el nombre del torneo o \"[name]\" se reemplazará por el nombre del usuario registrado en el sitio.</span>

              <div class='notifications-buttons'>
                <button id='notifications-send-button'>Enviar</button>
                <button id='notifications-show-save-modal-button'>Guardar notificación</button>
                <button id='notifications-show-delete-modal-button'>Eliminar notificación</button>
              </div>
              </div>";
  $html .= "</div>";
  $html .= "<dialog id='send-notifications-dialog'>
              <div class='notifications-dialog'>
                <p>¿Estás seguro de enviar esta notificación?</p>
                <span>Se enviará la notificación a los usuarios que cumplan los criterios previamente seleccionados.</span>
                <div class='modal-buttons'>
                  <button id='notifications-confirm-send-button'>Enviar</button>
                  <button id='notifications-cancel-send-button'>Cancelar</button>
                </div>
              </div>
            </dialog>";

  $html .= "<dialog id='save-notifications-dialog'>
              <div class='notifications-dialog'>
                <p>¿Estás seguro de guardar esta notificación?</p>
                <span>Guardar la notificación te permitira seleccionarla en el selector de tipo de notificaciones de forma rápida.</span>
                <input type='text' id='notifications-template-title' placeholder='Titulo'>
                <div class='modal-buttons'>
                  <button id='notifications-save-template-button'>Guardar</button>
                  <button id='notifications-cancel-save-template-button'>Cancelar</button>
                </div>
              </div>
            </dialog>";

  $html .= "<dialog id='delete-notifications-dialog'>
              <div class='notifications-dialog'>
                <p>¿Estás seguro de eliminar esta notificación?</p>
                <span>Eliminar la notificación la eliminara de la lista de notificaciones, por lo que no podrás seleccionarla de forma rápida en el futuro.</span>
                <div class='modal-buttons'>
                  <button id='notifications-delete-template-button'>Eliminar</button>
                  <button id='notifications-cancel-delete-template-button'>Cancelar</button>
                </div>
              </div>
            </dialog>";

  echo $html;
}

// enqueue scripts related to this file
function enqueue_notifications_scripts()
{
  wp_enqueue_style('notifications-styles', plugins_url('/styles.css', __FILE__));
  wp_enqueue_script(
    'notifications-script',
    plugins_url('/handle_notifications_request.js', __FILE__),
    array('jquery'),
    null,
    true
  );

  // Pass the AJAX URL to JavaScript
  wp_localize_script('notifications-script', 'cuicpro', array(
    'ajax_url' => admin_url('admin-ajax.php')
  ));
}
add_action('admin_enqueue_scripts', 'enqueue_notifications_scripts');

require_once __DIR__ . '/handle_notifications_request.php';
