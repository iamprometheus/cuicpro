<?php
/**
 * Plugin Name:       CUICPRO
 * Description:       CUICPRO Extension for data management
 * Version:           0.1.0
 * Requires at least: 6.7
 * Requires PHP:      8.2
 * Author:            Aly Castro
 * License:           GPL-2.0-or-later
 * Text Domain:       cuicpro
 *
 * @package CUICPRO
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Registers the block using a `blocks-manifest.php` file, which improves the performance of block type registration.
 * Behind the scenes, it also registers all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://make.wordpress.org/core/2025/03/13/more-efficient-block-type-registration-in-6-8/
 * @see https://make.wordpress.org/core/2024/10/17/new-block-type-registration-apis-to-improve-performance-in-wordpress-6-7/
 */
// function cuicpro_cuicpro_block_block_init() {
// 	/**
// 	 * Registers the block(s) metadata from the `blocks-manifest.php` and registers the block type(s)
// 	 * based on the registered block metadata.
// 	 * Added in WordPress 6.8 to simplify the block metadata registration process added in WordPress 6.7.
// 	 *
// 	 * @see https://make.wordpress.org/core/2025/03/13/more-efficient-block-type-registration-in-6-8/
// 	 */
// 	if ( function_exists( 'wp_register_block_types_from_metadata_collection' ) ) {
// 		echo "<script>console.log('test 1');</script>";
// 		wp_register_block_types_from_metadata_collection( __DIR__ . '/build', __DIR__ . '/build/blocks-manifest.php' );
// 		return;
// 	}

// 	/**
// 	 * Registers the block(s) metadata from the `blocks-manifest.php` file.
// 	 * Added to WordPress 6.7 to improve the performance of block type registration.
// 	 *
// 	 * @see https://make.wordpress.org/core/2024/10/17/new-block-type-registration-apis-to-improve-performance-in-wordpress-6-7/
// 	 */
// 	if ( function_exists( 'wp_register_block_metadata_collection' ) ) {
// 		echo "<script>console.log('test 2');</script>";
// 		wp_register_block_metadata_collection( __DIR__ . '/build', __DIR__ . '/build/blocks-manifest.php' );
// 	}
// 	/**
// 	 * Registers the block type(s) in the `blocks-manifest.php` file.
// 	 *
// 	 * @see https://developer.wordpress.org/reference/functions/register_block_type/
// 	 */
// 	echo "<script>console.log('test 3');</script>";
// 	$manifest_data = require __DIR__ . '/build/blocks-manifest.php';
// 	foreach ( array_keys( $manifest_data ) as $block_type ) {
// 		register_block_type_from_metadata( __DIR__ . "/build/{$block_type}" );
// 	}
// }

function cuicpro_cuicpro_block_block_init() {
	$blocks = array(
		array( 'name' => 'cuicpro-leagues' ),
		array( 'name' => 'cuicpro-teams' )
	);

	foreach ( $blocks as $block ) {
		register_block_type_from_metadata(
			__DIR__ . '/build/' . $block['name']
		);
	}
}
add_action( 'init', 'cuicpro_cuicpro_block_block_init' );

// function load_jquery() {
// 	wp_enqueue_script('jquery');
// }
// add_action('wp_enqueue_scripts', 'load_jquery');

// require_once __DIR__ . '/model/leagues.php';
// require_once __DIR__ . '/model/teams.php';


// function enqueue_custom_scripts() {
// 	wp_enqueue_script(
// 			'custom-script',
// 			plugins_url('/custom_script.js', __FILE__),
// 			array('jquery'),
// 			null,
// 			true
// 	);

// 	// Pass the AJAX URL to JavaScript
// 	wp_localize_script('custom-script', 'cuicpro', array(
// 			'ajax_url' => admin_url('admin-ajax.php')
// 	));
// }
// add_action('wp_enqueue_scripts', 'enqueue_custom_scripts');

// function handle_event_callback() {
// 	if (!isset($_POST['event_type']) || !isset($_POST['block_id'])) {
// 			wp_send_json_error(['message' => 'Invalid request']);
// 	}

// 	$event_type = sanitize_text_field($_POST['event_type']);
// 	$block_id = sanitize_text_field($_POST['block_id']);

// 	$response = ['message' => 'Event received!', 'event_type' => $event_type, 'block_id' => $block_id];

// 	wp_send_json_success($response);
// }
// add_action('wp_ajax_handle_event', 'handle_event_callback');
// add_action('wp_ajax_nopriv_handle_event', 'handle_event_callback');