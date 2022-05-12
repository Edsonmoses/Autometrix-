<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('reisen_sc_price_theme_setup')) {
	add_action( 'reisen_action_before_init_theme', 'reisen_sc_price_theme_setup' );
	function reisen_sc_price_theme_setup() {
		add_action('reisen_action_shortcodes_list', 		'reisen_sc_price_reg_shortcodes');
		if (function_exists('reisen_exists_visual_composer') && reisen_exists_visual_composer())
			add_action('reisen_action_shortcodes_list_vc','reisen_sc_price_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_price id="unique_id" currency="$" money="29.99" period="monthly"]
*/

if (!function_exists('reisen_sc_price')) {	
	function reisen_sc_price($atts, $content=null){	
		if (reisen_in_shortcode_blogger()) return '';
		extract(reisen_html_decode(shortcode_atts(array(
			// Individual params
			"money" => "",
			"currency" => "$",
			"period" => "",
			"align" => "",
			// Common params
			"id" => "",
			"class" => "",
			"css" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
		$output = '';
		if (!empty($money)) {
			$class .= ($class ? ' ' : '') . reisen_get_css_position_as_classes($top, $right, $bottom, $left);
			$m = explode('.', str_replace(',', '.', $money));
			$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
					. ' class="sc_price'
					. (!empty($class) ? ' '.esc_attr($class) : '')
					. ($align && $align!='none' ? ' align'.esc_attr($align) : '') 
					. '"'
					. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
					. '>'
				. '<span class="sc_price_currency">'.($currency).'</span>'
				. '<span class="sc_price_money">'.($m[0]).'</span>'
				. (!empty($m[1]) ? '<span class="sc_price_info">' : '')
				. (!empty($m[1]) ? '<span class="sc_price_penny">'.($m[1]).'</span>' : '')
				. (!empty($period) ? '<span class="sc_price_period">'.($period).'</span>' : (!empty($m[1]) ? '<span class="sc_price_period_empty"></span>' : ''))
				. (!empty($m[1]) ? '</span>' : '')
				. '</div>';
		}
		return apply_filters('reisen_shortcode_output', $output, 'trx_price', $atts, $content);
	}
	add_shortcode('trx_price', 'reisen_sc_price');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'reisen_sc_price_reg_shortcodes' ) ) {
	//add_action('reisen_action_shortcodes_list', 'reisen_sc_price_reg_shortcodes');
	function reisen_sc_price_reg_shortcodes() {
	
		reisen_sc_map("trx_price", array(
			"title" => esc_html__("Price", 'trx_utils'),
			"desc" => wp_kses_data( __("Insert price with decoration", 'trx_utils') ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"money" => array(
					"title" => esc_html__("Money", 'trx_utils'),
					"desc" => wp_kses_data( __("Money value (dot or comma separated)", 'trx_utils') ),
					"value" => "",
					"type" => "text"
				),
				"currency" => array(
					"title" => esc_html__("Currency", 'trx_utils'),
					"desc" => wp_kses_data( __("Currency character", 'trx_utils') ),
					"value" => "$",
					"type" => "text"
				),
				"period" => array(
					"title" => esc_html__("Period", 'trx_utils'),
					"desc" => wp_kses_data( __("Period text (if need). For example: monthly, daily, etc.", 'trx_utils') ),
					"value" => "",
					"type" => "text"
				),
				"align" => array(
					"title" => esc_html__("Alignment", 'trx_utils'),
					"desc" => wp_kses_data( __("Align price to left or right side", 'trx_utils') ),
					"value" => "",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => reisen_get_sc_param('float')
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
if ( !function_exists( 'reisen_sc_price_reg_shortcodes_vc' ) ) {
	//add_action('reisen_action_shortcodes_list_vc', 'reisen_sc_price_reg_shortcodes_vc');
	function reisen_sc_price_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_price",
			"name" => esc_html__("Price", 'trx_utils'),
			"description" => wp_kses_data( __("Insert price with decoration", 'trx_utils') ),
			"category" => esc_html__('Content', 'trx_utils'),
			'icon' => 'icon_trx_price',
			"class" => "trx_sc_single trx_sc_price",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "money",
					"heading" => esc_html__("Money", 'trx_utils'),
					"description" => wp_kses_data( __("Money value (dot or comma separated)", 'trx_utils') ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "currency",
					"heading" => esc_html__("Currency symbol", 'trx_utils'),
					"description" => wp_kses_data( __("Currency character", 'trx_utils') ),
					"admin_label" => true,
					"class" => "",
					"value" => "$",
					"type" => "textfield"
				),
				array(
					"param_name" => "period",
					"heading" => esc_html__("Period", 'trx_utils'),
					"description" => wp_kses_data( __("Period text (if need). For example: monthly, daily, etc.", 'trx_utils') ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "align",
					"heading" => esc_html__("Alignment", 'trx_utils'),
					"description" => wp_kses_data( __("Align price to left or right side", 'trx_utils') ),
					"admin_label" => true,
					"class" => "",
					"value" => array_flip(reisen_get_sc_param('float')),
					"type" => "dropdown"
				),
				reisen_get_vc_param('id'),
				reisen_get_vc_param('class'),
				reisen_get_vc_param('css'),
				reisen_get_vc_param('margin_top'),
				reisen_get_vc_param('margin_bottom'),
				reisen_get_vc_param('margin_left'),
				reisen_get_vc_param('margin_right')
			)
		) );
		
		class WPBakeryShortCode_Trx_Price extends Reisen_Vc_ShortCodeSingle {}
	}
}
?>