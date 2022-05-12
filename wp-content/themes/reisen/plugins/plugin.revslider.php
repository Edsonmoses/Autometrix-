<?php
/* Revolution Slider support functions
------------------------------------------------------------------------------- */

// Theme init
if (!function_exists('reisen_revslider_theme_setup')) {
    add_action( 'reisen_action_before_init_theme', 'reisen_revslider_theme_setup', 1 );
    function reisen_revslider_theme_setup() {
        if (reisen_exists_revslider()) {
            add_filter( 'reisen_filter_list_sliders',					'reisen_revslider_list_sliders' );
            add_filter( 'reisen_filter_theme_options_params',			'reisen_revslider_theme_options_params' );
        }
        if (is_admin()) {
            add_filter( 'reisen_filter_required_plugins',				'reisen_revslider_required_plugins' );
        }
    }
}

if ( !function_exists( 'reisen_revslider_settings_theme_setup2' ) ) {
    add_action( 'reisen_action_before_init_theme', 'reisen_revslider_settings_theme_setup2', 3 );
    function reisen_revslider_settings_theme_setup2() {
        if (reisen_exists_revslider()) {

            // Add Revslider specific options in the Theme Options
            reisen_storage_set_array_after('options', 'slider_engine', "slider_alias", array(
                    "title" => esc_html__('Revolution Slider: Select slider',  'reisen'),
                    "desc" => wp_kses_data( __("Select slider to show (if engine=revo in the field above)", 'reisen') ),
                    "override" => "category,services_group,page",
                    "dependency" => array(
                        'show_slider' => array('yes'),
                        'slider_engine' => array('revo')
                    ),
                    "std" => "",
                    "options" => reisen_get_options_param('list_revo_sliders'),
                    "type" => "select"
                )
            );

        }
    }
}

// Check if RevSlider installed and activated
if ( !function_exists( 'reisen_exists_revslider' ) ) {
    function reisen_exists_revslider() {
        return function_exists('rev_slider_shortcode');
    }
}

// Filter to add in the required plugins list
if ( !function_exists( 'reisen_revslider_required_plugins' ) ) {
    //Handler of add_filter('reisen_filter_required_plugins',	'reisen_revslider_required_plugins');
    function reisen_revslider_required_plugins($list=array()) {
        if (in_array('revslider', reisen_storage_get('required_plugins'))) {
            $path = reisen_get_file_dir('plugins/install/revslider.zip');
            if (file_exists($path)) {
                $list[] = array(
                    'name' 		=> esc_html__('Revolution Slider', 'reisen'),
                    'slug' 		=> 'revslider',
                    'version'	=> '6.3.2',
                    'source'	=> $path,
                    'required' 	=> false
                );
            }
        }
        return $list;
    }
}

// Lists
//------------------------------------------------------------------------

// Add RevSlider in the sliders list, prepended inherit (if need)
if ( !function_exists( 'reisen_revslider_list_sliders' ) ) {
    //Handler of add_filter( 'reisen_filter_list_sliders',					'reisen_revslider_list_sliders' );
    function reisen_revslider_list_sliders($list=array()) {
        $list["revo"] = esc_html__("Layer slider (Revolution)", 'reisen');
        return $list;
    }
}

// Return Revo Sliders list, prepended inherit (if need)
if ( !function_exists( 'reisen_get_list_revo_sliders' ) ) {
    function reisen_get_list_revo_sliders($prepend_inherit=false) {
        if (($list = reisen_storage_get('list_revo_sliders'))=='') {
            $list = array();
            if (reisen_exists_revslider()) {
                global $wpdb;
                // Attention! The use of wpdb->prepare() is not required
                // because the query does not use external data substitution
                $rows = $wpdb->get_results( "SELECT alias, title FROM " . esc_sql($wpdb->prefix) . "revslider_sliders" );
                if (is_array($rows) && count($rows) > 0) {
                    foreach ($rows as $row) {
                        $list[$row->alias] = $row->title;
                    }
                }
            }
            $list = apply_filters('reisen_filter_list_revo_sliders', $list);
            if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set('list_revo_sliders', $list);
        }
        return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
    }
}

// Add RevSlider in the Theme Options params
if ( !function_exists( 'reisen_revslider_theme_options_params' ) ) {
    //Handler of add_filter( 'reisen_filter_theme_options_params',			'reisen_revslider_theme_options_params' );
    function reisen_revslider_theme_options_params($list=array()) {
        $list["list_revo_sliders"] = array('$reisen_get_list_revo_sliders' => '');
        return $list;
    }
}
?>