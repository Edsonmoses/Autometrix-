<?php
/* WPBakery PageBuilder support functions
------------------------------------------------------------------------------- */

// Theme init
if (!function_exists('reisen_vc_theme_setup')) {
	add_action( 'reisen_action_before_init_theme', 'reisen_vc_theme_setup', 1 );
	function reisen_vc_theme_setup() {
		if (reisen_exists_visual_composer()) {
			add_action('reisen_action_add_styles',		 				'reisen_vc_frontend_scripts' );
		}
		if (is_admin()) {
			add_filter( 'reisen_filter_required_plugins',					'reisen_vc_required_plugins' );
		}
	}
}

// Check if WPBakery PageBuilder installed and activated
if ( !function_exists( 'reisen_exists_visual_composer' ) ) {
	function reisen_exists_visual_composer() {
		return class_exists('Vc_Manager');
	}
}

// Check if WPBakery PageBuilder in frontend editor mode
if ( !function_exists( 'reisen_vc_is_frontend' ) ) {
	function reisen_vc_is_frontend() {
		return (isset($_GET['vc_editable']) && $_GET['vc_editable']=='true')
			|| (isset($_GET['vc_action']) && $_GET['vc_action']=='vc_inline');
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'reisen_vc_required_plugins' ) ) {
	//Handler of add_filter('reisen_filter_required_plugins',	'reisen_vc_required_plugins');
	function reisen_vc_required_plugins($list=array()) {
		if (in_array('visual_composer', reisen_storage_get('required_plugins'))) {
			$path = reisen_get_file_dir('plugins/install/js_composer.zip');
			if (file_exists($path)) {
				$list[] = array(
					'name' 		=> esc_html__('WPBakery PageBuilder', 'reisen'),
					'slug' 		=> 'js_composer',
                    'version'	=> '6.4.2',
                    'source'	=> $path,
					'required' 	=> false
				);
			}
		}
		return $list;
	}
}

// Enqueue VC custom styles
if ( !function_exists( 'reisen_vc_frontend_scripts' ) ) {
	//Handler of add_action( 'reisen_action_add_styles', 'reisen_vc_frontend_scripts' );
	function reisen_vc_frontend_scripts() {
		if (file_exists(reisen_get_file_dir('css/plugin.visual-composer.css')))
			wp_enqueue_style( 'reisen-plugin-visual-composer-style',  reisen_get_file_url('css/plugin.visual-composer.css'), array(), null );
	}
}
?>