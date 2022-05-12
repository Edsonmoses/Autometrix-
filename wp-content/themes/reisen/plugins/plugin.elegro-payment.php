<?php
/* Instagram Feed support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if (!function_exists('reisen_elegro_payment_theme_setup')) {
    add_action( 'reisen_action_before_init_theme', 'reisen_elegro_payment_theme_setup', 1 );
    function reisen_elegro_payment_theme_setup() {
        if (is_admin()) {
            add_filter( 'reisen_filter_required_plugins',		'reisen_elegro_payment_required_plugins' );
        }
    }
}

// Filter to add in the required plugins list
if ( !function_exists( 'reisen_elegro_payment_required_plugins' ) ) {
    function reisen_elegro_payment_required_plugins($list=array()) {
        if (in_array('elegro-payment', (array)reisen_storage_get('required_plugins'))) {
            $list[] = array(
                'name' 		=> esc_html__('Elegro Payment', 'reisen'),
                'slug' 		=> 'elegro-payment',
                'required' 	=> false
            );
        }
        return $list;
    }
}

// Check if Instagram Feed installed and activated
if ( !function_exists( 'reisen_exists_elegro_payment' ) ) {
    function reisen_exists_elegro_payment() {
        return function_exists('init_Elegro_Payment');
    }
}
?>