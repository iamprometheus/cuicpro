<?php

function my_enqueue_brackets_frontend_assets() {
  // Enqueue a JavaScript file
  wp_enqueue_script(
    'brackets-frontend', 
    plugins_url('/handle_brackets_fe_requests.js', __FILE__), // or use get_stylesheet_directory_uri() for child themes
    array('jquery'), // dependencies
    null, 
    true // load in footer
  );
  wp_enqueue_script('leader-line', plugin_dir_url(__FILE__) . 'leader-line.min.js');
	
  // Pass the AJAX URL to JavaScript
  wp_localize_script('brackets-frontend', 'cuicpro', array(
    'ajax_url' => admin_url('admin-ajax.php')
  ));
}

add_action('wp_enqueue_scripts', 'my_enqueue_brackets_frontend_assets', 9);

require_once __DIR__ . '/handle_brackets_fe_requests.php';