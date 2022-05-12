<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('reisen_sc_number_theme_setup')) {
	add_action( 'reisen_action_before_init_theme', 'reisen_sc_number_theme_setup' );
	function reisen_sc_number_theme_setup() {
		add_action('reisen_action_shortcodes_list', 		'reisen_sc_number_reg_shortcodes');
		if (function_exists('reisen_exists_visual_composer') && reisen_exists_visual_composer())
			add_action('reisen_action_shortcodes_list_vc','reisen_sc_number_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_number id="unique_id" value="400"]
*/

if (!function_exists('reisen_sc_number')) {	
	function reisen_sc_number($atts, $content=null){	
		if (reisen_in_shortcode_blogger()) return '';
		extract(reisen_html_decode(shortcode_atts(array(
			// Individual params
			"value" => "",
			"align" => "",
			// Common params
			"id" => "",
			"class" => "",
			"animation" => "",
			"css" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
		$class .= ($class ? ' ' : '') . reisen_get_css_position_as_classes($top, $right, $bottom, $left);
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
				. ' class="sc_number' 
					. (!empty($align) ? ' align'.esc_attr($align) : '') 
					. (!empty($class) ? ' '.esc_attr($class) : '') 
					. '"'
				. (!reisen_param_is_off($animation) ? ' data-animation="'.esc_attr(reisen_get_animation_classes($animation)).'"' : '')
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
				. '>';
		for ($i=0; $i < reisen_strlen($value); $i++) {
			$output .= '<span class="sc_number_item">' . trim(reisen_substr($value, $i, 1)) . '</span>';
		}
		$output .= '</div>';
		return apply_filters('reisen_shortcode_output', $output, 'trx_number', $atts, $content);
	}
	add_shortcode('trx_number', 'reisen_sc_number');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'reisen_sc_number_reg_shortcodes' ) ) {
	//add_action('reisen_action_shortcodes_list', 'reisen_sc_number_reg_shortcodes');
	function reisen_sc_number_reg_shortcodes() {
	
		reisen_sc_map("trx_number", array(
			"title" => esc_html__("Number", 'trx_utils'),
			"desc" => wp_kses_data( __("Insert number or any word as set separate characters", 'trx_utils') ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"value" => array(
					"title" => esc_html__("Value", 'trx_utils'),
					"desc" => wp_kses_data( __("Number or any word", 'trx_utils') ),
					"value" => "",
					"type" => "text"
				),
				"align" => array(
					"title" => esc_html__("Align", 'trx_utils'),
					"desc" => wp_kses_data( __("Select block alignment", 'trx_utils') ),
					"value" => "none",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => reisen_get_sc_param('align')
				),
				"top" => reisen_get_sc_param('top'),
				"bottom" => reisen_get_sc_param('bottom'),
				"left" => reisen_get_sc_param('left'),
				"right" => reisen_get_sc_param('right'),
				"id" => reisen_get_sc_param('id'),
				"class" => reisen_get_sc_param('class'),
				"animation" => reisen_get_sc_param('animation'),
				"css" => reisen_get_sc_param('css')
			)
		));
	}
}


/* Register shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'reisen_sc_number_reg_shortcodes_vc' ) ) {
	//add_action('reisen_action_shortcodes_list_vc', 'reisen_sc_number_reg_shortcodes_vc');
	function reisen_sc_number_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_number",
			"name" => esc_html__("Number", 'trx_utils'),
			"description" => wp_kses_data( __("Insert number or any word as set of separated characters", 'trx_utils') ),
			"category" => esc_html__('Content', 'trx_utils'),
			"class" => "trx_sc_single trx_sc_number",
			'icon' => 'icon_trx_number',
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "value",
					"heading" => esc_html__("Value", 'trx_utils'),
					"description" => wp_kses_data( __("Number or any word to separate", 'trx_utils') ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "align",
					"heading" => esc_html__("Alignment", 'trx_utils'),
					"description" => wp_kses_data( __("Select block alignment", 'trx_utils') ),
					"class" => "",
					"value" => array_flip(reisen_get_sc_param('align')),
					"type" => "dropdown"
				),
				reisen_get_vc_param('id'),
				reisen_get_vc_param('class'),
				reisen_get_vc_param('animation'),
				reisen_get_vc_param('css'),
				reisen_get_vc_param('margin_top'),
				reisen_get_vc_param('margin_bottom'),
				reisen_get_vc_param('margin_left'),
				reisen_get_vc_param('margin_right')
			)
		) );
		
		class WPBakeryShortCode_Trx_Number extends Reisen_Vc_ShortCodeSingle {}
	}
}
?>