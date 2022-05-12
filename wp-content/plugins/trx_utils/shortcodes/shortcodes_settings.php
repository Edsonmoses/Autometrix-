<?php

// Check if shortcodes settings are now used
if ( !function_exists( 'reisen_shortcodes_is_used' ) ) {
	function reisen_shortcodes_is_used() {
		return reisen_options_is_used() 															// All modes when Theme Options are used
			|| (is_admin() && isset($_POST['action']) 
					&& in_array($_POST['action'], array('vc_edit_form', 'wpb_show_edit_form')))		// AJAX query when save post/page
			|| (is_admin() && !empty($_REQUEST['page']) && $_REQUEST['page']=='vc-roles')			// VC Role Manager
			|| (function_exists('reisen_vc_is_frontend') && reisen_vc_is_frontend());			// VC Frontend editor mode
	}
}

// Width and height params
if ( !function_exists( 'reisen_shortcodes_width' ) ) {
	function reisen_shortcodes_width($w="") {
		return array(
			"title" => esc_html__("Width", 'trx_utils'),
			"divider" => true,
			"value" => $w,
			"type" => "text"
		);
	}
}
if ( !function_exists( 'reisen_shortcodes_height' ) ) {
	function reisen_shortcodes_height($h='') {
		return array(
			"title" => esc_html__("Height", 'trx_utils'),
			"desc" => wp_kses_data( __("Width and height of the element", 'trx_utils') ),
			"value" => $h,
			"type" => "text"
		);
	}
}

// Return sc_param value
if ( !function_exists( 'reisen_get_sc_param' ) ) {
	function reisen_get_sc_param($prm) {
		return reisen_storage_get_array('sc_params', $prm);
	}
}

// Set sc_param value
if ( !function_exists( 'reisen_set_sc_param' ) ) {
	function reisen_set_sc_param($prm, $val) {
		reisen_storage_set_array('sc_params', $prm, $val);
	}
}

// Add sc settings in the sc list
if ( !function_exists( 'reisen_sc_map' ) ) {
	function reisen_sc_map($sc_name, $sc_settings) {
		reisen_storage_set_array('shortcodes', $sc_name, $sc_settings);
	}
}

// Add sc settings in the sc list after the key
if ( !function_exists( 'reisen_sc_map_after' ) ) {
	function reisen_sc_map_after($after, $sc_name, $sc_settings='') {
		reisen_storage_set_array_after('shortcodes', $after, $sc_name, $sc_settings);
	}
}

// Add sc settings in the sc list before the key
if ( !function_exists( 'reisen_sc_map_before' ) ) {
	function reisen_sc_map_before($before, $sc_name, $sc_settings='') {
		reisen_storage_set_array_before('shortcodes', $before, $sc_name, $sc_settings);
	}
}

// Compare two shortcodes by title
if ( !function_exists( 'reisen_compare_sc_title' ) ) {
	function reisen_compare_sc_title($a, $b) {
		return strcmp($a['title'], $b['title']);
	}
}



