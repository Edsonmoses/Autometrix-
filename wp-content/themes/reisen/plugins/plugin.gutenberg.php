<?php
/* Gutenberg support functions
------------------------------------------------------------------------------- */

// Theme init
if (!function_exists('reisen_gutenberg_theme_setup')) {
	add_action( 'reisen_action_before_init_theme', 'reisen_gutenberg_theme_setup', 1 );
	function reisen_gutenberg_theme_setup() {
		if (is_admin()) {
			add_filter( 'reisen_filter_required_plugins', 'reisen_gutenberg_required_plugins' );
		}
	}
}

// Check if Instagram Widget installed and activated
if ( !function_exists( 'reisen_exists_gutenberg' ) ) {
	function reisen_exists_gutenberg() {
		return function_exists( 'the_gutenberg_project' ) && function_exists( 'register_block_type' );
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'reisen_gutenberg_required_plugins' ) ) {
	//Handler of add_filter('reisen_filter_required_plugins',	'reisen_gutenberg_required_plugins');
	function reisen_gutenberg_required_plugins($list=array()) {
		if (in_array('gutenberg', (array)reisen_storage_get('required_plugins')))
			$list[] = array(
					'name' 		=> esc_html__('Gutenberg', 'reisen'),
					'slug' 		=> 'gutenberg',
					'required' 	=> false
				);
		return $list;
	}
}
