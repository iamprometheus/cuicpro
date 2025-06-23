<?php

function my_enqueue_tournament_frontend_assets() {
  // Enqueue a JavaScript file
  wp_enqueue_script(
    'tournaments-frontend', 
    plugins_url('/handle_tournaments_fe_requests.js', __FILE__), // or use get_stylesheet_directory_uri() for child themes
    array('jquery'), // dependencies
    null, 
    true // load in footer
  );
  
  // Pass the AJAX URL to JavaScript
  wp_localize_script('tournaments-frontend', 'cuicpro', array(
    'ajax_url' => admin_url('admin-ajax.php')
  ));
}

add_action('wp_enqueue_scripts', 'my_enqueue_tournament_frontend_assets', 9);

require_once __DIR__ . '/handle_tournaments_fe_requests.php';