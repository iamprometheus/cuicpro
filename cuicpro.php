<?php
/**
 * Plugin Name:       CUICPRO
 * Description:       CUICPRO Extension for data management
 * Version:           1.0.3
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
		array( 'name' => 'cuicpro-tournaments' ),
		array( 'name' => 'cuicpro-profile' ),
		array( 'name' => 'cuicpro-schedule' ),
		array( 'name' => 'cuicpro-playoffs' ),
		array( 'name' => 'cuicpro-standings' ),
		array( 'name' => 'cuicpro-home' ),
		array( 'name' => 'cuicpro-maps' ),

	);

	foreach ( $blocks as $block ) {
		register_block_type_from_metadata(
			__DIR__ . '/build/' . $block['name']
		);
		register_block_type(__DIR__ . '/build/' . $block['name']);
	}
}

// hooks up your code to initialize and register the blocks
add_action( 'init', 'cuicpro_init' );

// Models
require_once __DIR__ . '/model/base/teams.php';
require_once __DIR__ . '/model/base/players.php';
require_once __DIR__ . '/model/base/coaches.php';
require_once __DIR__ . '/model/base/officials.php';
require_once __DIR__ . '/model/base/officials_hours.php';
require_once __DIR__ . '/model/base/divisions.php';
require_once __DIR__ . '/model/base/tournaments.php';
require_once __DIR__ . '/model/base/modes.php';
require_once __DIR__ . '/model/base/categories.php';
require_once __DIR__ . '/model/base/tournament_hours.php';
require_once __DIR__ . '/model/base/team_register_queue.php';
require_once __DIR__ . '/model/base/pending_players.php';
require_once __DIR__ . '/model/matches.php';
require_once __DIR__ . '/model/brackets.php';
require_once __DIR__ . '/model/pending_matches.php';
require_once __DIR__ . '/model/tournament_scheduler.php';

// User models
require_once __DIR__ . '/model/users/coaches_user.php';
require_once __DIR__ . '/model/users/players_user.php';
require_once __DIR__ . '/model/users/officials_user.php';
require_once __DIR__ . '/model/users/teams_user.php';

// Dashboard components
require_once __DIR__ . '/dashboard/components/divisions/divisions.php';
require_once __DIR__ . '/dashboard/components/teams/teams.php';
require_once __DIR__ . '/dashboard/components/coaches/coaches.php';
require_once __DIR__ . '/dashboard/components/officials/officials.php';
require_once __DIR__ . '/dashboard/components/tournaments/tournaments.php';
require_once __DIR__ . '/dashboard/components/brackets/brackets.php';
require_once __DIR__ . '/dashboard/components/matches/matches.php';
require_once __DIR__ . '/dashboard/components/officials_schedule/officials_schedule.php';
require_once __DIR__ . '/dashboard/components/matches_schedule/matches_schedule.php';
require_once __DIR__ . '/dashboard/components/register/register.php';
require_once __DIR__ . '/dashboard/components/tournaments/tournaments_list.php';

// Frontend components
// require_once __DIR__ . '/frontend/register_form/register_form.php';
require_once __DIR__ . '/frontend/tournaments/tournaments_frontend.php';
require_once __DIR__ . '/frontend/schedule/schedule_frontend.php';
// require_once __DIR__ . '/frontend/brackets/brackets_frontend.php';
require_once __DIR__ . '/frontend/profile/profile_frontend.php';
require_once __DIR__ . '/frontend/playoffs/playoffs.php';
require_once __DIR__ . '/frontend/standings/standings.php';

// Initialize database tables if they don't exist
function cuicpro_databases() {
	ModesDatabase::init();
	CategoriesDatabase::init();
	CoachesDatabase::init();
	TournamentsDatabase::init();
	TournamentHoursDatabase::init();
	TeamsDatabase::init();
	PlayersDatabase::init();
	OfficialsDatabase::init();
	OfficialsHoursDatabase::init();
	DivisionsDatabase::init();
	MatchesDatabase::init();
	BracketsDatabase::init();
	PendingMatchesDatabase::init();
	TeamRegisterQueueDatabase::init();
	PendingPlayersDatabase::init();
	CoachesUserDatabase::init();
	TeamsUserDatabase::init();
	PlayersUserDatabase::init();
	OfficialsUserDatabase::init();
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
	wp_enqueue_style('tour-css', plugin_dir_url(__FILE__) . 'dashboard/css/tour.min.css');

	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui');
	wp_enqueue_script('jquery-ui-tabs');
	wp_enqueue_script('jquery-ui-slider');
	wp_enqueue_script('jquery-ui-multidatespicker', plugin_dir_url(__FILE__) . 'dashboard/dependencies/jquery-ui.multidatespicker.js', array('jquery','jquery-ui-datepicker'));
	wp_enqueue_script('leader-line', plugin_dir_url(__FILE__) . 'dashboard/dependencies/leader-line.min.js');
	wp_enqueue_script('custom-script', plugin_dir_url(__FILE__) . 'dashboard/scripts/jQuery-ui-components.js', array('jquery'));
	wp_enqueue_script('guidedtourjs', plugin_dir_url(__FILE__) . 'dashboard/dependencies/tour.js');
	// Pass the AJAX URL to JavaScript
	wp_localize_script('custom-script', 'cuicpro', array(
		'ajax_url' => admin_url('admin-ajax.php')
	));
}

// Guided tour
function cuicpro_guided_tour() {
	wp_enqueue_script('guidedtour', plugin_dir_url(__FILE__) . 'dashboard/scripts/guidedtour.js');
}
add_action('admin_enqueue_scripts', 'cuicpro_guided_tour', 20);

// Roles
function cuicpro_roles() {
	add_role('coach', 'Coach', array(
		'read' => true,
		'edit_posts' => false,
		'upload_files' => false,
		'edit_others_posts' => false,
		'publish_posts' => false,
		'delete_posts' => false,
	));

	add_role('tournament-organizer', 'Admin de Torneo', array(
		'read' => true,
		'edit_posts' => false,
		'upload_files' => false,
		'edit_others_posts' => false,
		'publish_posts' => false,
		'delete_posts' => false,
		'cuicpro_manage_tournament' => true,
	));

	remove_role('player');
	// add_role('player', 'Jugador', array(
	// 	'read' => true,
	// 	'edit_posts' => false,
	// 	'upload_files' => false,
	// 	'edit_others_posts' => false,
	// 	'publish_posts' => false,
	// 	'delete_posts' => false,
	// ));
	remove_role('official');
	// add_role('official', 'Arbitro', array(
	// 	'read' => true,
	// 	'edit_posts' => false,
	// 	'upload_files' => false,
	// 	'edit_others_posts' => false,
	// 	'publish_posts' => false,
	// 	'delete_posts' => false,
	// ));

	// add capabilities to admin role
	$admin_role = get_role('administrator');
	$admin_role->add_cap('cuicpro_manage_tournament');
	$admin_role->add_cap('cuicpro_administrate_tournaments');
}
add_action('admin_init', 'cuicpro_roles');

// Dashboard
// Add plugin to menu Page
add_action('admin_menu', 'cuicpro_menu_page');

function cuicpro_menu_page() {

	if (!current_user_can('cuicpro_manage_tournament')) {
		return;
	}
	add_menu_page(
		'CUICPRO',
		'CUICPRO Admin',
		'cuicpro_manage_tournament',
		'cuicpro',
		'cuicpro_handle_admin_page',
		"dashicons-admin-multisite",
		3
	);

	add_submenu_page(
		'cuicpro',
		'Horario de arbitros',
		'Horario de arbitros',
		'cuicpro_manage_tournament',
		'cuicpro-officials-schedule',
		'cuicpro_handle_officials_schedule_page'
	);

	add_submenu_page(
		'cuicpro',
		'Horario de partidos',
		'Horario de partidos',
		'cuicpro_manage_tournament',
		'cuicpro-matches-schedule',
		'cuicpro_handle_matches_schedule_page'
	);

	add_submenu_page(
		'cuicpro',
		'Registro de equipos',
		'Registro de equipos',
		'cuicpro_manage_tournament',
		'cuicpro-register',
		'cuicpro_handle_register_page'
	);
}

function cuicpro_handle_admin_page() {
	ob_start();
	include_once __DIR__ . '/dashboard/views/admin.php';
	echo ob_get_clean();
}

function cuicpro_handle_officials_schedule_page() {
	ob_start();
	include_once __DIR__ . '/dashboard/views/officials_schedule.php';
	echo ob_get_clean();
}

function cuicpro_handle_matches_schedule_page() {
	ob_start();
	include_once __DIR__ . '/dashboard/views/matches_schedule.php';
	echo ob_get_clean();
}

function cuicpro_handle_register_page() {
	ob_start();
	include_once __DIR__ . '/dashboard/views/register.php';
	echo ob_get_clean();
}
