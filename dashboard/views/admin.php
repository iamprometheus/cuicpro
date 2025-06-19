<?php

function create_tournament_entry($selected, $tournament) {
  $html = "";
  $html .= "<div class='tournament-item' id='tournament-" . esc_attr($tournament->tournament_id) . "' $selected>";
  $html .= "<span class='tournament-item-name'>" . esc_html($tournament->tournament_name) . "</span>";
  $html .= "</div>";
  return $html;
}

function create_tournament_list() {
  $tournaments = TournamentsDatabase::get_active_tournaments();
  $html = "<div class='tournaments-list-container' id='tournaments-selector'>";
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
      $html .= create_tournament_entry($selected, $tournament);
    }
  }
  $html .= "</div>";
  return $html;
}

?>
<div>
  <div id='tabs'>
    <ul>
      <li><a href='#tabs-1'>Torneo</a></li>
      <li><a href='#tabs-2'>Divisiones</a></li>
      <li><a href='#tabs-3'>Coaches</a></li>
      <li><a href='#tabs-4'>Equipos</a></li>
      <li><a href='#tabs-5'>Oficiales</a></li>
      <li><a href='#tabs-6'>Brackets</a></li>
      <li><a href='#tabs-7'>Registros</a></li>
    </ul>
    <div id='tabs-1'>
      <?php cuicpro_tournament_viewer(); ?>
    </div>
    <div id='tabs-2'>
      <?php cuicpro_division_viewer(); ?>
    </div>
    <div id='tabs-3'>
      <?php cuicpro_coach_viewer(); ?>
    </div>
    <div id='tabs-4'>
      <?php cuicpro_teams_viewer(); ?>
    </div>
    <div id='tabs-5'>
      <?php cuicpro_official_viewer(); ?>
    </div>
    <div id='tabs-6'>
      <?php cuicpro_brackets_viewer(); ?>
    </div>
    <div id='tabs-7'>
      <?php cuicpro_matches_viewer(); ?>
    </div>
  </div>
</div>