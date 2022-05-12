<?php
/* Essential Grid support functions
------------------------------------------------------------------------------- */

// Theme init
if (!function_exists('reisen_essgrids_theme_setup')) {
    add_action( 'reisen_action_before_init_theme', 'reisen_essgrids_theme_setup', 1 );
    function reisen_essgrids_theme_setup() {
        if (is_admin()) {
            add_filter( 'reisen_filter_required_plugins',				'reisen_essgrids_required_plugins' );
        }
    }
}


// Check if Ess. Grid installed and activated
if ( !function_exists( 'reisen_exists_essgrids' ) ) {
    function reisen_exists_essgrids() {
        return defined('EG_PLUGIN_PATH');
    }
}

// Filter to add in the required plugins list
if ( !function_exists( 'reisen_essgrids_required_plugins' ) ) {
    //Handler of add_filter('reisen_filter_required_plugins',	'reisen_essgrids_required_plugins');
    function reisen_essgrids_required_plugins($list=array()) {
        if (in_array('essgrids', reisen_storage_get('required_plugins'))) {
            $path = reisen_get_file_dir('plugins/install/essential-grid.zip');
            if (file_exists($path)) {
                $list[] = array(
                    'name' 		=> esc_html__('Essential Grid', 'reisen'),
                    'slug' 		=> 'essential-grid',
                    'version'	=> '3.0.9',
                    'source'	=> $path,
                    'required' 	=> false
                );
            }
        }
        return $list;
    }
}
?>