<?php

?>
<div class="matches-schedule-container">
  <div>
    <?php echo create_tournament_list_selector(); ?>
  </div>
  <div>
    <?php echo render_modifications_handler(); ?>
  </div>
  <div class="matches-schedule-parent">
    <span class="table-title">Horarios</span>
    <?php matches_schedule_viewer(); ?>
  </div>
  <dialog id="modify_matches_dialog">
    <div>
      <span>Conflicto detectado</span>
      <p></p>
      <button>Salir</button>
    </div>
  </dialog>
</div>