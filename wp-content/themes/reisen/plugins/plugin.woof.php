<?php
/* The GDPR Framework support functions
------------------------------------------------------------------------------- */

// Theme init
if (!function_exists('reisen_woof_theme_setup')) {
    add_action( 'reisen_action_before_init_theme', 'reisen_woof_theme_setup', 1 );
    function reisen_woof_theme_setup() {
        if (is_admin()) {
            add_filter( 'reisen_filter_required_plugins', 'reisen_woof_required_plugins' );
        }
    }
}

// Check if Instagram Widget installed and activated
if ( !function_exists( 'reisen_exists_woof' ) ) {
    function reisen_exists_woof() {
        return defined( 'WOOF_VERSION' );
    }
}

// Filter to add in the required plugins list
if ( !function_exists( 'reisen_woof_required_plugins' ) ) {
    //Handler of add_filter('reisen_filter_required_plugins',	'reisen_woof_required_plugins');
    function reisen_woof_required_plugins($list=array()) {
        if (in_array('woof', (array)reisen_storage_get('required_plugins')))
            $list[] = array(
                'name' 		=> esc_html__('WooCommerce Products Filter', 'reisen'),
                'slug' 		=> 'woof',
                'required' 	=> false
            );
        return $list;
    }
}