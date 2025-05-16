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

function cuicpro_init() {
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

// hooks up your code to initialize and register the blocks
add_action( 'init', 'cuicpro_init' ); 

// load jQuery
function load_jquery() {
	wp_enqueue_script('jquery');
}
add_action('wp_enqueue_scripts', 'load_jquery');

require_once __DIR__ . '/model/leagues.php';
require_once __DIR__ . '/model/teams.php';
require_once __DIR__ . '/model/coaches.php';
require_once __DIR__ . '/dashboard/leagues/leagues.php';
require_once __DIR__ . '/dashboard/teams/teams.php';
require_once __DIR__ . '/dashboard/coaches/coaches.php';

function cuicpro_admin_init() {
	LeaguesDatabase::init();
	TeamsDatabase::init();
	CoachesDatabase::init();
}
add_action('admin_menu', 'cuicpro_admin_init');

// Initialize database tables if they don't exist
// 


