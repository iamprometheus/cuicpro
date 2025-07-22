<?php

?>
<div>
  <div id='tabs'>
    <ul>
      <li><a href='#tabs-1'>Torneo</a></li>
      <li><a href='#tabs-2'>Divisiones</a></li>
      <li><a href='#tabs-3'>Coaches</a></li>
      <li><a href='#tabs-4'>Equipos</a></li>
      <li><a href='#tabs-5'>Equipos por Division</a></li>
      <li><a href='#tabs-6'>Oficiales</a></li>
      <li><a href='#tabs-7'>Brackets</a></li>
      <li><a href='#tabs-8'>Registros</a></li>
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
      <?php cuicpro_teams_by_division_viewer(); ?>
    </div>
    <div id='tabs-6'>
      <?php cuicpro_official_viewer(); ?>
    </div>
    <div id='tabs-7'>
      <?php cuicpro_brackets_viewer(); ?>
    </div>
    <div id='tabs-8'>
      <?php cuicpro_matches_viewer(); ?>
    </div>
  </div>
</div>