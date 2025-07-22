<?php

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