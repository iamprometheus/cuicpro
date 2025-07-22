<?php

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