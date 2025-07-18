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
  $html = "<div class='tournaments-list-container' id='tournaments-selector-schedule'>";
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


?>
<div style="padding: 10px;">
  <div>
    <?php echo create_tournament_list_selector(); ?>
  </div>
  <div style="display: flex; flex-direction: column; align-items: center; gap: 20px;">
    <span style="font-weight: bold; font-size: 24px;">Horarios</span>
    <?php officials_schedule_viewer(); ?>
  </div>
</div>