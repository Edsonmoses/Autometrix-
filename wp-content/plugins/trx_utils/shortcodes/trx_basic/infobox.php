<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('reisen_sc_infobox_theme_setup')) {
	add_action( 'reisen_action_before_init_theme', 'reisen_sc_infobox_theme_setup' );
	function reisen_sc_infobox_theme_setup() {
		add_action('reisen_action_shortcodes_list', 		'reisen_sc_infobox_reg_shortcodes');
		if (function_exists('reisen_exists_visual_composer') && reisen_exists_visual_composer())
			add_action('reisen_action_shortcodes_list_vc','reisen_sc_infobox_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_infobox id="unique_id" style="regular|info|success|error|result" static="0|1"]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta[/trx_infobox]
*/

if (!function_exists('reisen_sc_infobox')) {	
	function reisen_sc_infobox($atts, $content=null){	
		if (reisen_in_shortcode_blogger()) return '';
		extract(reisen_html_decode(shortcode_atts(array(
			// Individual params
			"style" => "regular",
			"closeable" => "no",
			"icon" => "",
			"color" => "",
			"bg_color" => "",
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
		$css .= ($color !== '' ? 'color:' . esc_attr($color) .';' : '')
			. ($bg_color !== '' ? 'background-color:' . esc_attr($bg_color) .';' : '');
		if (empty($icon)) {
			if ($style=='regular')
				$icon = 'icon-cog';
			else if ($style=='success')
				$icon = 'icon-check';
			else if ($style=='error')
				$icon = 'icon-attention';
			else if ($style=='info')
				$icon = 'icon-info';
		} else if ($icon=='none')
			$icon = '';

		$content = do_shortcode($content);
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
				. ' class="sc_infobox sc_infobox_style_' . esc_attr($style) 
					. (reisen_param_is_on($closeable) ? ' sc_infobox_closeable' : '') 
					. (!empty($class) ? ' '.esc_attr($class) : '') 
					. ($icon!='' && !reisen_param_is_inherit($icon) ? ' sc_infobox_iconed '. esc_attr($icon) : '') 
					. '"'
				. (!reisen_param_is_off($animation) ? ' data-animation="'.esc_attr(reisen_get_animation_classes($animation)).'"' : '')
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
				. '>'
				. trim($content)
				. '</div>';
		return apply_filters('reisen_shortcode_output', $output, 'trx_infobox', $atts, $content);
	}
	add_shortcode('trx_infobox', 'reisen_sc_infobox');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'reisen_sc_infobox_reg_shortcodes' ) ) {
	//add_action('reisen_action_shortcodes_list', 'reisen_sc_infobox_reg_shortcodes');
	function reisen_sc_infobox_reg_shortcodes() {
	
		reisen_sc_map("trx_infobox", array(
			"title" => esc_html__("Infobox", 'trx_utils'),
			"desc" => wp_kses_data( __("Insert infobox into your post (page)", 'trx_utils') ),
			"decorate" => false,
			"container" => true,
			"params" => array(
				"style" => array(
					"title" => esc_html__("Style", 'trx_utils'),
					"desc" => wp_kses_data( __("Infobox style", 'trx_utils') ),
					"value" => "regular",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => array(
						'regular' => esc_html__('Regular', 'trx_utils'),
						'info' => esc_html__('Info', 'trx_utils'),
						'success' => esc_html__('Success', 'trx_utils'),
						'error' => esc_html__('Error', 'trx_utils')
					)
				),
				"closeable" => array(
					"title" => esc_html__("Closeable box", 'trx_utils'),
					"desc" => wp_kses_data( __("Create closeable box (with close button)", 'trx_utils') ),
					"value" => "no",
					"type" => "switch",
					"options" => reisen_get_sc_param('yes_no')
				),
				"icon" => array(
					"title" => esc_html__("Custom icon",  'trx_utils'),
					"desc" => wp_kses_data( __('Select icon for the infobox from Fontello icons set. If empty - use default icon',  'trx_utils') ),
					"value" => "",
					"type" => "icons",
					"options" => reisen_get_sc_param('icons')
				),
				"color" => array(
					"title" => esc_html__("Text color", 'trx_utils'),
					"desc" => wp_kses_data( __("Any color for text and headers", 'trx_utils') ),
					"value" => "",
					"type" => "color"
				),
				"bg_color" => array(
					"title" => esc_html__("Background color", 'trx_utils'),
					"desc" => wp_kses_data( __("Any background color for this infobox", 'trx_utils') ),
					"value" => "",
					"type" => "color"
				),
				"_content_" => array(
					"title" => esc_html__("Infobox content", 'trx_utils'),
					"desc" => wp_kses_data( __("Content for infobox", 'trx_utils') ),
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
				"animation" => reisen_get_sc_param('animation'),
				"css" => reisen_get_sc_param('css')
			)
		));
	}
}


/* Register shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'reisen_sc_infobox_reg_shortcodes_vc' ) ) {
	//add_action('reisen_action_shortcodes_list_vc', 'reisen_sc_infobox_reg_shortcodes_vc');
	function reisen_sc_infobox_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_infobox",
			"name" => esc_html__("Infobox", 'trx_utils'),
			"description" => wp_kses_data( __("Box with info or error message", 'trx_utils') ),
			"category" => esc_html__('Content', 'trx_utils'),
			'icon' => 'icon_trx_infobox',
			"class" => "trx_sc_container trx_sc_infobox",
			"content_element" => true,
			"is_container" => true,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "style",
					"heading" => esc_html__("Style", 'trx_utils'),
					"description" => wp_kses_data( __("Infobox style", 'trx_utils') ),
					"admin_label" => true,
					"class" => "",
					"value" => array(
							esc_html__('Regular', 'trx_utils') => 'regular',
							esc_html__('Info', 'trx_utils') => 'info',
							esc_html__('Success', 'trx_utils') => 'success',
							esc_html__('Error', 'trx_utils') => 'error',
							esc_html__('Result', 'trx_utils') => 'result'
						),
					"type" => "dropdown"
				),
				array(
					"param_name" => "closeable",
					"heading" => esc_html__("Closeable", 'trx_utils'),
					"description" => wp_kses_data( __("Create closeable box (with close button)", 'trx_utils') ),
					"class" => "",
					"value" => array(esc_html__('Close button', 'trx_utils') => 'yes'),
					"type" => "checkbox"
				),
				array(
					"param_name" => "icon",
					"heading" => esc_html__("Custom icon", 'trx_utils'),
					"description" => wp_kses_data( __("Select icon for the infobox from Fontello icons set. If empty - use default icon", 'trx_utils') ),
					"class" => "",
					"value" => reisen_get_sc_param('icons'),
					"type" => "dropdown"
				),
				array(
					"param_name" => "color",
					"heading" => esc_html__("Text color", 'trx_utils'),
					"description" => wp_kses_data( __("Any color for the text and headers", 'trx_utils') ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				array(
					"param_name" => "bg_color",
					"heading" => esc_html__("Background color", 'trx_utils'),
					"description" => wp_kses_data( __("Any background color for this infobox", 'trx_utils') ),
					"class" => "",
					"value" => "",
					"type" => "colorpicker"
				),
				reisen_get_vc_param('id'),
				reisen_get_vc_param('class'),
				reisen_get_vc_param('animation'),
				reisen_get_vc_param('css'),
				reisen_get_vc_param('margin_top'),
				reisen_get_vc_param('margin_bottom'),
				reisen_get_vc_param('margin_left'),
				reisen_get_vc_param('margin_right')
			),
			'js_view' => 'VcTrxTextContainerView'
		) );
		
		class WPBakeryShortCode_Trx_Infobox extends Reisen_Vc_ShortCodeContainer {}
	}
}
?>