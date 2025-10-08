<?php

function render_pending_officials_from_register($tournament_id)
{
  if ($tournament_id === null) {
    $html = "";
    $html .= "<table id='pending-officials' border='1' align='center'>";
    $html .= "<caption>No hay arbitros en la fila de registro en este torneo.</caption>";
    $html .= "</table>";
    return $html;
  }

  $officials = OfficialsRegisterQueueDatabase::get_officials_by_tournament($tournament_id);
  $html = "";
  $html .= "<table id='pending-officials' border='1' align='center'>";
  if (empty($officials)) {
    $html .= "<caption>No hay arbitros en la fila de registro.</caption>";
    $html .= "</table>";
    return $html;
  }
  $html .= "<caption>Arbitros en la fila de registro:</caption>";
  $html .= "<thead>";
  $html .= "<tr>";
  $html .= "<th>Arbitro</th>";
  $html .= "<th>Contacto</th>";
  $html .= "<th>Ubicacion</th>";
  $html .= "<th>Acciones</th>";
  $html .= "</tr>";
  $html .= "</thead>";
  $html .= "<tbody>";
  foreach ($officials as $official2) {
    $official = OfficialsUserDatabase::get_official_by_id($official2->official_user_id);

    $html .= "<tr>";
    $html .= "<td>" . esc_html($official->user_name) . "</td>";
    $html .= "<td>" . esc_html($official->user_contact) . "</td>";
    $html .= "<td>" . esc_html($official->user_city) . ", " . esc_html($official->user_state) . ", " . esc_html($official->user_country) . "</td>";
    $html .= "<td>
                <div>
                  <button id='accept-official-button' data-record-id='" . esc_attr($official2->official_register_queue_id) . "'>Aceptar</button>
                  <button id='reject-official-button' data-record-id='" . esc_attr($official2->official_register_queue_id) . "'>Rechazar</button>
                </div>
              </td>";
    $html .= "</tr>";
  }
  $html .= "</tbody>";
  $html .= "</table>";
  return $html;
}

function render_registered_officials_table($tournament_id)
{
  if ($tournament_id === null) {
    $html = "";
    $html .= "<table id='registered-officials' border='1' align='center'>";
    $html .= "<caption>No hay arbitros registrados</caption>";
    $html .= "</table>";
    return $html;
  }
  $officials = OfficialsDatabase::get_officials_by_tournament($tournament_id);
  $html = "";
  $html .= "<table id='registered-officials' border='1' align='center'>";
  if (empty($officials)) {
    $html .= "<caption>No hay arbitros registrados</caption>";
    $html .= "</table>";
    return $html;
  }
  $html .= "<caption>Arbitros registrados:</caption>";
  $html .= "<thead>";
  $html .= "<tr>";
  $html .= "<th>Nombre</th>";
  $html .= "<th>Contacto</th>";
  $html .= "<th>Ubicacion</th>";
  $html .= "</tr>";
  $html .= "</thead>";
  $html .= "<tbody>";
  foreach ($officials as $official) {
    $official = OfficialsDatabase::get_official_by_id($official->official_id);

    $html .= "<tr>";
    $html .= "<td>" . esc_html($official->official_name) . "</td>";
    $html .= "<td>" . esc_html($official->official_contact) . "</td>";
    $html .= "<td>" . esc_html($official->official_city) . ", " . esc_html($official->official_state) . ", " . esc_html($official->official_country) . "</td>";
    $html .= "</tr>";
  }

  $html .= "</tbody>";
  $html .= "</table>";
  return $html;
}


// enqueue scripts related to this file
function enqueue_officials_register_scripts()
{
  wp_enqueue_style('officials-register-styles', plugins_url('/styles.css', __FILE__));
  wp_enqueue_script(
    'officials-register-script',
    plugins_url('/handle_officials_register_request.js', __FILE__),
    array('jquery'),
    null,
    true
  );

  // Pass the AJAX URL to JavaScript
  wp_localize_script('officials-register-script', 'cuicpro', array(
    'ajax_url' => admin_url('admin-ajax.php')
  ));
}
add_action('admin_enqueue_scripts', 'enqueue_officials_register_scripts');

require_once __DIR__ . '/handle_officials_register_request.php';