/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'reisen_shortcodes_settings_theme_setup' ) ) {
//	if ( reisen_vc_is_frontend() )
	if ( (isset($_GET['vc_editable']) && $_GET['vc_editable']=='true') || (isset($_GET['vc_action']) && $_GET['vc_action']=='vc_inline') )
		add_action( 'reisen_action_before_init_theme', 'reisen_shortcodes_settings_theme_setup', 20 );
	else
		add_action( 'reisen_action_after_init_theme', 'reisen_shortcodes_settings_theme_setup' );
	function reisen_shortcodes_settings_theme_setup() {
		if (reisen_shortcodes_is_used()) {

			// Sort templates alphabetically
			$tmp = reisen_storage_get('registered_templates');
			ksort($tmp);
			reisen_storage_set('registered_templates', $tmp);

			// Prepare arrays 
			reisen_storage_set('sc_params', array(
			
				// Current element id
				'id' => array(
					"title" => esc_html__("Element ID", 'trx_utils'),
					"desc" => wp_kses_data( __("ID for current element", 'trx_utils') ),
					"divider" => true,
					"value" => "",
					"type" => "text"
				),
			
				// Current element class
				'class' => array(
					"title" => esc_html__("Element CSS class", 'trx_utils'),
					"desc" => wp_kses_data( __("CSS class for current element (optional)", 'trx_utils') ),
					"value" => "",
					"type" => "text"
				),
			
				// Current element style
				'css' => array(
					"title" => esc_html__("CSS styles", 'trx_utils'),
					"desc" => wp_kses_data( __("Any additional CSS rules (if need)", 'trx_utils') ),
					"value" => "",
					"type" => "text"
				),
			
			
				// Switcher choises
				'list_styles' => array(
					'ul'	=> esc_html__('Unordered', 'trx_utils'),
					'ol'	=> esc_html__('Ordered', 'trx_utils'),
					'iconed'=> esc_html__('Iconed', 'trx_utils')
				),

				'yes_no'	=> reisen_get_list_yesno(),
				'on_off'	=> reisen_get_list_onoff(),
				'dir' 		=> reisen_get_list_directions(),
				'align'		=> reisen_get_list_alignments(),
				'float'		=> reisen_get_list_floats(),
				'hpos'		=> reisen_get_list_hpos(),
				'show_hide'	=> reisen_get_list_showhide(),
				'sorting' 	=> reisen_get_list_sortings(),
				'ordering' 	=> reisen_get_list_orderings(),
				'shapes'	=> reisen_get_list_shapes(),
				'sizes'		=> reisen_get_list_sizes(),
				'sliders'	=> reisen_get_list_sliders(),
				'controls'	=> reisen_get_list_controls(),
                    'categories'=> is_admin() && reisen_get_value_gp('action')=='vc_edit_form' && substr(reisen_get_value_gp('tag'), 0, 4)=='trx_' && isset($_POST['params']['post_type']) && $_POST['params']['post_type']!='post'
                        ? reisen_get_list_terms(false, reisen_get_taxonomy_categories_by_post_type($_POST['params']['post_type']))
                        : reisen_get_list_categories(),
				'columns'	=> reisen_get_list_columns(),
				'images'	=> array_merge(array('none'=>"none"), reisen_get_list_images("images/icons", "png")),
				'icons'		=> array_merge(array("inherit", "none"), reisen_get_list_icons()),
				'locations'	=> reisen_get_list_dedicated_locations(),
				'filters'	=> reisen_get_list_portfolio_filters(),
				'formats'	=> reisen_get_list_post_formats_filters(),
				'hovers'	=> reisen_get_list_hovers(true),
				'hovers_dir'=> reisen_get_list_hovers_directions(true),
				'schemes'	=> reisen_get_list_color_schemes(true),
				'animations'		=> reisen_get_list_animations_in(),
				'margins' 			=> reisen_get_list_margins(true),
				'blogger_styles'	=> reisen_get_list_templates_blogger(),
				'forms'				=> reisen_get_list_templates_forms(),
				'posts_types'		=> reisen_get_list_posts_types(),
				'googlemap_styles'	=> reisen_get_list_googlemap_styles(),
				'field_types'		=> reisen_get_list_field_types(),
				'label_positions'	=> reisen_get_list_label_positions()
				)
			);

			// Common params
			reisen_set_sc_param('animation', array(
				"title" => esc_html__("Animation",  'trx_utils'),
				"desc" => wp_kses_data( __('Select animation while object enter in the visible area of page',  'trx_utils') ),
				"value" => "none",
				"type" => "select",
				"options" => reisen_get_sc_param('animations')
				)
			);
			reisen_set_sc_param('top', array(
				"title" => esc_html__("Top margin",  'trx_utils'),
				"divider" => true,
				"value" => "inherit",
				"type" => "select",
				"options" => reisen_get_sc_param('margins')
				)
			);
			reisen_set_sc_param('bottom', array(
				"title" => esc_html__("Bottom margin",  'trx_utils'),
				"value" => "inherit",
				"type" => "select",
				"options" => reisen_get_sc_param('margins')
				)
			);
			reisen_set_sc_param('left', array(
				"title" => esc_html__("Left margin",  'trx_utils'),
				"value" => "inherit",
				"type" => "select",
				"options" => reisen_get_sc_param('margins')
				)
			);
			reisen_set_sc_param('right', array(
				"title" => esc_html__("Right margin",  'trx_utils'),
				"desc" => wp_kses_data( __("Margins around this shortcode", 'trx_utils') ),
				"value" => "inherit",
				"type" => "select",
				"options" => reisen_get_sc_param('margins')
				)
			);

			reisen_storage_set('sc_params', apply_filters('reisen_filter_shortcodes_params', reisen_storage_get('sc_params')));

			// Shortcodes list
			//------------------------------------------------------------------
			reisen_storage_set('shortcodes', array());
			
			// Register shortcodes
			do_action('reisen_action_shortcodes_list');

			// Sort shortcodes list
			$tmp = reisen_storage_get('shortcodes');
			uasort($tmp, 'reisen_compare_sc_title');
			reisen_storage_set('shortcodes', $tmp);
		}
	}
}
?>