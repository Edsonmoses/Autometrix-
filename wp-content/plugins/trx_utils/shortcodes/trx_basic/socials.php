<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('reisen_sc_socials_theme_setup')) {
	add_action( 'reisen_action_before_init_theme', 'reisen_sc_socials_theme_setup' );
	function reisen_sc_socials_theme_setup() {
		add_action('reisen_action_shortcodes_list', 		'reisen_sc_socials_reg_shortcodes');
		if (function_exists('reisen_exists_visual_composer') && reisen_exists_visual_composer())
			add_action('reisen_action_shortcodes_list_vc','reisen_sc_socials_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_socials id="unique_id" size="small"]
	[trx_social_item name="facebook" url="profile url" icon="path for the icon"]
	[trx_social_item name="twitter" url="profile url"]
[/trx_socials]
*/

if (!function_exists('reisen_sc_socials')) {	
	function reisen_sc_socials($atts, $content=null){	
		if (reisen_in_shortcode_blogger()) return '';
		extract(reisen_html_decode(shortcode_atts(array(
			// Individual params
			"size" => "small",		// tiny | small | medium | large
			"shape" => "square",	// round | square
			"type" => reisen_get_theme_setting('socials_type'),	// icons | images
			"socials" => "",
			"custom" => "no",
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
		reisen_storage_set('sc_social_data', array(
			'icons' => false,
            'type' => $type
            )
        );
		if (!empty($socials)) {
			$allowed = explode('|', $socials);
			$list = array();
			for ($i=0; $i<count($allowed); $i++) {
				$s = explode('=', $allowed[$i]);
				if (!empty($s[1])) {
					$list[] = array(
						'icon'	=> $type=='images' ? reisen_get_socials_url($s[0]) : 'icon-'.trim($s[0]),
						'url'	=> $s[1]
						);
				}
			}
			if (count($list) > 0) reisen_storage_set_array('sc_social_data', 'icons', $list);
		} else if (reisen_param_is_on($custom))
			$content = do_shortcode($content);
		if (reisen_storage_get_array('sc_social_data', 'icons')===false) reisen_storage_set_array('sc_social_data', 'icons', reisen_get_custom_option('social_icons'));
		$output = reisen_prepare_socials(reisen_storage_get_array('sc_social_data', 'icons'));
		$output = $output
			? '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
				. ' class="sc_socials sc_socials_type_' . esc_attr($type) . ' sc_socials_shape_' . esc_attr($shape) . ' sc_socials_size_' . esc_attr($size) . (!empty($class) ? ' '.esc_attr($class) : '') . '"' 
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
				. (!reisen_param_is_off($animation) ? ' data-animation="'.esc_attr(reisen_get_animation_classes($animation)).'"' : '')
				. '>' 
				. ($output)
				. '</div>'
			: '';
		return apply_filters('reisen_shortcode_output', $output, 'trx_socials', $atts, $content);
	}
	add_shortcode('trx_socials', 'reisen_sc_socials');
}


if (!function_exists('reisen_sc_social_item')) {	
	function reisen_sc_social_item($atts, $content=null){	
		if (reisen_in_shortcode_blogger()) return '';
		extract(reisen_html_decode(shortcode_atts(array(
			// Individual params
			"name" => "",
			"url" => "",
			"icon" => ""
		), $atts)));
		if (empty($icon)) {
			if (!empty($name)) {
				$type = reisen_storage_get_array('sc_social_data', 'type');
				if ($type=='images') {
					if (file_exists(reisen_get_socials_dir($name.'.png')))
						$icon = reisen_get_socials_url($name.'.png');
				} else
					$icon = 'icon-'.esc_attr($name);
			}
		} else if ((int) $icon > 0) {
			$attach = wp_get_attachment_image_src( $icon, 'full' );
			if (isset($attach[0]) && $attach[0]!='')
				$icon = $attach[0];
		}
		if (!empty($icon) && !empty($url)) {
			if (reisen_storage_get_array('sc_social_data', 'icons')===false) reisen_storage_set_array('sc_social_data', 'icons', array());
			reisen_storage_set_array2('sc_social_data', 'icons', '', array(
				'icon' => $icon,
				'url' => $url
				)
			);
		}
		return '';
	}
	add_shortcode('trx_social_item', 'reisen_sc_social_item');
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'reisen_sc_socials_reg_shortcodes' ) ) {
	//add_action('reisen_action_shortcodes_list', 'reisen_sc_socials_reg_shortcodes');
	function reisen_sc_socials_reg_shortcodes() {
	
		reisen_sc_map("trx_socials", array(
			"title" => esc_html__("Social icons", 'trx_utils'),
			"desc" => wp_kses_data( __("List of social icons (with hovers)", 'trx_utils') ),
			"decorate" => true,
			"container" => false,
			"params" => array(
				"type" => array(
					"title" => esc_html__("Icon's type", 'trx_utils'),
					"desc" => wp_kses_data( __("Type of the icons - images or font icons", 'trx_utils') ),
					"value" => reisen_get_theme_setting('socials_type'),
					"options" => array(
						'icons' => esc_html__('Icons', 'trx_utils'),
						'images' => esc_html__('Images', 'trx_utils')
					),
					"type" => "checklist"
				), 
				"size" => array(
					"title" => esc_html__("Icon's size", 'trx_utils'),
					"desc" => wp_kses_data( __("Size of the icons", 'trx_utils') ),
					"value" => "small",
					"options" => reisen_get_sc_param('sizes'),
					"type" => "checklist"
				), 
				"shape" => array(
					"title" => esc_html__("Icon's shape", 'trx_utils'),
					"desc" => wp_kses_data( __("Shape of the icons", 'trx_utils') ),
					"value" => "square",
					"options" => reisen_get_sc_param('shapes'),
					"type" => "checklist"
				), 
				"socials" => array(
					"title" => esc_html__("Manual socials list", 'trx_utils'),
					"desc" => wp_kses_data( __("Custom list of social networks. For example: twitter=http://twitter.com/my_profile|facebook=http://facebook.com/my_profile. If empty - use socials from Theme options.", 'trx_utils') ),
					"divider" => true,
					"value" => "",
					"type" => "text"
				),
				"custom" => array(
					"title" => esc_html__("Custom socials", 'trx_utils'),
					"desc" => wp_kses_data( __("Make custom icons from inner shortcodes (prepare it on tabs)", 'trx_utils') ),
					"divider" => true,
					"value" => "no",
					"options" => reisen_get_sc_param('yes_no'),
					"type" => "switch"
				),
				"top" => reisen_get_sc_param('top'),
				"bottom" => reisen_get_sc_param('bottom'),
				"left" => reisen_get_sc_param('left'),
				"right" => reisen_get_sc_param('right'),
				"id" => reisen_get_sc_param('id'),
				"class" => reisen_get_sc_param('class'),
				"animation" => reisen_get_sc_param('animation'),
				"css" => reisen_get_sc_param('css')
			),
			"children" => array(
				"name" => "trx_social_item",
				"title" => esc_html__("Custom social item", 'trx_utils'),
				"desc" => wp_kses_data( __("Custom social item: name, profile url and icon url", 'trx_utils') ),
				"decorate" => false,
				"container" => false,
				"params" => array(
					"name" => array(
						"title" => esc_html__("Social name", 'trx_utils'),
						"desc" => wp_kses_data( __("Name (slug) of the social network (twitter, facebook, linkedin, etc.)", 'trx_utils') ),
						"value" => "",
						"type" => "text"
					),
					"url" => array(
						"title" => esc_html__("Your profile URL", 'trx_utils'),
						"desc" => wp_kses_data( __("URL of your profile in specified social network", 'trx_utils') ),
						"value" => "",
						"type" => "text"
					),
					"icon" => array(
						"title" => esc_html__("URL (source) for icon file", 'trx_utils'),
						"desc" => wp_kses_data( __("Select or upload image or write URL from other site for the current social icon", 'trx_utils') ),
						"readonly" => false,
						"value" => "",
						"type" => "media"
					)
				)
			)
		));
	}
}


/* Register shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'reisen_sc_socials_reg_shortcodes_vc' ) ) {
	//add_action('reisen_action_shortcodes_list_vc', 'reisen_sc_socials_reg_shortcodes_vc');
	function reisen_sc_socials_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_socials",
			"name" => esc_html__("Social icons", 'trx_utils'),
			"description" => wp_kses_data( __("Custom social icons", 'trx_utils') ),
			"category" => esc_html__('Content', 'trx_utils'),
			'icon' => 'icon_trx_socials',
			"class" => "trx_sc_collection trx_sc_socials",
			"content_element" => true,
			"is_container" => true,
			"show_settings_on_create" => true,
			"as_parent" => array('only' => 'trx_social_item'),
			"params" => array_merge(array(
				array(
					"param_name" => "type",
					"heading" => esc_html__("Icon's type", 'trx_utils'),
					"description" => wp_kses_data( __("Type of the icons - images or font icons", 'trx_utils') ),
					"class" => "",
					"std" => reisen_get_theme_setting('socials_type'),
					"value" => array(
						esc_html__('Icons', 'trx_utils') => 'icons',
						esc_html__('Images', 'trx_utils') => 'images'
					),
					"type" => "dropdown"
				),
				array(
					"param_name" => "size",
					"heading" => esc_html__("Icon's size", 'trx_utils'),
					"description" => wp_kses_data( __("Size of the icons", 'trx_utils') ),
					"class" => "",
					"std" => "small",
					"value" => array_flip(reisen_get_sc_param('sizes')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "shape",
					"heading" => esc_html__("Icon's shape", 'trx_utils'),
					"description" => wp_kses_data( __("Shape of the icons", 'trx_utils') ),
					"class" => "",
					"std" => "square",
					"value" => array_flip(reisen_get_sc_param('shapes')),
					"type" => "dropdown"
				),
				array(
					"param_name" => "socials",
					"heading" => esc_html__("Manual socials list", 'trx_utils'),
					"description" => wp_kses_data( __("Custom list of social networks. For example: twitter=http://twitter.com/my_profile|facebook=http://facebook.com/my_profile. If empty - use socials from Theme options.", 'trx_utils') ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "custom",
					"heading" => esc_html__("Custom socials", 'trx_utils'),
					"description" => wp_kses_data( __("Make custom icons from inner shortcodes (prepare it on tabs)", 'trx_utils') ),
					"class" => "",
					"value" => array(esc_html__('Custom socials', 'trx_utils') => 'yes'),
					"type" => "checkbox"
				),
				reisen_get_vc_param('id'),
				reisen_get_vc_param('class'),
				reisen_get_vc_param('animation'),
				reisen_get_vc_param('css'),
				reisen_get_vc_param('margin_top'),
				reisen_get_vc_param('margin_bottom'),
				reisen_get_vc_param('margin_left'),
				reisen_get_vc_param('margin_right')
			))
		) );
		
		
		vc_map( array(
			"base" => "trx_social_item",
			"name" => esc_html__("Custom social item", 'trx_utils'),
			"description" => wp_kses_data( __("Custom social item: name, profile url and icon url", 'trx_utils') ),
			"show_settings_on_create" => true,
			"content_element" => true,
			"is_container" => false,
			'icon' => 'icon_trx_social_item',
			"class" => "trx_sc_single trx_sc_social_item",
			"as_child" => array('only' => 'trx_socials'),
			"as_parent" => array('except' => 'trx_socials'),
			"params" => array(
				array(
					"param_name" => "name",
					"heading" => esc_html__("Social name", 'trx_utils'),
					"description" => wp_kses_data( __("Name (slug) of the social network (twitter, facebook, linkedin, etc.)", 'trx_utils') ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "url",
					"heading" => esc_html__("Your profile URL", 'trx_utils'),
					"description" => wp_kses_data( __("URL of your profile in specified social network", 'trx_utils') ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "icon",
					"heading" => esc_html__("URL (source) for icon file", 'trx_utils'),
					"description" => wp_kses_data( __("Select or upload image or write URL from other site for the current social icon", 'trx_utils') ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "attach_image"
				)
			)
		) );
		
		class WPBakeryShortCode_Trx_Socials extends Reisen_Vc_ShortCodeCollection {}
		class WPBakeryShortCode_Trx_Social_Item extends Reisen_Vc_ShortCodeSingle {}
	}
}
?>