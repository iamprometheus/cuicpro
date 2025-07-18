<?php
/**
 * PHP file to use when rendering the block type on the server to show on the front end.
 *
 * The following variables are exposed to the file:
 *     $attributes (array): The block attributes.
 *     $content (string): The block default content.
 *     $block (WP_Block): The block instance.
 *
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */

// Generates a unique id for aria-controls.
$unique_id = wp_unique_id( 'p-' ); 

// Adds the global state.
wp_interactivity_state(
	'cuicpro-profile'
);

if (!function_exists('MediaFileAlreadyExists')) {
	function MediaFileAlreadyExists($filename){
		global $wpdb;
		$filename = strtolower($filename);
		$query = "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_title = '$filename'";
		return ($wpdb->get_var($query)  > 0) ;
	}
}

if (!function_exists('base_url')) {
	function base_url($atRoot=FALSE, $atCore=FALSE, $parse=FALSE){
			if (isset($_SERVER['HTTP_HOST'])) {
					$http = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
					$hostname = $_SERVER['HTTP_HOST'];
					$dir =  str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
					
					$core = preg_split('@/@', str_replace($_SERVER['DOCUMENT_ROOT'], '', realpath(dirname(__FILE__))), -1, PREG_SPLIT_NO_EMPTY);
					$core = $core[0];
					
					$tmplt = $atRoot ? ($atCore ? "%s://%s/%s/" : "%s://%s/") : ($atCore ? "%s://%s/%s/" : "%s://%s%s");
					$end = $atRoot ? ($atCore ? $core : $hostname) : ($atCore ? $core : $dir);
					$base_url = sprintf( $tmplt, $http, $hostname, $end );
			}
			else $base_url = 'http://test.local/';
			
			if ($parse) {
					$base_url = parse_url($base_url);
					if (isset($base_url['path'])) if ($base_url['path'] == '/') $base_url['path'] = '';
			}
			
			return $base_url;
	}
}

add_action('wp_enqueue_scripts', function() {
	wp_enqueue_script('jquery');
});

?>

<div
	<?php echo get_block_wrapper_attributes(); ?>
	data-wp-interactive="cuicpro-profile"
	id="cuicpro-profile"
>
	<div class="profile-container">
		
	</div>
</div>
