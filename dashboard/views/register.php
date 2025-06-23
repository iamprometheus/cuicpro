<?php

function create_tournament_entry_selector($selected, $tournament) {
  $html = "";
  $html .= "<div class='tournament-item' id='tournament-" . esc_attr($tournament->tournament_id) . "' $selected>";
  $html .= "<span class='tournament-item-name'>" . esc_html($tournament->tournament_name) . "</span>";
  $html .= "</div>";
  return $html;
}

function create_tournament_list_selector() {
  $tournaments = TournamentsDatabase::get_active_tournaments();
  $html = "<div class='tournaments-list-container' id='tournaments-selector-register'>";
  if (empty($tournaments)) {
    $html .= "<div class='tournament-item-header'>";
    $html .= "<span class='tournament-item-name'>No hay torneos activos</span>";
    $html .= "</div>";
  } else {
    $html .= "<div class='tournament-item-header'>";
    $html .= "<span class='tournament-item-name'>Torneos Activos:</span>";
    $html .= "</div>";
    foreach ($tournaments as $index => $tournament) {
      $selected = $index === 0 ? "selected" : "";
      $html .= create_tournament_entry_selector($selected, $tournament);
    }
  }
  $html .= "</div>";
  return $html;
}

$tournaments = TournamentsDatabase::get_active_tournaments();
$tournament_id = null;
$division_id = null;
if (!empty($tournaments)) {
  $tournament_id = $tournaments[0]->tournament_id;
  $divisions = DivisionsDatabase::get_active_divisions_by_tournament($tournament_id);
  if (!empty($divisions)) {
    $division_id = $divisions[0]->division_id;
  }
}

?>
<div style="padding: 10px; display: flex; flex-direction: column; gap: 20px;">
  <div>
    <?php echo create_tournament_list_selector(); ?>
  </div>
  <div>
    <?php echo register_viewer(); ?>
  </div>
  <div id="register-container">
    <?php echo render_pending_teams_from_register($tournament_id, $division_id); ?>
  </div>
  <div id="registered-container">
    <?php echo render_registered_teams_table($division_id); ?>
  </div>
</div>