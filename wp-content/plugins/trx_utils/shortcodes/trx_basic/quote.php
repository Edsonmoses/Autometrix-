<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('reisen_sc_quote_theme_setup')) {
	add_action( 'reisen_action_before_init_theme', 'reisen_sc_quote_theme_setup' );
	function reisen_sc_quote_theme_setup() {
		add_action('reisen_action_shortcodes_list', 		'reisen_sc_quote_reg_shortcodes');
		if (function_exists('reisen_exists_visual_composer') && reisen_exists_visual_composer())
			add_action('reisen_action_shortcodes_list_vc','reisen_sc_quote_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_quote id="unique_id" cite="url" title=""]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta[/quote]
*/

if (!function_exists('reisen_sc_quote')) {	
	function reisen_sc_quote($atts, $content=null){	
		if (reisen_in_shortcode_blogger()) return '';
		extract(reisen_html_decode(shortcode_atts(array(
			// Individual params
			"title" => "",
			"cite" => "",
			// Common params
			"id" => "",
			"class" => "",
			"animation" => "",
			"css" => "",
			"width" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
		$class .= ($class ? ' ' : '') . reisen_get_css_position_as_classes($top, $right, $bottom, $left);
		$css .= reisen_get_css_dimensions_from_values($width);
		$cite_param = $cite != '' ? ' cite="'.esc_attr($cite).'"' : '';
		$title = $title=='' ? $cite : $title;
		$content = do_shortcode($content);
		if (reisen_substr($content, 0, 2)!='<p') $content = '<p>' . ($content) . '</p>';
		$output = '<blockquote' 
			. ($id ? ' id="'.esc_attr($id).'"' : '') . ($cite_param) 
			. ' class="sc_quote'. (!empty($class) ? ' '.esc_attr($class) : '').'"' 
			. (!reisen_param_is_off($animation) ? ' data-animation="'.esc_attr(reisen_get_animation_classes($animation)).'"' : '')
			. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
			. '>'
				. ($content)
				. ($title == '' ? '' : ('<p class="sc_quote_title">' . ($cite!='' ? '<a href="'.esc_url($cite).'">' : '') . ($title) . ($cite!='' ? '</a>' : '') . '</p>'))
			.'</blockquote>';
		return apply_filters('reisen_shortcode_output', $output, 'trx_quote', $atts, $content);
	}
	add_shortcode('trx_quote', 'reisen_sc_quote');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'reisen_sc_quote_reg_shortcodes' ) ) {
	//add_action('reisen_action_shortcodes_list', 'reisen_sc_quote_reg_shortcodes');
	function reisen_sc_quote_reg_shortcodes() {
	
		reisen_sc_map("trx_quote", array(
			"title" => esc_html__("Quote", 'trx_utils'),
			"desc" => wp_kses_data( __("Quote text", 'trx_utils') ),
			"decorate" => false,
			"container" => true,
			"params" => array(
				"cite" => array(
					"title" => esc_html__("Quote cite", 'trx_utils'),
					"desc" => wp_kses_data( __("URL for quote cite", 'trx_utils') ),
					"value" => "",
					"type" => "text"
				),
				"title" => array(
					"title" => esc_html__("Title (author)", 'trx_utils'),
					"desc" => wp_kses_data( __("Quote title (author name)", 'trx_utils') ),
					"value" => "",
					"type" => "text"
				),
				"_content_" => array(
					"title" => esc_html__("Quote content", 'trx_utils'),
					"desc" => wp_kses_data( __("Quote content", 'trx_utils') ),
					"rows" => 4,
					"value" => "",
					"type" => "textarea"
				),
				"width" => reisen_shortcodes_width(),
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
if ( !function_exists( 'reisen_sc_quote_reg_shortcodes_vc' ) ) {
	//add_action('reisen_action_shortcodes_list_vc', 'reisen_sc_quote_reg_shortcodes_vc');
	function reisen_sc_quote_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_quote",
			"name" => esc_html__("Quote", 'trx_utils'),
			"description" => wp_kses_data( __("Quote text", 'trx_utils') ),
			"category" => esc_html__('Content', 'trx_utils'),
			'icon' => 'icon_trx_quote',
			"class" => "trx_sc_single trx_sc_quote",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "cite",
					"heading" => esc_html__("Quote cite", 'trx_utils'),
					"description" => wp_kses_data( __("URL for the quote cite link", 'trx_utils') ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "title",
					"heading" => esc_html__("Title (author)", 'trx_utils'),
					"description" => wp_kses_data( __("Quote title (author name)", 'trx_utils') ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "content",
					"heading" => esc_html__("Quote content", 'trx_utils'),
					"description" => wp_kses_data( __("Quote content", 'trx_utils') ),
					"class" => "",
					"value" => "",
					"type" => "textarea_html"
				),
				reisen_get_vc_param('id'),
				reisen_get_vc_param('class'),
				reisen_get_vc_param('animation'),
				reisen_get_vc_param('css'),
				reisen_vc_width(),
				reisen_get_vc_param('margin_top'),
				reisen_get_vc_param('margin_bottom'),
				reisen_get_vc_param('margin_left'),
				reisen_get_vc_param('margin_right')
			),
			'js_view' => 'VcTrxTextView'
		) );
		
		class WPBakeryShortCode_Trx_Quote extends Reisen_Vc_ShortCodeSingle {}
	}
}
?>