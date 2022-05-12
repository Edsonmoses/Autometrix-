<?php
if (!function_exists('reisen_theme_shortcodes_setup')) {
	add_action( 'reisen_action_before_init_theme', 'reisen_theme_shortcodes_setup', 1 );
	function reisen_theme_shortcodes_setup() {
		add_filter('reisen_filter_googlemap_styles', 'reisen_theme_shortcodes_googlemap_styles');
	}
}


// Add theme-specific Google map styles
if ( !function_exists( 'reisen_theme_shortcodes_googlemap_styles' ) ) {
	function reisen_theme_shortcodes_googlemap_styles($list) {
		$list['simple']		= esc_html__('Simple', 'reisen');
		$list['greyscale']	= esc_html__('Greyscale', 'reisen');
		$list['inverse']	= esc_html__('Inverse', 'reisen');
		$list['apple']		= esc_html__('Apple', 'reisen');
		return $list;
	}
}
?>