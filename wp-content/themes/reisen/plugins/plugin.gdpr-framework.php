<?php
/* The GDPR Framework support functions
------------------------------------------------------------------------------- */

// Theme init
if (!function_exists('reisen_gdpr_framework_theme_setup')) {
	add_action( 'reisen_action_before_init_theme', 'reisen_gdpr_framework_theme_setup', 1 );
	function reisen_gdpr_framework_theme_setup() {
		if (is_admin()) {
			add_filter( 'reisen_filter_required_plugins', 'reisen_gdpr_framework_required_plugins' );
		}
	}
}

// Check if Instagram Widget installed and activated
if ( !function_exists( 'reisen_exists_gdpr_framework' ) ) {
	function reisen_exists_gdpr_framework() {
		return defined( 'GDPR_FRAMEWORK_VERSION' );
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'reisen_gdpr_framework_required_plugins' ) ) {
	//Handler of add_filter('reisen_filter_required_plugins',	'reisen_gdpr_framework_required_plugins');
	function reisen_gdpr_framework_required_plugins($list=array()) {
		if (in_array('gdpr-framework', (array)reisen_storage_get('required_plugins')))
			$list[] = array(
					'name' 		=> esc_html__('The GDPR Framework', 'reisen'),
					'slug' 		=> 'gdpr-framework',
					'required' 	=> false
				);
		return $list;
	}
}
