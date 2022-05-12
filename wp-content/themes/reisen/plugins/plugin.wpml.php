<?php
/* WPML support functions
------------------------------------------------------------------------------- */

// Check if WPML installed and activated
if ( !function_exists( 'reisen_exists_wpml' ) ) {
	function reisen_exists_wpml() {
		return defined('ICL_SITEPRESS_VERSION') && class_exists('sitepress');
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'reisen_wpml_required_plugins' ) ) {
	//Handler of add_filter('reisen_filter_required_plugins',	'reisen_wpml_required_plugins');
	function reisen_wpml_required_plugins($list=array()) {
		if (in_array('wpml', reisen_storage_get('required_plugins'))) {
			$path = reisen_get_file_dir('plugins/install/wpml.zip');
			if (file_exists($path)) {
				$list[] = array(
					'name' 		=> esc_html__('WPML', 'reisen'),
					'slug' 		=> 'wpml',
					'source'	=> $path,
					'required' 	=> false
					);
			}
		}
		return $list;
	}
}
?>