<?php
/* Contact Form 7 support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if (!function_exists('reisen_cf7_theme_setup')) {
    add_action( 'after_setup_theme', 'reisen_cf7_theme_setup', 9 );
    function reisen_cf7_theme_setup() {
        if (is_admin()) {
            add_filter( 'reisen_filter_required_plugins',			'reisen_cf7_required_plugins' );
        }
    }
}


// Check if cf7 installed and activated
if ( !function_exists( 'reisen_exists_cf7' ) ) {
    function reisen_exists_cf7() {
        return class_exists('WPCF7');
    }
}


// Filter to add in the required plugins list
if ( !function_exists( 'reisen_cf7_required_plugins' ) ) {
    function reisen_cf7_required_plugins($list=array()) {

        $list[] = array(
            'name' 		=> esc_html__('Contact Form 7', 'reisen'),
            'slug' 		=> 'contact-form-7',
            'required' 	=> false
        );

        return $list;
    }
}
?>