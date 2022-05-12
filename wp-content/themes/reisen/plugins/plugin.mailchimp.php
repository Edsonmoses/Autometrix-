<?php
/* Mail Chimp support functions
------------------------------------------------------------------------------- */

// Theme init
if (!function_exists('reisen_mailchimp_theme_setup')) {
    add_action( 'reisen_action_before_init_theme', 'reisen_mailchimp_theme_setup', 1 );
    function reisen_mailchimp_theme_setup() {
        if (is_admin()) {
            add_filter( 'reisen_filter_required_plugins',					'reisen_mailchimp_required_plugins' );
        }
    }
}

// Check if Instagram Feed installed and activated
if ( !function_exists( 'reisen_exists_mailchimp' ) ) {
    function reisen_exists_mailchimp() {
        return function_exists('mc4wp_load_plugin');
    }
}

// Filter to add in the required plugins list
if ( !function_exists( 'reisen_mailchimp_required_plugins' ) ) {
    //Handler of add_filter('reisen_filter_required_plugins',	'reisen_mailchimp_required_plugins');
    function reisen_mailchimp_required_plugins($list=array()) {
        if (in_array('mailchimp', reisen_storage_get('required_plugins')))
            $list[] = array(
                'name' 		=> esc_html__('MailChimp for WP', 'reisen'),
                'slug' 		=> 'mailchimp-for-wp',
                'required' 	=> false
            );
        return $list;
    }
}
?>