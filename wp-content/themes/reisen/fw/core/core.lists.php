<?php
/**
 * Reisen Framework: return lists
 *
 * @package reisen
 * @since reisen 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }



// Return styles list
if ( !function_exists( 'reisen_get_list_styles' ) ) {
	function reisen_get_list_styles($from=1, $to=2, $prepend_inherit=false) {
		$list = array();
		for ($i=$from; $i<=$to; $i++)
			$list[$i] = sprintf(esc_html__('Style %d', 'reisen'), $i);
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}


// Return list of the shortcodes margins
if ( !function_exists( 'reisen_get_list_margins' ) ) {
	function reisen_get_list_margins($prepend_inherit=false) {
		if (($list = reisen_storage_get('list_margins'))=='') {
			$list = array(
				'null'		=> esc_html__('0 (No margin)',	'reisen'),
				'tiny'		=> esc_html__('Tiny',		'reisen'),
				'small'		=> esc_html__('Small',		'reisen'),
				'medium'	=> esc_html__('Medium',		'reisen'),
				'large'		=> esc_html__('Large',		'reisen'),
				'huge'		=> esc_html__('Huge',		'reisen'),
				'tiny-'		=> esc_html__('Tiny (negative)',	'reisen'),
				'small-'	=> esc_html__('Small (negative)',	'reisen'),
				'medium-'	=> esc_html__('Medium (negative)',	'reisen'),
				'large-'	=> esc_html__('Large (negative)',	'reisen'),
				'huge-'		=> esc_html__('Huge (negative)',	'reisen')
				);
			$list = apply_filters('reisen_filter_list_margins', $list);
			if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set('list_margins', $list);
		}
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}


// Return list of the line styles
if ( !function_exists( 'reisen_get_list_line_styles' ) ) {
	function reisen_get_list_line_styles($prepend_inherit=false) {
		if (($list = reisen_storage_get('list_line_styles'))=='') {
			$list = array(
				'solid'	=> esc_html__('Solid', 'reisen'),
				'dashed'=> esc_html__('Dashed', 'reisen'),
				'dotted'=> esc_html__('Dotted', 'reisen'),
				'double'=> esc_html__('Double', 'reisen'),
				'image'	=> esc_html__('Image', 'reisen')
				);
			$list = apply_filters('reisen_filter_list_line_styles', $list);
			if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set('list_line_styles', $list);
		}
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}


// Return list of the animations
if ( !function_exists( 'reisen_get_list_animations' ) ) {
	function reisen_get_list_animations($prepend_inherit=false) {
		if (($list = reisen_storage_get('list_animations'))=='') {
			$list = array(
				'none'			=> esc_html__('- None -',	'reisen'),
				'bounce'		=> esc_html__('Bounce',		'reisen'),
				'elastic'		=> esc_html__('Elastic',	'reisen'),
				'flash'			=> esc_html__('Flash',		'reisen'),
				'flip'			=> esc_html__('Flip',		'reisen'),
				'pulse'			=> esc_html__('Pulse',		'reisen'),
				'rubberBand'	=> esc_html__('Rubber Band','reisen'),
				'shake'			=> esc_html__('Shake',		'reisen'),
				'swing'			=> esc_html__('Swing',		'reisen'),
				'tada'			=> esc_html__('Tada',		'reisen'),
				'wobble'		=> esc_html__('Wobble',		'reisen')
				);
			$list = apply_filters('reisen_filter_list_animations', $list);
			if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set('list_animations', $list);
		}
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}


// Return list of the enter animations
if ( !function_exists( 'reisen_get_list_animations_in' ) ) {
	function reisen_get_list_animations_in($prepend_inherit=false) {
		if (($list = reisen_storage_get('list_animations_in'))=='') {
			$list = array(
				'none'				=> esc_html__('- None -',			'reisen'),
				'bounceIn'			=> esc_html__('Bounce In',			'reisen'),
				'bounceInUp'		=> esc_html__('Bounce In Up',		'reisen'),
				'bounceInDown'		=> esc_html__('Bounce In Down',		'reisen'),
				'bounceInLeft'		=> esc_html__('Bounce In Left',		'reisen'),
				'bounceInRight'		=> esc_html__('Bounce In Right',	'reisen'),
				'elastic'			=> esc_html__('Elastic In',			'reisen'),
				'fadeIn'			=> esc_html__('Fade In',			'reisen'),
				'fadeInUp'			=> esc_html__('Fade In Up',			'reisen'),
				'fadeInUpSmall'		=> esc_html__('Fade In Up Small',	'reisen'),
				'fadeInUpBig'		=> esc_html__('Fade In Up Big',		'reisen'),
				'fadeInDown'		=> esc_html__('Fade In Down',		'reisen'),
				'fadeInDownBig'		=> esc_html__('Fade In Down Big',	'reisen'),
				'fadeInLeft'		=> esc_html__('Fade In Left',		'reisen'),
				'fadeInLeftBig'		=> esc_html__('Fade In Left Big',	'reisen'),
				'fadeInRight'		=> esc_html__('Fade In Right',		'reisen'),
				'fadeInRightBig'	=> esc_html__('Fade In Right Big',	'reisen'),
				'flipInX'			=> esc_html__('Flip In X',			'reisen'),
				'flipInY'			=> esc_html__('Flip In Y',			'reisen'),
				'lightSpeedIn'		=> esc_html__('Light Speed In',		'reisen'),
				'rotateIn'			=> esc_html__('Rotate In',			'reisen'),
				'rotateInUpLeft'	=> esc_html__('Rotate In Down Left','reisen'),
				'rotateInUpRight'	=> esc_html__('Rotate In Up Right',	'reisen'),
				'rotateInDownLeft'	=> esc_html__('Rotate In Up Left',	'reisen'),
				'rotateInDownRight'	=> esc_html__('Rotate In Down Right','reisen'),
				'rollIn'			=> esc_html__('Roll In',			'reisen'),
				'slideInUp'			=> esc_html__('Slide In Up',		'reisen'),
				'slideInDown'		=> esc_html__('Slide In Down',		'reisen'),
				'slideInLeft'		=> esc_html__('Slide In Left',		'reisen'),
				'slideInRight'		=> esc_html__('Slide In Right',		'reisen'),
				'wipeInLeftTop'		=> esc_html__('Wipe In Left Top',	'reisen'),
				'zoomIn'			=> esc_html__('Zoom In',			'reisen'),
				'zoomInUp'			=> esc_html__('Zoom In Up',			'reisen'),
				'zoomInDown'		=> esc_html__('Zoom In Down',		'reisen'),
				'zoomInLeft'		=> esc_html__('Zoom In Left',		'reisen'),
				'zoomInRight'		=> esc_html__('Zoom In Right',		'reisen')
				);
			$list = apply_filters('reisen_filter_list_animations_in', $list);
			if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set('list_animations_in', $list);
		}
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}


// Return list of the out animations
if ( !function_exists( 'reisen_get_list_animations_out' ) ) {
	function reisen_get_list_animations_out($prepend_inherit=false) {
		if (($list = reisen_storage_get('list_animations_out'))=='') {
			$list = array(
				'none'				=> esc_html__('- None -',			'reisen'),
				'bounceOut'			=> esc_html__('Bounce Out',			'reisen'),
				'bounceOutUp'		=> esc_html__('Bounce Out Up',		'reisen'),
				'bounceOutDown'		=> esc_html__('Bounce Out Down',	'reisen'),
				'bounceOutLeft'		=> esc_html__('Bounce Out Left',	'reisen'),
				'bounceOutRight'	=> esc_html__('Bounce Out Right',	'reisen'),
				'fadeOut'			=> esc_html__('Fade Out',			'reisen'),
				'fadeOutUp'			=> esc_html__('Fade Out Up',		'reisen'),
				'fadeOutUpBig'		=> esc_html__('Fade Out Up Big',	'reisen'),
				'fadeOutDown'		=> esc_html__('Fade Out Down',		'reisen'),
				'fadeOutDownSmall'	=> esc_html__('Fade Out Down Small','reisen'),
				'fadeOutDownBig'	=> esc_html__('Fade Out Down Big',	'reisen'),
				'fadeOutLeft'		=> esc_html__('Fade Out Left',		'reisen'),
				'fadeOutLeftBig'	=> esc_html__('Fade Out Left Big',	'reisen'),
				'fadeOutRight'		=> esc_html__('Fade Out Right',		'reisen'),
				'fadeOutRightBig'	=> esc_html__('Fade Out Right Big',	'reisen'),
				'flipOutX'			=> esc_html__('Flip Out X',			'reisen'),
				'flipOutY'			=> esc_html__('Flip Out Y',			'reisen'),
				'hinge'				=> esc_html__('Hinge Out',			'reisen'),
				'lightSpeedOut'		=> esc_html__('Light Speed Out',	'reisen'),
				'rotateOut'			=> esc_html__('Rotate Out',			'reisen'),
				'rotateOutUpLeft'	=> esc_html__('Rotate Out Down Left','reisen'),
				'rotateOutUpRight'	=> esc_html__('Rotate Out Up Right','reisen'),
				'rotateOutDownLeft'	=> esc_html__('Rotate Out Up Left',	'reisen'),
				'rotateOutDownRight'=> esc_html__('Rotate Out Down Right','reisen'),
				'rollOut'			=> esc_html__('Roll Out',			'reisen'),
				'slideOutUp'		=> esc_html__('Slide Out Up',		'reisen'),
				'slideOutDown'		=> esc_html__('Slide Out Down',		'reisen'),
				'slideOutLeft'		=> esc_html__('Slide Out Left',		'reisen'),
				'slideOutRight'		=> esc_html__('Slide Out Right',	'reisen'),
				'zoomOut'			=> esc_html__('Zoom Out',			'reisen'),
				'zoomOutUp'			=> esc_html__('Zoom Out Up',		'reisen'),
				'zoomOutDown'		=> esc_html__('Zoom Out Down',		'reisen'),
				'zoomOutLeft'		=> esc_html__('Zoom Out Left',		'reisen'),
				'zoomOutRight'		=> esc_html__('Zoom Out Right',		'reisen')
				);
			$list = apply_filters('reisen_filter_list_animations_out', $list);
			if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set('list_animations_out', $list);
		}
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}

// Return classes list for the specified animation
if (!function_exists('reisen_get_animation_classes')) {
	function reisen_get_animation_classes($animation, $speed='normal', $loop='none') {
		return reisen_param_is_off($animation) ? '' : 'animated '.esc_attr($animation).' '.esc_attr($speed).(!reisen_param_is_off($loop) ? ' '.esc_attr($loop) : '');
	}
}


// Return list of the main menu hover effects
if ( !function_exists( 'reisen_get_list_menu_hovers' ) ) {
	function reisen_get_list_menu_hovers($prepend_inherit=false) {
		if (($list = reisen_storage_get('list_menu_hovers'))=='') {
			$list = array(
				'fade'			=> esc_html__('Fade',		'reisen'),
				'slide_line'	=> esc_html__('Slide Line',	'reisen'),
				'slide_box'		=> esc_html__('Slide Box',	'reisen'),
				'zoom_line'		=> esc_html__('Zoom Line',	'reisen'),
				'path_line'		=> esc_html__('Path Line',	'reisen'),
				'roll_down'		=> esc_html__('Roll Down',	'reisen'),
				'color_line'	=> esc_html__('Color Line',	'reisen'),
				);
			$list = apply_filters('reisen_filter_list_menu_hovers', $list);
			if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set('list_menu_hovers', $list);
		}
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}


// Return list of the button's hover effects
if ( !function_exists( 'reisen_get_list_button_hovers' ) ) {
	function reisen_get_list_button_hovers($prepend_inherit=false) {
		if (($list = reisen_storage_get('list_button_hovers'))=='') {
			$list = array(
				'default'		=> esc_html__('Default',			'reisen'),
				'fade'			=> esc_html__('Fade',				'reisen'),
				'slide_left'	=> esc_html__('Slide from Left',	'reisen'),
				'slide_top'		=> esc_html__('Slide from Top',		'reisen'),
				'arrow'			=> esc_html__('Arrow',				'reisen'),
				);
			$list = apply_filters('reisen_filter_list_button_hovers', $list);
			if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set('list_button_hovers', $list);
		}
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}


// Return list of the input field's hover effects
if ( !function_exists( 'reisen_get_list_input_hovers' ) ) {
	function reisen_get_list_input_hovers($prepend_inherit=false) {
		if (($list = reisen_storage_get('list_input_hovers'))=='') {
			$list = array(
				'default'	=> esc_html__('Default',	'reisen'),
				'accent'	=> esc_html__('Accented',	'reisen'),
				'path'		=> esc_html__('Path',		'reisen'),
				'jump'		=> esc_html__('Jump',		'reisen'),
				'underline'	=> esc_html__('Underline',	'reisen'),
				'iconed'	=> esc_html__('Iconed',		'reisen'),
				);
			$list = apply_filters('reisen_filter_list_input_hovers', $list);
			if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set('list_input_hovers', $list);
		}
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}


// Return list of the search field's styles
if ( !function_exists( 'reisen_get_list_search_styles' ) ) {
	function reisen_get_list_search_styles($prepend_inherit=false) {
		if (($list = reisen_storage_get('list_search_styles'))=='') {
			$list = array(
				'default'	=> esc_html__('Default',	'reisen'),
				'fullscreen'=> esc_html__('Fullscreen',	'reisen'),
				'slide'		=> esc_html__('Slide',		'reisen'),
				'expand'	=> esc_html__('Expand',		'reisen'),
				);
			$list = apply_filters('reisen_filter_list_search_styles', $list);
			if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set('list_search_styles', $list);
		}
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}


// Return list of categories
if ( !function_exists( 'reisen_get_list_categories' ) ) {
	function reisen_get_list_categories($prepend_inherit=false) {
		if (($list = reisen_storage_get('list_categories'))=='') {
			$list = array();
			$args = array(
				'type'                     => 'post',
				'child_of'                 => 0,
				'parent'                   => '',
				'orderby'                  => 'name',
				'order'                    => 'ASC',
				'hide_empty'               => 0,
				'hierarchical'             => 1,
				'exclude'                  => '',
				'include'                  => '',
				'number'                   => '',
				'taxonomy'                 => 'category',
				'pad_counts'               => false );
			$taxonomies = get_categories( $args );
			if (is_array($taxonomies) && count($taxonomies) > 0) {
				foreach ($taxonomies as $cat) {
					$list[$cat->term_id] = $cat->name;
				}
			}
			if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set('list_categories', $list);
		}
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}


// Return list of taxonomies
if ( !function_exists( 'reisen_get_list_terms' ) ) {
	function reisen_get_list_terms($prepend_inherit=false, $taxonomy='category') {
		if (($list = reisen_storage_get('list_taxonomies_'.($taxonomy)))=='') {
			$list = array();
			if ( is_array($taxonomy) || taxonomy_exists($taxonomy) ) {
				$terms = get_terms( $taxonomy, array(
					'child_of'                 => 0,
					'parent'                   => '',
					'orderby'                  => 'name',
					'order'                    => 'ASC',
					'hide_empty'               => 0,
					'hierarchical'             => 1,
					'exclude'                  => '',
					'include'                  => '',
					'number'                   => '',
					'taxonomy'                 => $taxonomy,
					'pad_counts'               => false
					)
				);
			} else {
				$terms = reisen_get_terms_by_taxonomy_from_db($taxonomy);
			}
			if (!is_wp_error( $terms ) && is_array($terms) && count($terms) > 0) {
				foreach ($terms as $cat) {
					$list[$cat->term_id] = $cat->name;
				}
			}
			if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set('list_taxonomies_'.($taxonomy), $list);
		}
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}

// Return list of post's types
if ( !function_exists( 'reisen_get_list_posts_types' ) ) {
	function reisen_get_list_posts_types($prepend_inherit=false) {
		if (($list = reisen_storage_get('list_posts_types'))=='') {
			// Return only theme inheritance supported post types
			$list = apply_filters('reisen_filter_list_post_types', $list);
			if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set('list_posts_types', $list);
		}
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}


// Return list post items from any post type and taxonomy
if ( !function_exists( 'reisen_get_list_posts' ) ) {
	function reisen_get_list_posts($prepend_inherit=false, $opt=array()) {
		$opt = array_merge(array(
			'post_type'			=> 'post',
			'post_status'		=> 'publish',
			'taxonomy'			=> 'category',
			'taxonomy_value'	=> '',
			'posts_per_page'	=> -1,
			'orderby'			=> 'post_date',
			'order'				=> 'desc',
			'return'			=> 'id'
			), is_array($opt) ? $opt : array('post_type'=>$opt));

		$hash = 'list_posts_'.($opt['post_type']).'_'.($opt['taxonomy']).'_'.($opt['taxonomy_value']).'_'.($opt['orderby']).'_'.($opt['order']).'_'.($opt['return']).'_'.($opt['posts_per_page']);
		if (($list = reisen_storage_get($hash))=='') {
			$list = array();
			$list['none'] = esc_html__("- Not selected -", 'reisen');
			$args = array(
				'post_type' => $opt['post_type'],
				'post_status' => $opt['post_status'],
				'posts_per_page' => $opt['posts_per_page'],
				'ignore_sticky_posts' => true,
				'orderby'	=> $opt['orderby'],
				'order'		=> $opt['order']
			);
			if (!empty($opt['taxonomy_value'])) {
				$args['tax_query'] = array(
					array(
						'taxonomy' => $opt['taxonomy'],
						'field' => (int) $opt['taxonomy_value'] > 0 ? 'id' : 'slug',
						'terms' => $opt['taxonomy_value']
					)
				);
			}
			$posts = get_posts( $args );
			if (is_array($posts) && count($posts) > 0) {
				foreach ($posts as $post) {
					$list[$opt['return']=='id' ? $post->ID : $post->post_title] = $post->post_title;
				}
			}
			if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set($hash, $list);
		}
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}


// Return list pages
if ( !function_exists( 'reisen_get_list_pages' ) ) {
	function reisen_get_list_pages($prepend_inherit=false, $opt=array()) {
		$opt = array_merge(array(
			'post_type'			=> 'page',
			'post_status'		=> 'publish',
			'posts_per_page'	=> -1,
			'orderby'			=> 'title',
			'order'				=> 'asc',
			'return'			=> 'id'
			), is_array($opt) ? $opt : array('post_type'=>$opt));
		return reisen_get_list_posts($prepend_inherit, $opt);
	}
}


// Return list of registered users
if ( !function_exists( 'reisen_get_list_users' ) ) {
	function reisen_get_list_users($prepend_inherit=false, $roles=array('administrator', 'editor', 'author', 'contributor', 'shop_manager')) {
		if (($list = reisen_storage_get('list_users'))=='') {
			$list = array();
			$list['none'] = esc_html__("- Not selected -", 'reisen');
			$args = array(
				'orderby'	=> 'display_name',
				'order'		=> 'ASC' );
			$users = get_users( $args );
			if (is_array($users) && count($users) > 0) {
				foreach ($users as $user) {
					$accept = true;
					if (is_array($user->roles)) {
						if (is_array($user->roles) && count($user->roles) > 0) {
							$accept = false;
							foreach ($user->roles as $role) {
								if (in_array($role, $roles)) {
									$accept = true;
									break;
								}
							}
						}
					}
					if ($accept) $list[$user->user_login] = $user->display_name;
				}
			}
			if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set('list_users', $list);
		}
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}


// Return slider engines list, prepended inherit (if need)
if ( !function_exists( 'reisen_get_list_sliders' ) ) {
	function reisen_get_list_sliders($prepend_inherit=false) {
		if (($list = reisen_storage_get('list_sliders'))=='') {
			$list = array(
				'swiper' => esc_html__("Posts slider (Swiper)", 'reisen')
			);
			$list = apply_filters('reisen_filter_list_sliders', $list);
			if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set('list_sliders', $list);
		}
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}


// Return slider controls list, prepended inherit (if need)
if ( !function_exists( 'reisen_get_list_slider_controls' ) ) {
	function reisen_get_list_slider_controls($prepend_inherit=false) {
		if (($list = reisen_storage_get('list_slider_controls'))=='') {
			$list = array(
				'no'		=> esc_html__('None', 'reisen'),
				'side'		=> esc_html__('Side', 'reisen'),
				'bottom'	=> esc_html__('Bottom', 'reisen'),
				'pagination'=> esc_html__('Pagination', 'reisen')
				);
			$list = apply_filters('reisen_filter_list_slider_controls', $list);
			if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set('list_slider_controls', $list);
		}
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}


// Return slider controls classes
if ( !function_exists( 'reisen_get_slider_controls_classes' ) ) {
	function reisen_get_slider_controls_classes($controls) {
		if (reisen_param_is_off($controls))	$classes = 'sc_slider_nopagination sc_slider_nocontrols';
		else if ($controls=='bottom')			$classes = 'sc_slider_nopagination sc_slider_controls sc_slider_controls_bottom';
		else if ($controls=='pagination')		$classes = 'sc_slider_pagination sc_slider_pagination_bottom sc_slider_nocontrols';
		else									$classes = 'sc_slider_nopagination sc_slider_controls sc_slider_controls_side';
		return $classes;
	}
}

// Return list with popup engines
if ( !function_exists( 'reisen_get_list_popup_engines' ) ) {
	function reisen_get_list_popup_engines($prepend_inherit=false) {
		if (($list = reisen_storage_get('list_popup_engines'))=='') {
			$list = array(
				"pretty"	=> esc_html__("Pretty photo", 'reisen'),
				"magnific"	=> esc_html__("Magnific popup", 'reisen')
				);
			$list = apply_filters('reisen_filter_list_popup_engines', $list);
			if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set('list_popup_engines', $list);
		}
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}

// Return menus list, prepended inherit
if ( !function_exists( 'reisen_get_list_menus' ) ) {
	function reisen_get_list_menus($prepend_inherit=false) {
		if (($list = reisen_storage_get('list_menus'))=='') {
			$list = array();
			$list['default'] = esc_html__("Default", 'reisen');
			$menus = wp_get_nav_menus();
			if (is_array($menus) && count($menus) > 0) {
				foreach ($menus as $menu) {
					$list[$menu->slug] = $menu->name;
				}
			}
			if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set('list_menus', $list);
		}
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}

// Return custom sidebars list, prepended inherit and main sidebars item (if need)
if ( !function_exists( 'reisen_get_list_sidebars' ) ) {
	function reisen_get_list_sidebars($prepend_inherit=false) {
		if (($list = reisen_storage_get('list_sidebars'))=='') {
			if (($list = reisen_storage_get('registered_sidebars'))=='') $list = array();
			if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set('list_sidebars', $list);
		}
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}

// Return sidebars positions
if ( !function_exists( 'reisen_get_list_sidebars_positions' ) ) {
	function reisen_get_list_sidebars_positions($prepend_inherit=false) {
		if (($list = reisen_storage_get('list_sidebars_positions'))=='') {
			$list = array(
				'none'  => esc_html__('Hide',  'reisen'),
				'left'  => esc_html__('Left',  'reisen'),
				'right' => esc_html__('Right', 'reisen')
				);
			if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set('list_sidebars_positions', $list);
		}
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}

// Return sidebars class
if ( !function_exists( 'reisen_get_sidebar_class' ) ) {
	function reisen_get_sidebar_class() {
		$sb_main = reisen_get_custom_option('show_sidebar_main');
		$sb_outer = reisen_get_custom_option('show_sidebar_outer');
        $sidebar_name   = reisen_get_custom_option('sidebar_main');
        $sb_classes = ' ' . (reisen_param_is_off($sb_outer) ? 'sidebar_outer_hide' : 'sidebar_outer_show sidebar_outer_'.($sb_outer));

        if ($sb_main == 'none')
            return 'sidebar_hide';
        else if(is_active_sidebar($sidebar_name))
            return  'sidebar_show sidebar_'.($sb_main) . $sb_classes;
	}
}

// Return body styles list, prepended inherit
if ( !function_exists( 'reisen_get_list_body_styles' ) ) {
	function reisen_get_list_body_styles($prepend_inherit=false) {
		if (($list = reisen_storage_get('list_body_styles'))=='') {
			$list = array(
				'boxed'	=> esc_html__('Boxed',		'reisen'),
				'wide'	=> esc_html__('Wide',		'reisen')
				);
			if (reisen_get_theme_setting('allow_fullscreen')) {
				$list['fullwide']	= esc_html__('Fullwide',	'reisen');
				$list['fullscreen']	= esc_html__('Fullscreen',	'reisen');
			}
			$list = apply_filters('reisen_filter_list_body_styles', $list);
			if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set('list_body_styles', $list);
		}
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}

// Return templates list, prepended inherit
if ( !function_exists( 'reisen_get_list_templates' ) ) {
	function reisen_get_list_templates($mode='') {
		if (($list = reisen_storage_get('list_templates_'.($mode)))=='') {
			$list = array();
			$tpl = reisen_storage_get('registered_templates');
			if (is_array($tpl) && count($tpl) > 0) {
				foreach ($tpl as $k=>$v) {
					if ($mode=='' || in_array($mode, explode(',', $v['mode'])))
						$list[$k] = !empty($v['icon']) 
									? $v['icon'] 
									: (!empty($v['title']) 
										? $v['title'] 
										: reisen_strtoproper($v['layout'])
										);
				}
			}
			if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set('list_templates_'.($mode), $list);
		}
		return $list;
	}
}

// Return blog styles list, prepended inherit
if ( !function_exists( 'reisen_get_list_templates_blog' ) ) {
	function reisen_get_list_templates_blog($prepend_inherit=false) {
		if (($list = reisen_storage_get('list_templates_blog'))=='') {
			$list = reisen_get_list_templates('blog');
			if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set('list_templates_blog', $list);
		}
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}

// Return blogger styles list, prepended inherit
if ( !function_exists( 'reisen_get_list_templates_blogger' ) ) {
	function reisen_get_list_templates_blogger($prepend_inherit=false) {
		if (($list = reisen_storage_get('list_templates_blogger'))=='') {
			$list = reisen_array_merge(reisen_get_list_templates('blogger'), reisen_get_list_templates('blog'));
			if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set('list_templates_blogger', $list);
		}
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}

// Return single page styles list, prepended inherit
if ( !function_exists( 'reisen_get_list_templates_single' ) ) {
	function reisen_get_list_templates_single($prepend_inherit=false) {
		if (($list = reisen_storage_get('list_templates_single'))=='') {
			$list = reisen_get_list_templates('single');
			if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set('list_templates_single', $list);
		}
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}

// Return header styles list, prepended inherit
if ( !function_exists( 'reisen_get_list_templates_header' ) ) {
	function reisen_get_list_templates_header($prepend_inherit=false) {
		if (($list = reisen_storage_get('list_templates_header'))=='') {
			$list = reisen_get_list_templates('header');
			if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set('list_templates_header', $list);
		}
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}

// Return form styles list, prepended inherit
if ( !function_exists( 'reisen_get_list_templates_forms' ) ) {
	function reisen_get_list_templates_forms($prepend_inherit=false) {
		if (($list = reisen_storage_get('list_templates_forms'))=='') {
			$list = reisen_get_list_templates('forms');
			if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set('list_templates_forms', $list);
		}
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}

// Return article styles list, prepended inherit
if ( !function_exists( 'reisen_get_list_article_styles' ) ) {
	function reisen_get_list_article_styles($prepend_inherit=false) {
		if (($list = reisen_storage_get('list_article_styles'))=='') {
			$list = array(
				"boxed"   => esc_html__('Boxed', 'reisen'),
				"stretch" => esc_html__('Stretch', 'reisen')
				);
			if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set('list_article_styles', $list);
		}
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}

// Return post-formats filters list, prepended inherit
if ( !function_exists( 'reisen_get_list_post_formats_filters' ) ) {
	function reisen_get_list_post_formats_filters($prepend_inherit=false) {
		if (($list = reisen_storage_get('list_post_formats_filters'))=='') {
			$list = array(
				"no"      => esc_html__('All posts', 'reisen'),
				"thumbs"  => esc_html__('With thumbs', 'reisen'),
				"reviews" => esc_html__('With reviews', 'reisen'),
				"video"   => esc_html__('With videos', 'reisen'),
				"audio"   => esc_html__('With audios', 'reisen'),
				"gallery" => esc_html__('With galleries', 'reisen')
				);
			if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set('list_post_formats_filters', $list);
		}
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}

// Return portfolio filters list, prepended inherit
if ( !function_exists( 'reisen_get_list_portfolio_filters' ) ) {
	function reisen_get_list_portfolio_filters($prepend_inherit=false) {
		if (($list = reisen_storage_get('list_portfolio_filters'))=='') {
			$list = array(
				"hide"		=> esc_html__('Hide', 'reisen'),
				"tags"		=> esc_html__('Tags', 'reisen'),
				"categories"=> esc_html__('Categories', 'reisen')
				);
			if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set('list_portfolio_filters', $list);
		}
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}

// Return hover styles list, prepended inherit
if ( !function_exists( 'reisen_get_list_hovers' ) ) {
	function reisen_get_list_hovers($prepend_inherit=false) {
		if (($list = reisen_storage_get('list_hovers'))=='') {
			$list = array();
			$list['circle effect1']  = esc_html__('Circle Effect 1',  'reisen');
			$list['circle effect2']  = esc_html__('Circle Effect 2',  'reisen');
			$list['circle effect3']  = esc_html__('Circle Effect 3',  'reisen');
			$list['circle effect4']  = esc_html__('Circle Effect 4',  'reisen');
			$list['circle effect5']  = esc_html__('Circle Effect 5',  'reisen');
			$list['circle effect6']  = esc_html__('Circle Effect 6',  'reisen');
			$list['circle effect7']  = esc_html__('Circle Effect 7',  'reisen');
			$list['circle effect8']  = esc_html__('Circle Effect 8',  'reisen');
			$list['circle effect9']  = esc_html__('Circle Effect 9',  'reisen');
			$list['circle effect10'] = esc_html__('Circle Effect 10',  'reisen');
			$list['circle effect11'] = esc_html__('Circle Effect 11',  'reisen');
			$list['circle effect12'] = esc_html__('Circle Effect 12',  'reisen');
			$list['circle effect13'] = esc_html__('Circle Effect 13',  'reisen');
			$list['circle effect14'] = esc_html__('Circle Effect 14',  'reisen');
			$list['circle effect15'] = esc_html__('Circle Effect 15',  'reisen');
			$list['circle effect16'] = esc_html__('Circle Effect 16',  'reisen');
			$list['circle effect17'] = esc_html__('Circle Effect 17',  'reisen');
			$list['circle effect18'] = esc_html__('Circle Effect 18',  'reisen');
			$list['circle effect19'] = esc_html__('Circle Effect 19',  'reisen');
			$list['circle effect20'] = esc_html__('Circle Effect 20',  'reisen');
			$list['square effect1']  = esc_html__('Square Effect 1',  'reisen');
			$list['square effect2']  = esc_html__('Square Effect 2',  'reisen');
			$list['square effect3']  = esc_html__('Square Effect 3',  'reisen');
			$list['square effect5']  = esc_html__('Square Effect 5',  'reisen');
			$list['square effect6']  = esc_html__('Square Effect 6',  'reisen');
			$list['square effect7']  = esc_html__('Square Effect 7',  'reisen');
			$list['square effect8']  = esc_html__('Square Effect 8',  'reisen');
			$list['square effect9']  = esc_html__('Square Effect 9',  'reisen');
			$list['square effect10'] = esc_html__('Square Effect 10',  'reisen');
			$list['square effect11'] = esc_html__('Square Effect 11',  'reisen');
			$list['square effect12'] = esc_html__('Square Effect 12',  'reisen');
			$list['square effect13'] = esc_html__('Square Effect 13',  'reisen');
			$list['square effect14'] = esc_html__('Square Effect 14',  'reisen');
			$list['square effect15'] = esc_html__('Square Effect 15',  'reisen');
			$list['square effect_dir']   = esc_html__('Square Effect Dir',   'reisen');
			$list['square effect_shift'] = esc_html__('Square Effect Shift', 'reisen');
			$list['square effect_book']  = esc_html__('Square Effect Book',  'reisen');
			$list['square effect_more']  = esc_html__('Square Effect More',  'reisen');
			$list['square effect_fade']  = esc_html__('Square Effect Fade',  'reisen');
			$list['square effect_pull']  = esc_html__('Square Effect Pull',  'reisen');
			$list['square effect_slide'] = esc_html__('Square Effect Slide', 'reisen');
			$list['square effect_border'] = esc_html__('Square Effect Border', 'reisen');
			$list = apply_filters('reisen_filter_portfolio_hovers', $list);
			if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set('list_hovers', $list);
		}
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}


// Return list of the blog counters
if ( !function_exists( 'reisen_get_list_blog_counters' ) ) {
	function reisen_get_list_blog_counters($prepend_inherit=false) {
		if (($list = reisen_storage_get('list_blog_counters'))=='') {
			$list = array(
				'views'		=> esc_html__('Views', 'reisen'),
				'likes'		=> esc_html__('Likes', 'reisen'),
				'rating'	=> esc_html__('Rating', 'reisen'),
				'comments'	=> esc_html__('Comments', 'reisen')
				);
			$list = apply_filters('reisen_filter_list_blog_counters', $list);
			if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set('list_blog_counters', $list);
		}
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}

// Return list of the item sizes for the portfolio alter style, prepended inherit
if ( !function_exists( 'reisen_get_list_alter_sizes' ) ) {
	function reisen_get_list_alter_sizes($prepend_inherit=false) {
		if (($list = reisen_storage_get('list_alter_sizes'))=='') {
			$list = array(
					'1_1' => esc_html__('1x1', 'reisen'),
					'1_2' => esc_html__('1x2', 'reisen'),
					'2_1' => esc_html__('2x1', 'reisen'),
					'2_2' => esc_html__('2x2', 'reisen'),
					'1_3' => esc_html__('1x3', 'reisen'),
					'2_3' => esc_html__('2x3', 'reisen'),
					'3_1' => esc_html__('3x1', 'reisen'),
					'3_2' => esc_html__('3x2', 'reisen'),
					'3_3' => esc_html__('3x3', 'reisen')
					);
			$list = apply_filters('reisen_filter_portfolio_alter_sizes', $list);
			if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set('list_alter_sizes', $list);
		}
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}

// Return extended hover directions list, prepended inherit
if ( !function_exists( 'reisen_get_list_hovers_directions' ) ) {
	function reisen_get_list_hovers_directions($prepend_inherit=false) {
		if (($list = reisen_storage_get('list_hovers_directions'))=='') {
			$list = array(
				'left_to_right' => esc_html__('Left to Right',  'reisen'),
				'right_to_left' => esc_html__('Right to Left',  'reisen'),
				'top_to_bottom' => esc_html__('Top to Bottom',  'reisen'),
				'bottom_to_top' => esc_html__('Bottom to Top',  'reisen'),
				'scale_up'      => esc_html__('Scale Up',  'reisen'),
				'scale_down'    => esc_html__('Scale Down',  'reisen'),
				'scale_down_up' => esc_html__('Scale Down-Up',  'reisen'),
				'from_left_and_right' => esc_html__('From Left and Right',  'reisen'),
				'from_top_and_bottom' => esc_html__('From Top and Bottom',  'reisen')
			);
			$list = apply_filters('reisen_filter_portfolio_hovers_directions', $list);
			if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set('list_hovers_directions', $list);
		}
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}


// Return list of the label positions in the custom forms
if ( !function_exists( 'reisen_get_list_label_positions' ) ) {
	function reisen_get_list_label_positions($prepend_inherit=false) {
		if (($list = reisen_storage_get('list_label_positions'))=='') {
			$list = array(
				'top'		=> esc_html__('Top',		'reisen'),
				'bottom'	=> esc_html__('Bottom',		'reisen'),
				'left'		=> esc_html__('Left',		'reisen'),
				'over'		=> esc_html__('Over',		'reisen')
			);
			$list = apply_filters('reisen_filter_label_positions', $list);
			if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set('list_label_positions', $list);
		}
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}


// Return list of the bg image positions
if ( !function_exists( 'reisen_get_list_bg_image_positions' ) ) {
	function reisen_get_list_bg_image_positions($prepend_inherit=false) {
		if (($list = reisen_storage_get('list_bg_image_positions'))=='') {
			$list = array(
				'left top'	   => esc_html__('Left Top', 'reisen'),
				'center top'   => esc_html__("Center Top", 'reisen'),
				'right top'    => esc_html__("Right Top", 'reisen'),
				'left center'  => esc_html__("Left Center", 'reisen'),
				'center center'=> esc_html__("Center Center", 'reisen'),
				'right center' => esc_html__("Right Center", 'reisen'),
				'left bottom'  => esc_html__("Left Bottom", 'reisen'),
				'center bottom'=> esc_html__("Center Bottom", 'reisen'),
				'right bottom' => esc_html__("Right Bottom", 'reisen')
			);
			if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set('list_bg_image_positions', $list);
		}
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}


// Return list of the bg image repeat
if ( !function_exists( 'reisen_get_list_bg_image_repeats' ) ) {
	function reisen_get_list_bg_image_repeats($prepend_inherit=false) {
		if (($list = reisen_storage_get('list_bg_image_repeats'))=='') {
			$list = array(
				'repeat'	=> esc_html__('Repeat', 'reisen'),
				'repeat-x'	=> esc_html__('Repeat X', 'reisen'),
				'repeat-y'	=> esc_html__('Repeat Y', 'reisen'),
				'no-repeat'	=> esc_html__('No Repeat', 'reisen')
			);
			if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set('list_bg_image_repeats', $list);
		}
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}


// Return list of the bg image attachment
if ( !function_exists( 'reisen_get_list_bg_image_attachments' ) ) {
	function reisen_get_list_bg_image_attachments($prepend_inherit=false) {
		if (($list = reisen_storage_get('list_bg_image_attachments'))=='') {
			$list = array(
				'scroll'	=> esc_html__('Scroll', 'reisen'),
				'fixed'		=> esc_html__('Fixed', 'reisen'),
				'local'		=> esc_html__('Local', 'reisen')
			);
			if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set('list_bg_image_attachments', $list);
		}
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}


// Return list of the bg tints
if ( !function_exists( 'reisen_get_list_bg_tints' ) ) {
	function reisen_get_list_bg_tints($prepend_inherit=false) {
		if (($list = reisen_storage_get('list_bg_tints'))=='') {
			$list = array(
				'white'	=> esc_html__('White', 'reisen'),
				'light'	=> esc_html__('Light', 'reisen'),
				'dark'	=> esc_html__('Dark', 'reisen')
			);
			$list = apply_filters('reisen_filter_bg_tints', $list);
			if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set('list_bg_tints', $list);
		}
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}

// Return custom fields types list, prepended inherit
if ( !function_exists( 'reisen_get_list_field_types' ) ) {
	function reisen_get_list_field_types($prepend_inherit=false) {
		if (($list = reisen_storage_get('list_field_types'))=='') {
			$list = array(
				'text'     => esc_html__('Text',  'reisen'),
				'textarea' => esc_html__('Text Area','reisen'),
				'password' => esc_html__('Password',  'reisen'),
				'radio'    => esc_html__('Radio',  'reisen'),
				'checkbox' => esc_html__('Checkbox',  'reisen'),
				'select'   => esc_html__('Select',  'reisen'),
				'date'     => esc_html__('Date','reisen'),
				'time'     => esc_html__('Time','reisen'),
				'button'   => esc_html__('Button','reisen')
			);
			$list = apply_filters('reisen_filter_field_types', $list);
			if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set('list_field_types', $list);
		}
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}

// Return Google map styles
if ( !function_exists( 'reisen_get_list_googlemap_styles' ) ) {
	function reisen_get_list_googlemap_styles($prepend_inherit=false) {
		if (($list = reisen_storage_get('list_googlemap_styles'))=='') {
			$list = array(
				'default' => esc_html__('Default', 'reisen')
			);
			$list = apply_filters('reisen_filter_googlemap_styles', $list);
			if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set('list_googlemap_styles', $list);
		}
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}

// Return iconed classes list
if ( !function_exists( 'reisen_get_list_icons' ) ) {
	function reisen_get_list_icons($prepend_inherit=false) {
		if (($list = reisen_storage_get('list_icons'))=='') {
			$list = reisen_parse_icons_classes(reisen_get_file_dir("css/fontello/css/fontello-codes.css"));
			if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set('list_icons', $list);
		}
		return $prepend_inherit ? array_merge(array('inherit'), $list) : $list;
	}
}

// Return socials list
if ( !function_exists( 'reisen_get_list_socials' ) ) {
	function reisen_get_list_socials($prepend_inherit=false) {
		if (($list = reisen_storage_get('list_socials'))=='') {
			$list = reisen_get_list_images("images/socials", "png");
			if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set('list_socials', $list);
		}
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}

// Return list with 'Yes' and 'No' items
if ( !function_exists( 'reisen_get_list_yesno' ) ) {
	function reisen_get_list_yesno($prepend_inherit=false) {
		$list = array(
			'yes' => esc_html__("Yes", 'reisen'),
			'no'  => esc_html__("No", 'reisen')
		);
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}

// Return list with 'On' and 'Of' items
if ( !function_exists( 'reisen_get_list_onoff' ) ) {
	function reisen_get_list_onoff($prepend_inherit=false) {
		$list = array(
			"on" => esc_html__("On", 'reisen'),
			"off" => esc_html__("Off", 'reisen')
		);
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}

// Return list with 'Show' and 'Hide' items
if ( !function_exists( 'reisen_get_list_showhide' ) ) {
	function reisen_get_list_showhide($prepend_inherit=false) {
		$list = array(
			"show" => esc_html__("Show", 'reisen'),
			"hide" => esc_html__("Hide", 'reisen')
		);
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}

// Return list with 'Ascending' and 'Descending' items
if ( !function_exists( 'reisen_get_list_orderings' ) ) {
	function reisen_get_list_orderings($prepend_inherit=false) {
		$list = array(
			"asc" => esc_html__("Ascending", 'reisen'),
			"desc" => esc_html__("Descending", 'reisen')
		);
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}

// Return list with 'Horizontal' and 'Vertical' items
if ( !function_exists( 'reisen_get_list_directions' ) ) {
	function reisen_get_list_directions($prepend_inherit=false) {
		$list = array(
			"horizontal" => esc_html__("Horizontal", 'reisen'),
			"vertical" => esc_html__("Vertical", 'reisen')
		);
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}

// Return list with item's shapes
if ( !function_exists( 'reisen_get_list_shapes' ) ) {
	function reisen_get_list_shapes($prepend_inherit=false) {
		$list = array(
			"round"  => esc_html__("Round", 'reisen'),
			"square" => esc_html__("Square", 'reisen')
		);
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}

// Return list with item's sizes
if ( !function_exists( 'reisen_get_list_sizes' ) ) {
	function reisen_get_list_sizes($prepend_inherit=false) {
		$list = array(
			"tiny"   => esc_html__("Tiny", 'reisen'),
			"small"  => esc_html__("Small", 'reisen'),
			"medium" => esc_html__("Medium", 'reisen'),
			"large"  => esc_html__("Large", 'reisen')
		);
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}

// Return list with slider (scroll) controls positions
if ( !function_exists( 'reisen_get_list_controls' ) ) {
	function reisen_get_list_controls($prepend_inherit=false) {
		$list = array(
			"hide" => esc_html__("Hide", 'reisen'),
			"side" => esc_html__("Side", 'reisen'),
			"bottom" => esc_html__("Bottom", 'reisen')
		);
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}

// Return list with float items
if ( !function_exists( 'reisen_get_list_floats' ) ) {
	function reisen_get_list_floats($prepend_inherit=false) {
		$list = array(
			"none" => esc_html__("None", 'reisen'),
			"left" => esc_html__("Float Left", 'reisen'),
			"right" => esc_html__("Float Right", 'reisen')
		);
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}

// Return list with alignment items
if ( !function_exists( 'reisen_get_list_alignments' ) ) {
	function reisen_get_list_alignments($justify=false, $prepend_inherit=false) {
		$list = array(
			"none" => esc_html__("None", 'reisen'),
			"left" => esc_html__("Left", 'reisen'),
			"center" => esc_html__("Center", 'reisen'),
			"right" => esc_html__("Right", 'reisen')
		);
		if ($justify) $list["justify"] = esc_html__("Justify", 'reisen');
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}

// Return list with horizontal positions
if ( !function_exists( 'reisen_get_list_hpos' ) ) {
	function reisen_get_list_hpos($prepend_inherit=false, $center=false) {
		$list = array();
		$list['left'] = esc_html__("Left", 'reisen');
		if ($center) $list['center'] = esc_html__("Center", 'reisen');
		$list['right'] = esc_html__("Right", 'reisen');
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}

// Return list with vertical positions
if ( !function_exists( 'reisen_get_list_vpos' ) ) {
	function reisen_get_list_vpos($prepend_inherit=false, $center=false) {
		$list = array();
		$list['top'] = esc_html__("Top", 'reisen');
		if ($center) $list['center'] = esc_html__("Center", 'reisen');
		$list['bottom'] = esc_html__("Bottom", 'reisen');
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}

// Return sorting list items
if ( !function_exists( 'reisen_get_list_sortings' ) ) {
	function reisen_get_list_sortings($prepend_inherit=false) {
		if (($list = reisen_storage_get('list_sortings'))=='') {
			$list = array(
				"date" => esc_html__("Date", 'reisen'),
				"title" => esc_html__("Alphabetically", 'reisen'),
				"views" => esc_html__("Popular (views count)", 'reisen'),
				"comments" => esc_html__("Most commented (comments count)", 'reisen'),
				"author_rating" => esc_html__("Author rating", 'reisen'),
				"users_rating" => esc_html__("Visitors (users) rating", 'reisen'),
				"random" => esc_html__("Random", 'reisen')
			);
			$list = apply_filters('reisen_filter_list_sortings', $list);
			if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set('list_sortings', $list);
		}
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}

// Return list with columns widths
if ( !function_exists( 'reisen_get_list_columns' ) ) {
	function reisen_get_list_columns($prepend_inherit=false) {
		if (($list = reisen_storage_get('list_columns'))=='') {
			$list = array(
				"none" => esc_html__("None", 'reisen'),
				"1_1" => esc_html__("100%", 'reisen'),
				"1_2" => esc_html__("1/2", 'reisen'),
				"1_3" => esc_html__("1/3", 'reisen'),
				"2_3" => esc_html__("2/3", 'reisen'),
				"1_4" => esc_html__("1/4", 'reisen'),
				"3_4" => esc_html__("3/4", 'reisen'),
				"1_5" => esc_html__("1/5", 'reisen'),
				"2_5" => esc_html__("2/5", 'reisen'),
				"3_5" => esc_html__("3/5", 'reisen'),
				"4_5" => esc_html__("4/5", 'reisen'),
				"1_6" => esc_html__("1/6", 'reisen'),
				"5_6" => esc_html__("5/6", 'reisen'),
				"1_7" => esc_html__("1/7", 'reisen'),
				"2_7" => esc_html__("2/7", 'reisen'),
				"3_7" => esc_html__("3/7", 'reisen'),
				"4_7" => esc_html__("4/7", 'reisen'),
				"5_7" => esc_html__("5/7", 'reisen'),
				"6_7" => esc_html__("6/7", 'reisen'),
				"1_8" => esc_html__("1/8", 'reisen'),
				"3_8" => esc_html__("3/8", 'reisen'),
				"5_8" => esc_html__("5/8", 'reisen'),
				"7_8" => esc_html__("7/8", 'reisen'),
				"1_9" => esc_html__("1/9", 'reisen'),
				"2_9" => esc_html__("2/9", 'reisen'),
				"4_9" => esc_html__("4/9", 'reisen'),
				"5_9" => esc_html__("5/9", 'reisen'),
				"7_9" => esc_html__("7/9", 'reisen'),
				"8_9" => esc_html__("8/9", 'reisen'),
				"1_10"=> esc_html__("1/10", 'reisen'),
				"3_10"=> esc_html__("3/10", 'reisen'),
				"7_10"=> esc_html__("7/10", 'reisen'),
				"9_10"=> esc_html__("9/10", 'reisen'),
				"1_11"=> esc_html__("1/11", 'reisen'),
				"2_11"=> esc_html__("2/11", 'reisen'),
				"3_11"=> esc_html__("3/11", 'reisen'),
				"4_11"=> esc_html__("4/11", 'reisen'),
				"5_11"=> esc_html__("5/11", 'reisen'),
				"6_11"=> esc_html__("6/11", 'reisen'),
				"7_11"=> esc_html__("7/11", 'reisen'),
				"8_11"=> esc_html__("8/11", 'reisen'),
				"9_11"=> esc_html__("9/11", 'reisen'),
				"10_11"=> esc_html__("10/11", 'reisen'),
				"1_12"=> esc_html__("1/12", 'reisen'),
				"5_12"=> esc_html__("5/12", 'reisen'),
				"7_12"=> esc_html__("7/12", 'reisen'),
				"10_12"=> esc_html__("10/12", 'reisen'),
				"11_12"=> esc_html__("11/12", 'reisen')
			);
			$list = apply_filters('reisen_filter_list_columns', $list);
			if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set('list_columns', $list);
		}
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}

// Return list of locations for the dedicated content
if ( !function_exists( 'reisen_get_list_dedicated_locations' ) ) {
	function reisen_get_list_dedicated_locations($prepend_inherit=false) {
		if (($list = reisen_storage_get('list_dedicated_locations'))=='') {
			$list = array(
				"default" => esc_html__('As in the post defined', 'reisen'),
				"center"  => esc_html__('Above the text of the post', 'reisen'),
				"left"    => esc_html__('To the left the text of the post', 'reisen'),
				"right"   => esc_html__('To the right the text of the post', 'reisen'),
				"alter"   => esc_html__('Alternates for each post', 'reisen')
			);
			$list = apply_filters('reisen_filter_list_dedicated_locations', $list);
			if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set('list_dedicated_locations', $list);
		}
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}

// Return post-format name
if ( !function_exists( 'reisen_get_post_format_name' ) ) {
	function reisen_get_post_format_name($format, $single=true) {
		$name = '';
		if ($format=='gallery')		$name = $single ? esc_html__('gallery', 'reisen') : esc_html__('galleries', 'reisen');
		else if ($format=='video')	$name = $single ? esc_html__('video', 'reisen') : esc_html__('videos', 'reisen');
		else if ($format=='audio')	$name = $single ? esc_html__('audio', 'reisen') : esc_html__('audios', 'reisen');
		else if ($format=='image')	$name = $single ? esc_html__('image', 'reisen') : esc_html__('images', 'reisen');
		else if ($format=='quote')	$name = $single ? esc_html__('quote', 'reisen') : esc_html__('quotes', 'reisen');
		else if ($format=='link')	$name = $single ? esc_html__('link', 'reisen') : esc_html__('links', 'reisen');
		else if ($format=='status')	$name = $single ? esc_html__('status', 'reisen') : esc_html__('statuses', 'reisen');
		else if ($format=='aside')	$name = $single ? esc_html__('aside', 'reisen') : esc_html__('asides', 'reisen');
		else if ($format=='chat')	$name = $single ? esc_html__('chat', 'reisen') : esc_html__('chats', 'reisen');
		else						$name = $single ? esc_html__('standard', 'reisen') : esc_html__('standards', 'reisen');
		return apply_filters('reisen_filter_list_post_format_name', $name, $format);
	}
}

// Return post-format icon name (from Fontello library)
if ( !function_exists( 'reisen_get_post_format_icon' ) ) {
	function reisen_get_post_format_icon($format) {
		$icon = 'icon-';
		if ($format=='gallery')		$icon .= 'pictures';
		else if ($format=='video')	$icon .= 'video';
		else if ($format=='audio')	$icon .= 'note';
		else if ($format=='image')	$icon .= 'picture';
		else if ($format=='quote')	$icon .= 'quote';
		else if ($format=='link')	$icon .= 'link';
		else if ($format=='status')	$icon .= 'comment';
		else if ($format=='aside')	$icon .= 'doc-text';
		else if ($format=='chat')	$icon .= 'chat';
		else						$icon .= 'book-open';
		return apply_filters('reisen_filter_list_post_format_icon', $icon, $format);
	}
}

// Return fonts styles list, prepended inherit
if ( !function_exists( 'reisen_get_list_fonts_styles' ) ) {
	function reisen_get_list_fonts_styles($prepend_inherit=false) {
		if (($list = reisen_storage_get('list_fonts_styles'))=='') {
			$list = array(
				'i' => esc_html__('I','reisen'),
				'u' => esc_html__('U', 'reisen')
			);
			if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set('list_fonts_styles', $list);
		}
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}

// Return Google fonts list
if ( !function_exists( 'reisen_get_list_fonts' ) ) {
	function reisen_get_list_fonts($prepend_inherit=false) {
		if (($list = reisen_storage_get('list_fonts'))=='') {
			$list = array();
			$list = reisen_array_merge($list, reisen_get_list_font_faces());
			$list = reisen_array_merge($list, array(
				'Advent Pro' => array('family'=>'sans-serif'),
				'Alegreya Sans' => array('family'=>'sans-serif'),
				'Arimo' => array('family'=>'sans-serif'),
				'Asap' => array('family'=>'sans-serif'),
				'Averia Sans Libre' => array('family'=>'cursive'),
				'Averia Serif Libre' => array('family'=>'cursive'),
				'Bree Serif' => array('family'=>'serif',),
				'Cabin' => array('family'=>'sans-serif'),
				'Cabin Condensed' => array('family'=>'sans-serif'),
				'Caudex' => array('family'=>'serif'),
				'Comfortaa' => array('family'=>'cursive'),
				'Cousine' => array('family'=>'sans-serif'),
				'Crimson Text' => array('family'=>'serif'),
				'Cuprum' => array('family'=>'sans-serif'),
				'Dosis' => array('family'=>'sans-serif'),
				'Economica' => array('family'=>'sans-serif'),
				'Exo' => array('family'=>'sans-serif'),
				'Expletus Sans' => array('family'=>'cursive'),
				'Karla' => array('family'=>'sans-serif'),
				'Lato' => array('family'=>'sans-serif'),
				'Lekton' => array('family'=>'sans-serif'),
				'Lobster Two' => array('family'=>'cursive'),
				'Maven Pro' => array('family'=>'sans-serif'),
				'Merriweather' => array('family'=>'serif'),
				'Montserrat' => array('family'=>'sans-serif'),
				'Neuton' => array('family'=>'serif'),
				'Noticia Text' => array('family'=>'serif'),
				'Old Standard TT' => array('family'=>'serif'),
				'Open Sans' => array('family'=>'sans-serif'),
				'Orbitron' => array('family'=>'sans-serif'),
				'Oswald' => array('family'=>'sans-serif'),
				'Overlock' => array('family'=>'cursive'),
				'Oxygen' => array('family'=>'sans-serif'),
				'Philosopher' => array('family'=>'serif'),
				'PT Serif' => array('family'=>'serif'),
				'Puritan' => array('family'=>'sans-serif'),
				'Raleway' => array('family'=>'sans-serif'),
				'Roboto' => array('family'=>'sans-serif'),
				'Roboto Slab' => array('family'=>'sans-serif'),
				'Roboto Condensed' => array('family'=>'sans-serif'),
				'Rosario' => array('family'=>'sans-serif'),
				'Share' => array('family'=>'cursive'),
				'Signika' => array('family'=>'sans-serif'),
				'Signika Negative' => array('family'=>'sans-serif'),
				'Source Sans Pro' => array('family'=>'sans-serif'),
				'Tinos' => array('family'=>'serif'),
				'Ubuntu' => array('family'=>'sans-serif'),
				'Vollkorn' => array('family'=>'serif')
				)
			);
			$list = apply_filters('reisen_filter_list_fonts', $list);
			if (reisen_get_theme_setting('use_list_cache')) reisen_storage_set('list_fonts', $list);
		}
		return $prepend_inherit ? reisen_array_merge(array('inherit' => esc_html__("Inherit", 'reisen')), $list) : $list;
	}
}

// Return Custom font-face list
if ( !function_exists( 'reisen_get_list_font_faces' ) ) {
	function reisen_get_list_font_faces($prepend_inherit=false) {
		static $list = false;
		if (is_array($list)) return $list;
		$fonts = reisen_storage_get('required_custom_fonts');
		$list = array();
		if (is_array($fonts)) {
			foreach ($fonts as $font) {
				if (($url = reisen_get_file_url('css/font-face/'.trim($font).'/stylesheet.css'))!='') {
					$list[sprintf(esc_html__('%s (uploaded font)', 'reisen'), $font)] = array('css' => $url);
				}
			}
		}
		return $list;
	}
}
?>