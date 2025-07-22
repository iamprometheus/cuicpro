<?php

function create_tournament_entry($selected, $tournament) {
  $html = "";
  $html .= "<div class='tournament-item' id='tournament-" . esc_attr($tournament->tournament_id) . "' $selected>";
  $html .= "<span class='tournament-item-name'>" . esc_html($tournament->tournament_name) . "</span>";
  $html .= "</div>";
  return $html;
}

function create_tournament_list() {
  $tournaments = [];
  if (current_user_can('cuicpro_administrate_tournaments')) {
    $tournaments = TournamentsDatabase::get_active_tournaments();
  } else {
    $tournaments = TournamentsDatabase::get_active_tournaments_by_organizer(get_current_user_id());
  }
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

function create_tournament_list_selector() {
  $tournaments = [];
  if (current_user_can('cuicpro_administrate_tournaments')) {
    $tournaments = TournamentsDatabase::get_active_tournaments();
  } else {
    $tournaments = TournamentsDatabase::get_active_tournaments_by_organizer(get_current_user_id());
  }
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
      $html .= create_tournament_entry($selected, $tournament);
    }
  }
  $html .= "</div>";
  return $html;
}