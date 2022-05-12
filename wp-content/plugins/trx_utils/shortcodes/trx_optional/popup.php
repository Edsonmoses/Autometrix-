<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('reisen_sc_popup_theme_setup')) {
	add_action( 'reisen_action_before_init_theme', 'reisen_sc_popup_theme_setup' );
	function reisen_sc_popup_theme_setup() {
		add_action('reisen_action_shortcodes_list', 		'reisen_sc_popup_reg_shortcodes');
		if (function_exists('reisen_exists_visual_composer') && reisen_exists_visual_composer())
			add_action('reisen_action_shortcodes_list_vc','reisen_sc_popup_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_popup id="unique_id" class="class_name" style="css_styles"]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta[/trx_popup]
*/

if (!function_exists('reisen_sc_popup')) {	
	function reisen_sc_popup($atts, $content=null){	
		if (reisen_in_shortcode_blogger()) return '';
		extract(reisen_html_decode(shortcode_atts(array(
			// Common params
			"id" => "",
			"class" => "",
			"css" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
		$class .= ($class ? ' ' : '') . reisen_get_css_position_as_classes($top, $right, $bottom, $left);
		reisen_enqueue_popup('magnific');
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
				. ' class="sc_popup mfp-with-anim mfp-hide' . ($class ? ' '.esc_attr($class) : '') . '"'
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
				. '>' 
				. do_shortcode($content) 
				. '</div>';
		return apply_filters('reisen_shortcode_output', $output, 'trx_popup', $atts, $content);
	}
	add_shortcode('trx_popup', 'reisen_sc_popup');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'reisen_sc_popup_reg_shortcodes' ) ) {
	//add_action('reisen_action_shortcodes_list', 'reisen_sc_popup_reg_shortcodes');
	function reisen_sc_popup_reg_shortcodes() {
	
		reisen_sc_map("trx_popup", array(
			"title" => esc_html__("Popup window", 'trx_utils'),
			"desc" => wp_kses_data( __("Container for any html-block with desired class and style for popup window", 'trx_utils') ),
			"decorate" => true,
			"container" => true,
			"params" => array(
				"_content_" => array(
					"title" => esc_html__("Container content", 'trx_utils'),
					"desc" => wp_kses_data( __("Content for section container", 'trx_utils') ),
					"divider" => true,
					"rows" => 4,
					"value" => "",
					"type" => "textarea"
				),
				"top" => reisen_get_sc_param('top'),
				"bottom" => reisen_get_sc_param('bottom'),
				"left" => reisen_get_sc_param('left'),
				"right" => reisen_get_sc_param('right'),
				"id" => reisen_get_sc_param('id'),
				"class" => reisen_get_sc_param('class'),
				"css" => reisen_get_sc_param('css')
			)
		));
	}
}


/* Register shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'reisen_sc_popup_reg_shortcodes_vc' ) ) {
	//add_action('reisen_action_shortcodes_list_vc', 'reisen_sc_popup_reg_shortcodes_vc');
	function reisen_sc_popup_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_popup",
			"name" => esc_html__("Popup window", 'trx_utils'),
			"description" => wp_kses_data( __("Container for any html-block with desired class and style for popup window", 'trx_utils') ),
			"category" => esc_html__('Content', 'trx_utils'),
			'icon' => 'icon_trx_popup',
			"class" => "trx_sc_collection trx_sc_popup",
			"content_element" => true,
			"is_container" => true,
			"show_settings_on_create" => true,
			"params" => array(
				reisen_get_vc_param('id'),
				reisen_get_vc_param('class'),
				reisen_get_vc_param('css'),
				reisen_get_vc_param('margin_top'),
				reisen_get_vc_param('margin_bottom'),
				reisen_get_vc_param('margin_left'),
				reisen_get_vc_param('margin_right')
			)
		) );
		
		class WPBakeryShortCode_Trx_Popup extends Reisen_Vc_ShortCodeCollection {}
	}
}
?>