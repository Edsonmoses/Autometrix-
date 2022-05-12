<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('reisen_sc_hide_theme_setup')) {
	add_action( 'reisen_action_before_init_theme', 'reisen_sc_hide_theme_setup' );
	function reisen_sc_hide_theme_setup() {
		add_action('reisen_action_shortcodes_list', 		'reisen_sc_hide_reg_shortcodes');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_hide selector="unique_id"]
*/

if (!function_exists('reisen_sc_hide')) {	
	function reisen_sc_hide($atts, $content=null){	
		if (reisen_in_shortcode_blogger()) return '';
		extract(reisen_html_decode(shortcode_atts(array(
			// Individual params
			"selector" => "",
			"hide" => "on",
			"delay" => 0
		), $atts)));
		$selector = trim(chop($selector));
		if (!empty($selector)) {
			reisen_storage_concat('js_code', '
				'.($delay>0 ? 'setTimeout(function() {' : '').'
					jQuery("'.esc_attr($selector).'").' . ($hide=='on' ? 'hide' : 'show') . '();
				'.($delay>0 ? '},'.($delay).');' : '').'
			');
		}
		return apply_filters('reisen_shortcode_output', $output, 'trx_hide', $atts, $content);
	}
	add_shortcode('trx_hide', 'reisen_sc_hide');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'reisen_sc_hide_reg_shortcodes' ) ) {
	//add_action('reisen_action_shortcodes_list', 'reisen_sc_hide_reg_shortcodes');
	function reisen_sc_hide_reg_shortcodes() {
	
		reisen_sc_map("trx_hide", array(
			"title" => esc_html__("Hide/Show any block", 'trx_utils'),
			"desc" => wp_kses_data( __("Hide or Show any block with desired CSS-selector", 'trx_utils') ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"selector" => array(
					"title" => esc_html__("Selector", 'trx_utils'),
					"desc" => wp_kses_data( __("Any block's CSS-selector", 'trx_utils') ),
					"value" => "",
					"type" => "text"
				),
				"hide" => array(
					"title" => esc_html__("Hide or Show", 'trx_utils'),
					"desc" => wp_kses_data( __("New state for the block: hide or show", 'trx_utils') ),
					"value" => "yes",
					"size" => "small",
					"options" => reisen_get_sc_param('yes_no'),
					"type" => "switch"
				)
			)
		));
	}
}
?>