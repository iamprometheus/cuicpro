<?php

function my_enqueue_profile_assets() {
  // Enqueue a JavaScript file
  wp_enqueue_script(
    'profile-handler', 
    plugins_url('/handle_profile_requests.js', __FILE__), // or use get_stylesheet_directory_uri() for child themes
    array('jquery'), // dependencies
    null, 
    true // load in footer
  );
  
  // Pass the AJAX URL to JavaScript
  wp_localize_script('profile-handler', 'cuicpro', array(
    'ajax_url' => admin_url('admin-ajax.php')
  ));
}

add_action('wp_enqueue_scripts', 'my_enqueue_profile_assets', 9);

require_once __DIR__ . '/handle_profile_requests.php';