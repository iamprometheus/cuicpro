<?php

function my_enqueue_frontend_assets() {
  // Enqueue a JavaScript file
  wp_enqueue_script(
    'form-handler', 
    plugins_url('/handle_requests.js', __FILE__), // or use get_stylesheet_directory_uri() for child themes
    array('jquery'), // dependencies
    null, 
    true // load in footer
  );
  
  // Pass the AJAX URL to JavaScript
  wp_localize_script('form-handler', 'cuicpro', array(
    'ajax_url' => admin_url('admin-ajax.php')
  ));
}

add_action('wp_enqueue_scripts', 'my_enqueue_frontend_assets', 9);

require_once __DIR__ . '/handle_requests.php';