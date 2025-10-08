<?php

$tournaments = TournamentsDatabase::get_active_tournaments();
$tournament_id = null;
if (!empty($tournaments)) {
  $tournament_id = $tournaments[0]->tournament_id;
}

?>

<div style="padding: 10px; display: flex; flex-direction: column; gap: 20px;" class="officials-register-container">
  <div>
    <?php echo create_tournament_list_selector(); ?>
  </div>
  <div id="pending-officials-container">
    <?php echo render_pending_officials_from_register($tournament_id); ?>
  </div>
  <div id="registered-officials-container">
    <?php echo render_registered_officials_table($tournament_id); ?>
  </div>
</div>