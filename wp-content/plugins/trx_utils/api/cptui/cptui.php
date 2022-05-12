<?php
/**
 * Plugin support: WooCommerce
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.5
 */

// Check if plugin installed and activated
// Attention! This function is used in many files and was moved to the api.php
/*
if ( !function_exists( 'trx_utils_exists_cptui' ) ) {
	function trx_utils_exists_cptui() {
		return defined( 'CPTUI_VERSION' );
	}
}
*/


// One-click import support
//------------------------------------------------------------------------

// Check plugin in the required plugins
if ( !function_exists( 'trx_utils_cptui_importer_required_plugins' ) ) {
	if (is_admin()) add_filter( 'trx_utils_filter_importer_required_plugins',	'trx_utils_cptui_importer_required_plugins', 10, 2 );
	function trx_utils_cptui_importer_required_plugins($not_installed='', $list='') {
		if (strpos($list, 'cptui')!==false && !trx_utils_exists_cptui() )
			$not_installed .= '<br>' . esc_html__('Custom Post Type UI', 'trx_utils');
		return $not_installed;
	}
}

// Set plugin's specific importer options
if ( !function_exists( 'trx_utils_cptui_importer_set_options' ) ) {
	if (is_admin()) add_filter( 'trx_utils_filter_importer_options',	'trx_utils_cptui_importer_set_options' );
	function trx_utils_cptui_importer_set_options($options=array()) {
		if ( trx_utils_exists_cptui() && in_array('cptui', $options['required_plugins']) ) {
			$options['additional_options'][]	= 'cptui_%';					// Add slugs to export options for this plugin
		}
		return $options;
	}
}

// Add checkbox to the one-click importer
if ( !function_exists( 'trx_utils_cptui_importer_show_params' ) ) {
	if (is_admin()) add_action( 'trx_utils_action_importer_params',	'trx_utils_cptui_importer_show_params', 10, 1 );
	function trx_utils_cptui_importer_show_params($importer) {
		if ( trx_utils_exists_cptui() && in_array('cptui', $importer->options['required_plugins']) ) {
			$importer->show_importer_params(array(
				'slug' => 'cptui',
				'title' => esc_html__('Import Custom Post Type UI', 'trx_utils'),
				'part' => 1
			));
		}
	}
}

// Display import progress
if ( !function_exists( 'trx_utils_cptui_importer_import_fields' ) ) {
	if (is_admin()) add_action( 'trx_utils_action_importer_import_fields',	'trx_utils_cptui_importer_import_fields', 10, 1 );
	function trx_utils_cptui_importer_import_fields($importer) {
		if ( trx_utils_exists_cptui() && in_array('cptui', $importer->options['required_plugins']) ) {
			$importer->show_importer_fields(array(
				'slug'=>'cptui',
				'title' => esc_html__('Custom Post Type UI', 'trx_utils')
				)
			);
		}
	}
}
?>