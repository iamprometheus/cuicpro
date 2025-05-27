<?php
/**
 * Plugin Name:       CUICPRO
 * Description:       CUICPRO Extension for data management
 * Version:           0.8.0
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



require_once __DIR__ . '/model/base/teams.php';
require_once __DIR__ . '/model/base/coaches.php';
require_once __DIR__ . '/model/base/officials.php';
require_once __DIR__ . '/model/base/divisions.php';
require_once __DIR__ . '/model/base/tournaments.php';
require_once __DIR__ . '/model/base/modes.php';
require_once __DIR__ . '/model/base/categories.php';
require_once __DIR__ . '/model/base/tournament_hours.php';
require_once __DIR__ . '/model/matches.php';
require_once __DIR__ . '/model/brackets.php';
require_once __DIR__ . '/model/pending_matches.php';
require_once __DIR__ . '/model/tournament_scheduler.php';


require_once __DIR__ . '/dashboard/components/divisions/divisions.php';
require_once __DIR__ . '/dashboard/components/teams/teams.php';
require_once __DIR__ . '/dashboard/components/coaches/coaches.php';
require_once __DIR__ . '/dashboard/components/officials/officials.php';
require_once __DIR__ . '/dashboard/components/tournaments/tournaments.php';
require_once __DIR__ . '/dashboard/components/brackets/brackets.php';


// Initialize database tables if they don't exist
function cuicpro_databases() {
	ModesDatabase::init();
	CategoriesDatabase::init();
	CoachesDatabase::init();
	TournamentsDatabase::init();
	TournamentHoursDatabase::init();
	TeamsDatabase::init();
	OfficialsDatabase::init();
	DivisionsDatabase::init();
	MatchesDatabase::init();
	BracketsDatabase::init();	
	PendingMatchesDatabase::init();
}

add_action('admin_menu', 'cuicpro_databases');

if(!defined('WPINC')) {
	return;
}

add_action('admin_enqueue_scripts', 'load_jquery');
// load jQuery
function load_jquery() {
	// css
	wp_enqueue_style('jquery-ui', plugin_dir_url(__FILE__) . 'dashboard/css/jquery-ui.css');
	wp_enqueue_style('jquery-ui-multidatespicker', plugin_dir_url(__FILE__) . 'dashboard/css/mdp.css');
	wp_enqueue_style('prettify', plugin_dir_url(__FILE__) . 'dashboard/css/prettify.css');
	wp_enqueue_style('admin-page', plugin_dir_url(__FILE__) . 'dashboard/css/admin-page.css');

	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui');
	wp_enqueue_script('jquery-ui-tabs');
	wp_enqueue_script('jquery-ui-slider');
	wp_enqueue_script('jquery-ui-multidatespicker', plugin_dir_url(__FILE__) . 'dashboard/dependencies/jquery-ui.multidatespicker.js', array('jquery','jquery-ui-datepicker'));
	wp_enqueue_script('leader-line', plugin_dir_url(__FILE__) . 'dashboard/dependencies/leader-line.min.js');
	wp_enqueue_script('custom-script', plugin_dir_url(__FILE__) . 'dashboard/scripts/jQuery-ui-components.js', array('jquery'));
	
	// Pass the AJAX URL to JavaScript
	wp_localize_script('custom-script', 'cuicpro', array(
		'ajax_url' => admin_url('admin-ajax.php')
	));
}

// Add plugin to menu Page
add_action('admin_menu', 'cuicpro_menu_page');

function cuicpro_menu_page() {
	add_menu_page(
		'CUICPRO',
		'CUICPRO Admin',
		'manage_options',
		'cuicpro',
		'cuicpro_handle_admin_page',
		"dashicons-admin-multisite",
		3
	);

	add_submenu_page(
		'cuicpro',
		'Arbitros',
		'Arbitros',
		'manage_options',
		'cuicpro-officials',
		'cuicpro_handle_officials_page'
	);
}

function cuicpro_handle_admin_page() {
	ob_start();
	include_once __DIR__ . '/dashboard/views/admin.php';
	echo ob_get_clean();
}

function cuicpro_handle_officials_page() {
	echo "<h1>Officials</h1>";
}
