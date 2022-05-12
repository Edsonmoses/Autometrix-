<?php
/* Instagram Feed support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if (!function_exists('reisen_trx_updater_theme_setup')) {
    add_action( 'reisen_action_before_init_theme', 'reisen_trx_updater_theme_setup', 1 );
    function reisen_trx_updater_theme_setup() {
        if (is_admin()) {
            add_filter( 'reisen_filter_required_plugins',		'reisen_trx_updater_required_plugins' );
        }
    }
}

// Filter to add in the required plugins list
if ( !function_exists( 'reisen_trx_updater_required_plugins' ) ) {
    function reisen_trx_updater_required_plugins($list=array()) {
        if (in_array('trx_updater', (array)reisen_storage_get('required_plugins'))) {
            $list[] = array(
                'name' 		=> esc_html__('Themerex Updater', 'reisen'),
                'slug' 		=> 'trx_updater',
                'version'   => '1.4.1',
                'source'	=> reisen_get_file_dir('plugins/install/trx_updater.zip'),
                'required' 	=> false
            );
        }
        return $list;
    }
}

// Check if Instagram Feed installed and activated
if ( !function_exists( 'reisen_exists_trx_updater' ) ) {
    function reisen_exists_trx_updater() {
        return defined('TRX_UPDATER_VERSION');
    }
}
?>