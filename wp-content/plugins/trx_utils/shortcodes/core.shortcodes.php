<?php
/**
 * Reisen Framework: shortcodes manipulations
 *
 * @package	reisen
 * @since	reisen 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Theme init
if (!function_exists('reisen_sc_theme_setup')) {
	add_action( 'reisen_action_init_theme', 'reisen_sc_theme_setup', 1 );
	function reisen_sc_theme_setup() {
		// Add sc stylesheets
		add_action('reisen_action_add_styles', 'reisen_sc_add_styles', 1);
	}
}

if (!function_exists('reisen_sc_theme_setup2')) {
	add_action( 'reisen_action_before_init_theme', 'reisen_sc_theme_setup2' );
	function reisen_sc_theme_setup2() {

		if ( !is_admin() || isset($_POST['action']) ) {
			// Enable/disable shortcodes in excerpt
			add_filter('the_excerpt', 					'reisen_sc_excerpt_shortcodes');
	
			// Prepare shortcodes in the content
			if (function_exists('reisen_sc_prepare_content')) reisen_sc_prepare_content();
		}

		// Add init script into shortcodes output in VC frontend editor
		add_filter('reisen_shortcode_output', 'reisen_sc_add_scripts', 10, 4);

		// AJAX: Send contact form data
		add_action('wp_ajax_send_form',			'reisen_sc_form_send');
		add_action('wp_ajax_nopriv_send_form',	'reisen_sc_form_send');

		// Show shortcodes list in admin editor
		add_action('media_buttons',				'reisen_sc_selector_add_in_toolbar', 11);

        // Registar shortcodes [trx_clients] and [trx_clients_item] in the shortcodes list
        add_action('reisen_action_shortcodes_list',		'reisen_clients_reg_shortcodes');
        if (function_exists('reisen_exists_visual_composer') && reisen_exists_visual_composer())
            add_action('reisen_action_shortcodes_list_vc','reisen_clients_reg_shortcodes_vc');

        // Register shortcodes [trx_services] and [trx_services_item]
        add_action('reisen_action_shortcodes_list',		'reisen_services_reg_shortcodes');
        if (function_exists('reisen_exists_visual_composer') && reisen_exists_visual_composer())
            add_action('reisen_action_shortcodes_list_vc','reisen_services_reg_shortcodes_vc');

        // Register shortcodes [trx_testimonials] and [trx_testimonials_item]
        add_action('reisen_action_shortcodes_list',		'reisen_testimonials_reg_shortcodes');
        if (function_exists('reisen_exists_visual_composer') && reisen_exists_visual_composer())
            add_action('reisen_action_shortcodes_list_vc','reisen_testimonials_reg_shortcodes_vc');

        if (reisen_exists_woocommerce()){
            add_action('reisen_action_shortcodes_list', 			'reisen_woocommerce_reg_shortcodes', 20);
            if (function_exists('reisen_exists_visual_composer') && reisen_exists_visual_composer())
                add_action('reisen_action_shortcodes_list_vc',	'reisen_woocommerce_reg_shortcodes_vc', 20);
        }

        if (reisen_exists_revslider()) {
            add_filter( 'reisen_filter_shortcodes_params',			'reisen_revslider_shortcodes_params' );
        }
	}
}


// Register shortcodes styles
if ( !function_exists( 'reisen_sc_add_styles' ) ) {
	//add_action('reisen_action_add_styles', 'reisen_sc_add_styles', 1);
	function reisen_sc_add_styles() {
		// Shortcodes
		wp_enqueue_style( 'reisen-shortcodes-style',	trx_utils_get_file_url('shortcodes/theme.shortcodes.css'), array(), null );
	}
}


// Register shortcodes init scripts
if ( !function_exists( 'reisen_sc_add_scripts' ) ) {
	//add_filter('reisen_shortcode_output', 'reisen_sc_add_scripts', 10, 4);
	function reisen_sc_add_scripts($output, $tag='', $atts=array(), $content='') {

		if (reisen_storage_empty('shortcodes_scripts_added')) {
			reisen_storage_set('shortcodes_scripts_added', true);
			wp_enqueue_script( 'reisen-shortcodes-script', trx_utils_get_file_url('shortcodes/theme.shortcodes.js'), array('jquery'), null, true );
		}
		
		return $output;
	}
}


/* Prepare text for shortcodes
-------------------------------------------------------------------------------- */

// Prepare shortcodes in content
if (!function_exists('reisen_sc_prepare_content')) {
	function reisen_sc_prepare_content() {
		if (function_exists('reisen_sc_clear_around')) {
			$filters = array(
				array('trx_utils', 'sc', 'clear', 'around'),
				array('widget', 'text'),
				array('the', 'excerpt'),
				array('the', 'content')
			);
			if (function_exists('reisen_exists_woocommerce') && reisen_exists_woocommerce()) {
				$filters[] = array('woocommerce', 'template', 'single', 'excerpt');
				$filters[] = array('woocommerce', 'short', 'description');
			}
			if (is_array($filters) && count($filters) > 0) {
				foreach ($filters as $flt)
					add_filter(join('_', $flt), 'reisen_sc_clear_around', 1);	// Priority 1 to clear spaces before do_shortcodes()
			}
		}
	}
}

// Enable/Disable shortcodes in the excerpt
if (!function_exists('reisen_sc_excerpt_shortcodes')) {
	//add_filter('the_excerpt', 'reisen_sc_excerpt_shortcodes');
	function reisen_sc_excerpt_shortcodes($content) {
		if (!empty($content)) {
			$content = do_shortcode($content);
		}
		return $content;
	}
}



/*
// Remove spaces and line breaks between close and open shortcode brackets ][:
[trx_columns]
	[trx_column_item]Column text ...[/trx_column_item]
	[trx_column_item]Column text ...[/trx_column_item]
	[trx_column_item]Column text ...[/trx_column_item]
[/trx_columns]

convert to

[trx_columns][trx_column_item]Column text ...[/trx_column_item][trx_column_item]Column text ...[/trx_column_item][trx_column_item]Column text ...[/trx_column_item][/trx_columns]
*/
if (!function_exists('reisen_sc_clear_around')) {
	function reisen_sc_clear_around($content) {
		if (!empty($content)) $content = preg_replace("/\](\s|\n|\r)*\[/", "][", $content);
		return $content;
	}
}






/* Shortcodes support utils
---------------------------------------------------------------------- */

// Reisen shortcodes load scripts
if (!function_exists('reisen_sc_load_scripts')) {
	function reisen_sc_load_scripts() {
		static $loaded = false;
		if (!$loaded) {
			wp_enqueue_script( 'reisen-shortcodes_admin-script', trx_utils_get_file_url('shortcodes/shortcodes_admin.js'), array('jquery'), null, true );
			wp_enqueue_script( 'reisen-selection-script',  reisen_get_file_url('js/jquery.selection.js'), array('jquery'), null, true );
			wp_localize_script( 'reisen-shortcodes_admin-script', 'REISEN_SHORTCODES_DATA', reisen_storage_get('shortcodes') );
			$loaded = true;
		}
	}
}

// Reisen shortcodes prepare scripts
if (!function_exists('reisen_sc_prepare_scripts')) {
	function reisen_sc_prepare_scripts() {
		static $prepared = false;
		if (!$prepared) {
			reisen_storage_set_array('js_vars', 'shortcodes_cp', is_admin() ? (!reisen_storage_empty('to_colorpicker') ? reisen_storage_get('to_colorpicker') : 'wp') : 'custom');	// wp | tiny | custom
			$prepared = true;
		}
	}
}

// Show shortcodes list in admin editor
if (!function_exists('reisen_sc_selector_add_in_toolbar')) {
	//add_action('media_buttons','reisen_sc_selector_add_in_toolbar', 11);
	function reisen_sc_selector_add_in_toolbar(){

		if ( !reisen_options_is_used() ) return;

		reisen_sc_load_scripts();
		reisen_sc_prepare_scripts();

		$shortcodes = reisen_storage_get('shortcodes');
		$shortcodes_list = '<select class="sc_selector"><option value="">&nbsp;'.esc_html__('- Select Shortcode -', 'trx_utils').'&nbsp;</option>';

		if (is_array($shortcodes) && count($shortcodes) > 0) {
			foreach ($shortcodes as $idx => $sc) {
				$shortcodes_list .= '<option value="'.esc_attr($idx).'" title="'.esc_attr($sc['desc']).'">'.esc_html($sc['title']).'</option>';
			}
		}

		$shortcodes_list .= '</select>';

        reisen_show_layout($shortcodes_list);
	}
}


// ---------------------------------- [trx_clients] ---------------------------------------

if ( !function_exists( 'reisen_sc_clients' ) ) {
    function reisen_sc_clients($atts, $content=null){
        if (reisen_in_shortcode_blogger()) return '';
        extract(reisen_html_decode(shortcode_atts(array(
            // Individual params
            "style" => "clients-1",
            "columns" => 4,
            "slider" => "no",
            "slides_space" => 0,
            "controls" => "no",
            "interval" => "",
            "autoheight" => "no",
            "custom" => "no",
            "ids" => "",
            "cat" => "",
            "count" => 4,
            "offset" => "",
            "orderby" => "title",
            "order" => "asc",
            "title" => "",
            "subtitle" => "",
            "description" => "",
            "link_caption" => esc_html__('Learn more', 'trx_utils'),
            "link" => '',
            "scheme" => '',
            // Common params
            "id" => "",
            "class" => "",
            "animation" => "",
            "css" => "",
            "width" => "",
            "height" => "",
            "top" => "",
            "bottom" => "",
            "left" => "",
            "right" => ""
        ), $atts)));

        if (empty($id)) $id = "sc_clients_".str_replace('.', '', mt_rand());
        if (empty($width)) $width = "100%";
        if (!empty($height) && reisen_param_is_on($autoheight)) $autoheight = "no";
        if (empty($interval)) $interval = mt_rand(5000, 10000);

        $class .= ($class ? ' ' : '') . reisen_get_css_position_as_classes($top, $right, $bottom, $left);

        $ws = reisen_get_css_dimensions_from_values($width);
        $hs = reisen_get_css_dimensions_from_values('', $height);
        $css .= ($hs) . ($ws);

        if (reisen_param_is_on($slider)) reisen_enqueue_slider('swiper');

        $columns = max(1, min(12, $columns));
        $count = max(1, (int) $count);
        if (reisen_param_is_off($custom) && $count < $columns) $columns = $count;
        reisen_storage_set('sc_clients_data', array(
                'id'=>$id,
                'style'=>$style,
                'counter'=>0,
                'columns'=>$columns,
                'slider'=>$slider,
                'css_wh'=>$ws . $hs
            )
        );

        $output = '<div' . ($id ? ' id="'.esc_attr($id).'_wrap"' : '')
            . ' class="sc_clients_wrap'
            . ($scheme && !reisen_param_is_off($scheme) && !reisen_param_is_inherit($scheme) ? ' scheme_'.esc_attr($scheme) : '')
            .'">'
            . '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
            . ' class="sc_clients sc_clients_style_'.esc_attr($style)
            . ' ' . esc_attr(reisen_get_template_property($style, 'container_classes'))
            . (!empty($class) ? ' '.esc_attr($class) : '')
            .'"'
            . ($css!='' ? ' style="'.esc_attr($css).'"' : '')
            . (!reisen_param_is_off($animation) ? ' data-animation="'.esc_attr(reisen_get_animation_classes($animation)).'"' : '')
            . '>'
            . (!empty($subtitle) ? '<h6 class="sc_clients_subtitle sc_item_subtitle">' . trim(reisen_strmacros($subtitle)) . '</h6>' : '')
            . (!empty($title) ? '<h2 class="sc_clients_title sc_item_title' . (empty($description) ? ' sc_item_title_without_descr' : ' sc_item_title_without_descr') . '">' . trim(reisen_strmacros($title)) . '</h2>' : '')
            . (!empty($description) ? '<div class="sc_clients_descr sc_item_descr">' . trim(reisen_strmacros($description)) . '</div>' : '')
            . (reisen_param_is_on($slider)
                ? ('<div class="sc_slider_swiper swiper-slider-container'
                    . ' ' . esc_attr(reisen_get_slider_controls_classes($controls))
                    . (reisen_param_is_on($autoheight) ? ' sc_slider_height_auto' : '')
                    . ($hs ? ' sc_slider_height_fixed' : '')
                    . '"'
                    . (!empty($width) && reisen_strpos($width, '%')===false ? ' data-old-width="' . esc_attr($width) . '"' : '')
                    . (!empty($height) && reisen_strpos($height, '%')===false ? ' data-old-height="' . esc_attr($height) . '"' : '')
                    . ((int) $interval > 0 ? ' data-interval="'.esc_attr($interval).'"' : '')
                    . ($columns > 1 ? ' data-slides-per-view="' . esc_attr($columns) . '"' : '')
                    . ($slides_space > 0 ? ' data-slides-space="' . esc_attr($slides_space) . '"' : '')
                    . ' data-slides-min-width="' . ($style=='clients-1' ? 100 : 220) . '"'
                    . '>'
                    . '<div class="slides swiper-wrapper">')
                : ($columns > 1
                    ? '<div class="sc_columns columns_wrap">'
                    : '')
            );

        if (reisen_param_is_on($custom) && $content) {
            $output .= do_shortcode($content);
        } else {
            global $post;

            if (!empty($ids)) {
                $posts = explode(',', $ids);
                $count = count($posts);
            }

            $args = array(
                'post_type' => 'clients',
                'post_status' => 'publish',
                'posts_per_page' => $count,
                'ignore_sticky_posts' => true,
                'order' => $order=='asc' ? 'asc' : 'desc',
            );

            if ($offset > 0 && empty($ids)) {
                $args['offset'] = $offset;
            }

            $args = reisen_query_add_sort_order($args, $orderby, $order);
            $args = reisen_query_add_posts_and_cats($args, $ids, 'clients', $cat, 'clients_group');

            $query = new WP_Query( $args );

            $post_number = 0;

            while ( $query->have_posts() ) {
                $query->the_post();
                $post_number++;
                $args = array(
                    'layout' => $style,
                    'show' => false,
                    'number' => $post_number,
                    'posts_on_page' => ($count > 0 ? $count : $query->found_posts),
                    "descr" => reisen_get_custom_option('post_excerpt_maxlength'.($columns > 1 ? '_masonry' : '')),
                    "orderby" => $orderby,
                    'content' => false,
                    'terms_list' => false,
                    'columns_count' => $columns,
                    'slider' => $slider,
                    'tag_id' => $id ? $id . '_' . $post_number : '',
                    'tag_class' => '',
                    'tag_animation' => '',
                    'tag_css' => '',
                    'tag_css_wh' => $ws . $hs
                );
                $post_data = reisen_get_post_data($args);
                $post_meta = get_post_meta($post_data['post_id'], reisen_storage_get('options_prefix') . '_post_options', true);
                $thumb_sizes = reisen_get_thumb_sizes(array('layout' => $style));
                $args['client_name'] = $post_meta['client_name'];
                $args['client_position'] = $post_meta['client_position'];
                $args['client_image'] = $post_data['post_thumb'];
                $args['client_link'] = reisen_param_is_on('client_show_link')
                    ? (!empty($post_meta['client_link']) ? $post_meta['client_link'] : $post_data['post_link'])
                    : '';
                $output .= reisen_show_post_layout($args, $post_data);
            }
            wp_reset_postdata();
        }

        if (reisen_param_is_on($slider)) {
            $output .= '</div>'
                . '<div class="sc_slider_controls_wrap"><a class="sc_slider_prev" href="#"></a><a class="sc_slider_next" href="#"></a></div>'
                . '<div class="sc_slider_pagination_wrap"></div>'
                . '</div>';
        } else if ($columns > 1) {
            $output .= '</div>';
        }

        $output .= (!empty($link) ? '<div class="sc_clients_button sc_item_button">'.reisen_do_shortcode('[trx_button link="'.esc_url($link).'" icon="icon-right"]'.esc_html($link_caption).'[/trx_button]').'</div>' : '')
            . '</div><!-- /.sc_clients -->'
            . '</div><!-- /.sc_clients_wrap -->';

        // Add template specific scripts and styles
        do_action('reisen_action_blog_scripts', $style);

        return apply_filters('reisen_shortcode_output', $output, 'trx_clients', $atts, $content);
    }
    add_shortcode('trx_clients', 'reisen_sc_clients');
}


if ( !function_exists( 'reisen_sc_clients_item' ) ) {
    function reisen_sc_clients_item($atts, $content=null) {
        if (reisen_in_shortcode_blogger()) return '';
        extract(reisen_html_decode(shortcode_atts( array(
            // Individual params
            "name" => "",
            "position" => "",
            "image" => "",
            "link" => "",
            // Common params
            "id" => "",
            "class" => "",
            "animation" => "",
            "css" => ""
        ), $atts)));

        reisen_storage_inc_array('sc_clients_data', 'counter');

        $id = $id ? $id : (reisen_storage_get_array('sc_clients_data', 'id') ? reisen_storage_get_array('sc_clients_data', 'id') . '_' . reisen_storage_get_array('sc_clients_data', 'counter') : '');

        $descr = trim(chop(do_shortcode($content)));

        $thumb_sizes = reisen_get_thumb_sizes(array('layout' => reisen_storage_get_array('sc_clients_data', 'style')));

        if ($image > 0) {
            $attach = wp_get_attachment_image_src( $image, 'full' );
            if (isset($attach[0]) && $attach[0]!='')
                $image = $attach[0];
        }
        $image = reisen_get_resized_image_tag($image, $thumb_sizes['w'], $thumb_sizes['h']);

        $post_data = array(
            'post_title' => $name,
            'post_excerpt' => $descr
        );
        $args = array(
            'layout' => reisen_storage_get_array('sc_clients_data', 'style'),
            'number' => reisen_storage_get_array('sc_clients_data', 'counter'),
            'columns_count' => reisen_storage_get_array('sc_clients_data', 'columns'),
            'slider' => reisen_storage_get_array('sc_clients_data', 'slider'),
            'show' => false,
            'descr'  => 0,
            'tag_id' => $id,
            'tag_class' => $class,
            'tag_animation' => $animation,
            'tag_css' => $css,
            'tag_css_wh' => reisen_storage_get_array('sc_clients_data', 'css_wh'),
            'client_position' => $position,
            'client_link' => $link,
            'client_image' => $image
        );
        $output = reisen_show_post_layout($args, $post_data);
        return apply_filters('reisen_shortcode_output', $output, 'trx_clients_item', $atts, $content);
    }
    add_shortcode('trx_clients_item', 'reisen_sc_clients_item');
}
// ---------------------------------- [/trx_clients] ---------------------------------------



// Add [trx_clients] and [trx_clients_item] in the shortcodes list
if (!function_exists('reisen_clients_reg_shortcodes')) {
    //Handler of add_filter('reisen_action_shortcodes_list',	'reisen_clients_reg_shortcodes');
    function reisen_clients_reg_shortcodes() {
        if (reisen_storage_isset('shortcodes')) {

            $users = reisen_get_list_users();
            $members = reisen_get_list_posts(false, array(
                    'post_type'=>'clients',
                    'orderby'=>'title',
                    'order'=>'asc',
                    'return'=>'title'
                )
            );
            $clients_groups = reisen_get_list_terms(false, 'clients_group');
            $clients_styles = reisen_get_list_templates('clients');
            $controls 		= reisen_get_list_slider_controls();

            reisen_sc_map_after('trx_chat', array(

                // Clients
                "trx_clients" => array(
                    "title" => esc_html__("Clients", 'trx_utils'),
                    "desc" => wp_kses_data( __("Insert clients list in your page (post)", 'trx_utils') ),
                    "decorate" => true,
                    "container" => false,
                    "params" => array(
                        "title" => array(
                            "title" => esc_html__("Title", 'trx_utils'),
                            "desc" => wp_kses_data( __("Title for the block", 'trx_utils') ),
                            "value" => "",
                            "type" => "text"
                        ),
                        "subtitle" => array(
                            "title" => esc_html__("Subtitle", 'trx_utils'),
                            "desc" => wp_kses_data( __("Subtitle for the block", 'trx_utils') ),
                            "value" => "",
                            "type" => "text"
                        ),
                        "description" => array(
                            "title" => esc_html__("Description", 'trx_utils'),
                            "desc" => wp_kses_data( __("Short description for the block", 'trx_utils') ),
                            "value" => "",
                            "type" => "textarea"
                        ),
                        "style" => array(
                            "title" => esc_html__("Clients style", 'trx_utils'),
                            "desc" => wp_kses_data( __("Select style to display clients list", 'trx_utils') ),
                            "value" => "clients-1",
                            "type" => "select",
                            "options" => $clients_styles
                        ),
                        "columns" => array(
                            "title" => esc_html__("Columns", 'trx_utils'),
                            "desc" => wp_kses_data( __("How many columns use to show clients", 'trx_utils') ),
                            "value" => 4,
                            "min" => 2,
                            "max" => 6,
                            "step" => 1,
                            "type" => "spinner"
                        ),
                        "scheme" => array(
                            "title" => esc_html__("Color scheme", 'trx_utils'),
                            "desc" => wp_kses_data( __("Select color scheme for this block", 'trx_utils') ),
                            "value" => "",
                            "type" => "checklist",
                            "options" => reisen_get_sc_param('schemes')
                        ),
                        "slider" => array(
                            "title" => esc_html__("Slider", 'trx_utils'),
                            "desc" => wp_kses_data( __("Use slider to show clients", 'trx_utils') ),
                            "value" => "no",
                            "type" => "switch",
                            "options" => reisen_get_sc_param('yes_no')
                        ),
                        "controls" => array(
                            "title" => esc_html__("Controls", 'trx_utils'),
                            "desc" => wp_kses_data( __("Slider controls style and position", 'trx_utils') ),
                            "dependency" => array(
                                'slider' => array('yes')
                            ),
                            "divider" => true,
                            "value" => "no",
                            "type" => "checklist",
                            "dir" => "horizontal",
                            "options" => $controls
                        ),
                        "slides_space" => array(
                            "title" => esc_html__("Space between slides", 'trx_utils'),
                            "desc" => wp_kses_data( __("Size of space (in px) between slides", 'trx_utils') ),
                            "dependency" => array(
                                'slider' => array('yes')
                            ),
                            "value" => 0,
                            "min" => 0,
                            "max" => 100,
                            "step" => 10,
                            "type" => "spinner"
                        ),
                        "interval" => array(
                            "title" => esc_html__("Slides change interval", 'trx_utils'),
                            "desc" => wp_kses_data( __("Slides change interval (in milliseconds: 1000ms = 1s)", 'trx_utils') ),
                            "dependency" => array(
                                'slider' => array('yes')
                            ),
                            "value" => 7000,
                            "step" => 500,
                            "min" => 0,
                            "type" => "spinner"
                        ),
                        "autoheight" => array(
                            "title" => esc_html__("Autoheight", 'trx_utils'),
                            "desc" => wp_kses_data( __("Change whole slider's height (make it equal current slide's height)", 'trx_utils') ),
                            "dependency" => array(
                                'slider' => array('yes')
                            ),
                            "value" => "no",
                            "type" => "switch",
                            "options" => reisen_get_sc_param('yes_no')
                        ),
                        "custom" => array(
                            "title" => esc_html__("Custom", 'trx_utils'),
                            "desc" => wp_kses_data( __("Allow get team members from inner shortcodes (custom) or get it from specified group (cat)", 'trx_utils') ),
                            "divider" => true,
                            "value" => "no",
                            "type" => "switch",
                            "options" => reisen_get_sc_param('yes_no')
                        ),
                        "cat" => array(
                            "title" => esc_html__("Categories", 'trx_utils'),
                            "desc" => wp_kses_data( __("Select categories (groups) to show team members. If empty - select team members from any category (group) or from IDs list", 'trx_utils') ),
                            "dependency" => array(
                                'custom' => array('no')
                            ),
                            "divider" => true,
                            "value" => "",
                            "type" => "select",
                            "style" => "list",
                            "multiple" => true,
                            "options" => reisen_array_merge(array(0 => esc_html__('- Select category -', 'trx_utils')), $clients_groups)
                        ),
                        "count" => array(
                            "title" => esc_html__("Number of posts", 'trx_utils'),
                            "desc" => wp_kses_data( __("How many posts will be displayed? If used IDs - this parameter ignored.", 'trx_utils') ),
                            "dependency" => array(
                                'custom' => array('no')
                            ),
                            "value" => 4,
                            "min" => 1,
                            "max" => 100,
                            "type" => "spinner"
                        ),
                        "offset" => array(
                            "title" => esc_html__("Offset before select posts", 'trx_utils'),
                            "desc" => wp_kses_data( __("Skip posts before select next part.", 'trx_utils') ),
                            "dependency" => array(
                                'custom' => array('no')
                            ),
                            "value" => 0,
                            "min" => 0,
                            "type" => "spinner"
                        ),
                        "orderby" => array(
                            "title" => esc_html__("Post order by", 'trx_utils'),
                            "desc" => wp_kses_data( __("Select desired posts sorting method", 'trx_utils') ),
                            "dependency" => array(
                                'custom' => array('no')
                            ),
                            "value" => "title",
                            "type" => "select",
                            "options" => reisen_get_sc_param('sorting')
                        ),
                        "order" => array(
                            "title" => esc_html__("Post order", 'trx_utils'),
                            "desc" => wp_kses_data( __("Select desired posts order", 'trx_utils') ),
                            "dependency" => array(
                                'custom' => array('no')
                            ),
                            "value" => "asc",
                            "type" => "switch",
                            "size" => "big",
                            "options" => reisen_get_sc_param('ordering')
                        ),
                        "ids" => array(
                            "title" => esc_html__("Post IDs list", 'trx_utils'),
                            "desc" => wp_kses_data( __("Comma separated list of posts ID. If set - parameters above are ignored!", 'trx_utils') ),
                            "dependency" => array(
                                'custom' => array('no')
                            ),
                            "value" => "",
                            "type" => "text"
                        ),
                        "link" => array(
                            "title" => esc_html__("Button URL", 'trx_utils'),
                            "desc" => wp_kses_data( __("Link URL for the button at the bottom of the block", 'trx_utils') ),
                            "value" => "",
                            "type" => "text"
                        ),
                        "link_caption" => array(
                            "title" => esc_html__("Button caption", 'trx_utils'),
                            "desc" => wp_kses_data( __("Caption for the button at the bottom of the block", 'trx_utils') ),
                            "value" => "",
                            "type" => "text"
                        ),
                        "width" => reisen_shortcodes_width(),
                        "height" => reisen_shortcodes_height(),
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
                        "name" => "trx_clients_item",
                        "title" => esc_html__("Client", 'trx_utils'),
                        "desc" => wp_kses_data( __("Single client (custom parameters)", 'trx_utils') ),
                        "container" => true,
                        "params" => array(
                            "name" => array(
                                "title" => esc_html__("Name", 'trx_utils'),
                                "desc" => wp_kses_data( __("Client's name", 'trx_utils') ),
                                "divider" => true,
                                "value" => "",
                                "type" => "text"
                            ),
                            "position" => array(
                                "title" => esc_html__("Position", 'trx_utils'),
                                "desc" => wp_kses_data( __("Client's position", 'trx_utils') ),
                                "value" => "",
                                "type" => "text"
                            ),
                            "link" => array(
                                "title" => esc_html__("Link", 'trx_utils'),
                                "desc" => wp_kses_data( __("Link on client's personal page", 'trx_utils') ),
                                "divider" => true,
                                "value" => "",
                                "type" => "text"
                            ),
                            "image" => array(
                                "title" => esc_html__("Image", 'trx_utils'),
                                "desc" => wp_kses_data( __("Client's image", 'trx_utils') ),
                                "value" => "",
                                "readonly" => false,
                                "type" => "media"
                            ),
                            "_content_" => array(
                                "title" => esc_html__("Description", 'trx_utils'),
                                "desc" => wp_kses_data( __("Client's short description", 'trx_utils') ),
                                "divider" => true,
                                "rows" => 4,
                                "value" => "",
                                "type" => "textarea"
                            ),
                            "id" => reisen_get_sc_param('id'),
                            "class" => reisen_get_sc_param('class'),
                            "animation" => reisen_get_sc_param('animation'),
                            "css" => reisen_get_sc_param('css')
                        )
                    )
                )

            ));
        }
    }
}


// Add [trx_clients] and [trx_clients_item] in the VC shortcodes list
if (!function_exists('reisen_clients_reg_shortcodes_vc')) {
    //Handler of add_filter('reisen_action_shortcodes_list_vc',	'reisen_clients_reg_shortcodes_vc');
    function reisen_clients_reg_shortcodes_vc() {

        $clients_groups = reisen_get_list_terms(false, 'clients_group');
        $clients_styles = reisen_get_list_templates('clients');
        $controls		= reisen_get_list_slider_controls();

        // Clients
        vc_map( array(
            "base" => "trx_clients",
            "name" => esc_html__("Clients", 'trx_utils'),
            "description" => wp_kses_data( __("Insert clients list", 'trx_utils') ),
            "category" => esc_html__('Content', 'trx_utils'),
            'icon' => 'icon_trx_clients',
            "class" => "trx_sc_columns trx_sc_clients",
            "content_element" => true,
            "is_container" => true,
            "show_settings_on_create" => true,
            "as_parent" => array('only' => 'trx_clients_item'),
            "params" => array(
                array(
                    "param_name" => "style",
                    "heading" => esc_html__("Clients style", 'trx_utils'),
                    "description" => wp_kses_data( __("Select style to display clients list", 'trx_utils') ),
                    "class" => "",
                    "admin_label" => true,
                    "value" => array_flip($clients_styles),
                    "type" => "dropdown"
                ),
                array(
                    "param_name" => "scheme",
                    "heading" => esc_html__("Color scheme", 'trx_utils'),
                    "description" => wp_kses_data( __("Select color scheme for this block", 'trx_utils') ),
                    "class" => "",
                    "value" => array_flip(reisen_get_sc_param('schemes')),
                    "type" => "dropdown"
                ),
                array(
                    "param_name" => "slider",
                    "heading" => esc_html__("Slider", 'trx_utils'),
                    "description" => wp_kses_data( __("Use slider to show testimonials", 'trx_utils') ),
                    "admin_label" => true,
                    "group" => esc_html__('Slider', 'trx_utils'),
                    "class" => "",
                    "std" => "no",
                    "value" => array_flip(reisen_get_sc_param('yes_no')),
                    "type" => "dropdown"
                ),
                array(
                    "param_name" => "controls",
                    "heading" => esc_html__("Controls", 'trx_utils'),
                    "description" => wp_kses_data( __("Slider controls style and position", 'trx_utils') ),
                    "admin_label" => true,
                    "group" => esc_html__('Slider', 'trx_utils'),
                    'dependency' => array(
                        'element' => 'slider',
                        'value' => 'yes'
                    ),
                    "class" => "",
                    "std" => "no",
                    "value" => array_flip($controls),
                    "type" => "dropdown"
                ),
                array(
                    "param_name" => "slides_space",
                    "heading" => esc_html__("Space between slides", 'trx_utils'),
                    "description" => wp_kses_data( __("Size of space (in px) between slides", 'trx_utils') ),
                    "admin_label" => true,
                    "group" => esc_html__('Slider', 'trx_utils'),
                    'dependency' => array(
                        'element' => 'slider',
                        'value' => 'yes'
                    ),
                    "class" => "",
                    "value" => "0",
                    "type" => "textfield"
                ),
                array(
                    "param_name" => "interval",
                    "heading" => esc_html__("Slides change interval", 'trx_utils'),
                    "description" => wp_kses_data( __("Slides change interval (in milliseconds: 1000ms = 1s)", 'trx_utils') ),
                    "group" => esc_html__('Slider', 'trx_utils'),
                    'dependency' => array(
                        'element' => 'slider',
                        'value' => 'yes'
                    ),
                    "class" => "",
                    "value" => "7000",
                    "type" => "textfield"
                ),
                array(
                    "param_name" => "autoheight",
                    "heading" => esc_html__("Autoheight", 'trx_utils'),
                    "description" => wp_kses_data( __("Change whole slider's height (make it equal current slide's height)", 'trx_utils') ),
                    "group" => esc_html__('Slider', 'trx_utils'),
                    'dependency' => array(
                        'element' => 'slider',
                        'value' => 'yes'
                    ),
                    "class" => "",
                    "value" => array("Autoheight" => "yes" ),
                    "type" => "checkbox"
                ),
                array(
                    "param_name" => "custom",
                    "heading" => esc_html__("Custom", 'trx_utils'),
                    "description" => wp_kses_data( __("Allow get clients from inner shortcodes (custom) or get it from specified group (cat)", 'trx_utils') ),
                    "class" => "",
                    "value" => array("Custom clients" => "yes" ),
                    "type" => "checkbox"
                ),
                array(
                    "param_name" => "title",
                    "heading" => esc_html__("Title", 'trx_utils'),
                    "description" => wp_kses_data( __("Title for the block", 'trx_utils') ),
                    "admin_label" => true,
                    "group" => esc_html__('Captions', 'trx_utils'),
                    "class" => "",
                    "value" => "",
                    "type" => "textfield"
                ),
                array(
                    "param_name" => "subtitle",
                    "heading" => esc_html__("Subtitle", 'trx_utils'),
                    "description" => wp_kses_data( __("Subtitle for the block", 'trx_utils') ),
                    "group" => esc_html__('Captions', 'trx_utils'),
                    "class" => "",
                    "value" => "",
                    "type" => "textfield"
                ),
                array(
                    "param_name" => "description",
                    "heading" => esc_html__("Description", 'trx_utils'),
                    "description" => wp_kses_data( __("Description for the block", 'trx_utils') ),
                    "group" => esc_html__('Captions', 'trx_utils'),
                    "class" => "",
                    "value" => "",
                    "type" => "textarea"
                ),
                array(
                    "param_name" => "cat",
                    "heading" => esc_html__("Categories", 'trx_utils'),
                    "description" => wp_kses_data( __("Select category to show clients. If empty - select clients from any category (group) or from IDs list", 'trx_utils') ),
                    "group" => esc_html__('Query', 'trx_utils'),
                    'dependency' => array(
                        'element' => 'custom',
                        'is_empty' => true
                    ),
                    "class" => "",
                    "value" => array_flip(reisen_array_merge(array(0 => esc_html__('- Select category -', 'trx_utils')), $clients_groups)),
                    "type" => "dropdown"
                ),
                array(
                    "param_name" => "columns",
                    "heading" => esc_html__("Columns", 'trx_utils'),
                    "description" => wp_kses_data( __("How many columns use to show clients", 'trx_utils') ),
                    "group" => esc_html__('Query', 'trx_utils'),
                    "admin_label" => true,
                    "class" => "",
                    "value" => "4",
                    "type" => "textfield"
                ),
                array(
                    "param_name" => "count",
                    "heading" => esc_html__("Number of posts", 'trx_utils'),
                    "description" => wp_kses_data( __("How many posts will be displayed? If used IDs - this parameter ignored.", 'trx_utils') ),
                    "group" => esc_html__('Query', 'trx_utils'),
                    'dependency' => array(
                        'element' => 'custom',
                        'is_empty' => true
                    ),
                    "class" => "",
                    "value" => "4",
                    "type" => "textfield"
                ),
                array(
                    "param_name" => "offset",
                    "heading" => esc_html__("Offset before select posts", 'trx_utils'),
                    "description" => wp_kses_data( __("Skip posts before select next part.", 'trx_utils') ),
                    "group" => esc_html__('Query', 'trx_utils'),
                    'dependency' => array(
                        'element' => 'custom',
                        'is_empty' => true
                    ),
                    "class" => "",
                    "value" => "0",
                    "type" => "textfield"
                ),
                array(
                    "param_name" => "orderby",
                    "heading" => esc_html__("Post sorting", 'trx_utils'),
                    "description" => wp_kses_data( __("Select desired posts sorting method", 'trx_utils') ),
                    "group" => esc_html__('Query', 'trx_utils'),
                    'dependency' => array(
                        'element' => 'custom',
                        'is_empty' => true
                    ),
                    "std" => "title",
                    "class" => "",
                    "value" => array_flip(reisen_get_sc_param('sorting')),
                    "type" => "dropdown"
                ),
                array(
                    "param_name" => "order",
                    "heading" => esc_html__("Post order", 'trx_utils'),
                    "description" => wp_kses_data( __("Select desired posts order", 'trx_utils') ),
                    "group" => esc_html__('Query', 'trx_utils'),
                    'dependency' => array(
                        'element' => 'custom',
                        'is_empty' => true
                    ),
                    "std" => "asc",
                    "class" => "",
                    "value" => array_flip(reisen_get_sc_param('ordering')),
                    "type" => "dropdown"
                ),
                array(
                    "param_name" => "ids",
                    "heading" => esc_html__("client's IDs list", 'trx_utils'),
                    "description" => wp_kses_data( __("Comma separated list of client's ID. If set - parameters above (category, count, order, etc.)  are ignored!", 'trx_utils') ),
                    "group" => esc_html__('Query', 'trx_utils'),
                    'dependency' => array(
                        'element' => 'custom',
                        'is_empty' => true
                    ),
                    "class" => "",
                    "value" => "",
                    "type" => "textfield"
                ),
                array(
                    "param_name" => "link",
                    "heading" => esc_html__("Button URL", 'trx_utils'),
                    "description" => wp_kses_data( __("Link URL for the button at the bottom of the block", 'trx_utils') ),
                    "group" => esc_html__('Captions', 'trx_utils'),
                    "class" => "",
                    "value" => "",
                    "type" => "textfield"
                ),
                array(
                    "param_name" => "link_caption",
                    "heading" => esc_html__("Button caption", 'trx_utils'),
                    "description" => wp_kses_data( __("Caption for the button at the bottom of the block", 'trx_utils') ),
                    "group" => esc_html__('Captions', 'trx_utils'),
                    "class" => "",
                    "value" => "",
                    "type" => "textfield"
                ),
                reisen_vc_width(),
                reisen_vc_height(),
                reisen_get_vc_param('margin_top'),
                reisen_get_vc_param('margin_bottom'),
                reisen_get_vc_param('margin_left'),
                reisen_get_vc_param('margin_right'),
                reisen_get_vc_param('id'),
                reisen_get_vc_param('class'),
                reisen_get_vc_param('animation'),
                reisen_get_vc_param('css')
            ),
            'js_view' => 'VcTrxColumnsView'
        ) );


        vc_map( array(
            "base" => "trx_clients_item",
            "name" => esc_html__("Client", 'trx_utils'),
            "description" => wp_kses_data( __("Client - all data pull out from it account on your site", 'trx_utils') ),
            "show_settings_on_create" => true,
            "class" => "trx_sc_collection trx_sc_column_item trx_sc_clients_item",
            "content_element" => true,
            "is_container" => true,
            'icon' => 'icon_trx_clients_item',
            "as_child" => array('only' => 'trx_clients'),
            "as_parent" => array('except' => 'trx_clients'),
            "params" => array(
                array(
                    "param_name" => "name",
                    "heading" => esc_html__("Name", 'trx_utils'),
                    "description" => wp_kses_data( __("Client's name", 'trx_utils') ),
                    "admin_label" => true,
                    "class" => "",
                    "value" => "",
                    "type" => "textfield"
                ),
                array(
                    "param_name" => "position",
                    "heading" => esc_html__("Position", 'trx_utils'),
                    "description" => wp_kses_data( __("Client's position", 'trx_utils') ),
                    "admin_label" => true,
                    "class" => "",
                    "value" => "",
                    "type" => "textfield"
                ),
                array(
                    "param_name" => "link",
                    "heading" => esc_html__("Link", 'trx_utils'),
                    "description" => wp_kses_data( __("Link on client's personal page", 'trx_utils') ),
                    "class" => "",
                    "value" => "",
                    "type" => "textfield"
                ),
                array(
                    "param_name" => "image",
                    "heading" => esc_html__("Client's image", 'trx_utils'),
                    "description" => wp_kses_data( __("Clients's image", 'trx_utils') ),
                    "class" => "",
                    "value" => "",
                    "type" => "attach_image"
                ),
                reisen_get_vc_param('id'),
                reisen_get_vc_param('class'),
                reisen_get_vc_param('animation'),
                reisen_get_vc_param('css')
            ),
            'js_view' => 'VcTrxColumnItemView'
        ) );

        class WPBakeryShortCode_Trx_Clients extends Reisen_Vc_ShortCodeColumns {}
        class WPBakeryShortCode_Trx_Clients_Item extends Reisen_Vc_ShortCodeCollection {}

    }
}


// ---------------------------------- [trx_services] ---------------------------------------

if ( !function_exists( 'reisen_sc_services' ) ) {
    function reisen_sc_services($atts, $content=null){
        if (reisen_in_shortcode_blogger()) return '';
        extract(reisen_html_decode(shortcode_atts(array(
            // Individual params
            "style" => "services-1",
            "columns" => 4,
            "slider" => "no",
            "slides_space" => 0,
            "controls" => "no",
            "interval" => "",
            "autoheight" => "no",
            "equalheight" => "no",
            "align" => "",
            "custom" => "no",
            "type" => "icons",	// icons | images
            "ids" => "",
            "cat" => "",
            "count" => 4,
            "offset" => "",
            "orderby" => "date",
            "order" => "desc",
            "readmore" => esc_html__('Learn more', 'trx_utils'),
            "title" => "",
            "subtitle" => "",
            "description" => "",
            "link_caption" => esc_html__('Learn more', 'trx_utils'),
            "link" => '',
            "scheme" => '',
            "image" => '',
            "image_align" => '',
            // Common params
            "id" => "",
            "class" => "",
            "animation" => "",
            "css" => "",
            "width" => "",
            "height" => "",
            "top" => "",
            "bottom" => "",
            "left" => "",
            "right" => ""
        ), $atts)));

        if (reisen_param_is_off($slider) && $columns > 1 && $style == 'services-5' && !empty($image)) $columns = 2;
        if (!empty($image)) {
            if ($image > 0) {
                $attach = wp_get_attachment_image_src( $image, 'full' );
                if (isset($attach[0]) && $attach[0]!='')
                    $image = $attach[0];
            }
        }

        if (empty($id)) $id = "sc_services_".str_replace('.', '', mt_rand());
        if (empty($width)) $width = "100%";
        if (!empty($height) && reisen_param_is_on($autoheight)) $autoheight = "no";
        if (empty($interval)) $interval = mt_rand(5000, 10000);

        $class .= ($class ? ' ' : '') . reisen_get_css_position_as_classes($top, $right, $bottom, $left);

        $ws = reisen_get_css_dimensions_from_values($width);
        $hs = reisen_get_css_dimensions_from_values('', $height);
        $css .= ($hs) . ($ws);

        $columns = max(1, min(12, (int) $columns));
        $count = max(1, (int) $count);
        if (reisen_param_is_off($custom) && $count < $columns) $columns = $count;

        if (reisen_param_is_on($slider)) reisen_enqueue_slider('swiper');

        reisen_storage_set('sc_services_data', array(
                'id' => $id,
                'style' => $style,
                'type' => $type,
                'columns' => $columns,
                'counter' => 0,
                'slider' => $slider,
                'css_wh' => $ws . $hs,
                'readmore' => $readmore
            )
        );

        $alt = basename($image);
        $alt = substr($alt,0,strlen($alt) - 4);
        $output = '<div' . ($id ? ' id="'.esc_attr($id).'_wrap"' : '')
            . ' class="sc_services_wrap'
            . ($scheme && !reisen_param_is_off($scheme) && !reisen_param_is_inherit($scheme) ? ' scheme_'.esc_attr($scheme) : '')
            .'">'
            . '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
            . ' class="sc_services'
            . ' sc_services_style_'.esc_attr($style)
            . ' sc_services_type_'.esc_attr($type)
            . ' ' . esc_attr(reisen_get_template_property($style, 'container_classes'))
            . (!empty($class) ? ' '.esc_attr($class) : '')
            . ($align!='' && $align!='none' ? ' align'.esc_attr($align) : '')
            . '"'
            . ($css!='' ? ' style="'.esc_attr($css).'"' : '')
            . (!reisen_param_is_off($equalheight) ? ' data-equal-height=".sc_services_item"' : '')
            . (!reisen_param_is_off($animation) ? ' data-animation="'.esc_attr(reisen_get_animation_classes($animation)).'"' : '')
            . '>'
            . (!empty($subtitle) ? '<h6 class="sc_services_subtitle sc_item_subtitle">' . trim(reisen_strmacros($subtitle)) . '</h6>' : '')
            . (!empty($title) ? '<h3 class="sc_services_title sc_item_title' . (empty($description) ? ' sc_item_title_without_descr' : ' sc_item_title_without_descr') . '">' . trim(reisen_strmacros($title)) . '</h3>' : '')
            . (!empty($description) ? '<div class="sc_services_descr sc_item_descr">' . trim(reisen_strmacros($description)) . '</div>' : '')
            . (reisen_param_is_on($slider)
                ? ('<div class="sc_slider_swiper swiper-slider-container'
                    . ' ' . esc_attr(reisen_get_slider_controls_classes($controls))
                    . (reisen_param_is_on($autoheight) ? ' sc_slider_height_auto' : '')
                    . ($hs ? ' sc_slider_height_fixed' : '')
                    . '"'
                    . (!empty($width) && reisen_strpos($width, '%')===false ? ' data-old-width="' . esc_attr($width) . '"' : '')
                    . (!empty($height) && reisen_strpos($height, '%')===false ? ' data-old-height="' . esc_attr($height) . '"' : '')
                    . ((int) $interval > 0 ? ' data-interval="'.esc_attr($interval).'"' : '')
                    . ($columns > 1 ? ' data-slides-per-view="' . esc_attr($columns) . '"' : '')
                    . ($slides_space > 0 ? ' data-slides-space="' . esc_attr($slides_space) . '"' : '')
                    . ' data-slides-min-width="250"'
                    . '>'
                    . '<div class="slides swiper-wrapper">')
                : ($columns > 1
                    ? ($style == 'services-5' && !empty($image)
                        ? '<div class="sc_service_container sc_align_'.esc_attr($image_align).'">'
                        . '<div class="sc_services_image"><img src="'.esc_url($image).'" alt="'.esc_html($alt).'"></div>'
                        : '')
                    . '<div class="sc_columns columns_wrap">'
                    : '')
            );

        if (reisen_param_is_on($custom) && $content) {
            $output .= do_shortcode($content);
        } else {
            global $post;

            if (!empty($ids)) {
                $posts = explode(',', $ids);
                $count = count($posts);
            }

            $args = array(
                'post_type' => 'services',
                'post_status' => 'publish',
                'posts_per_page' => $count,
                'ignore_sticky_posts' => true,
                'order' => $order=='asc' ? 'asc' : 'desc',
                'readmore' => $readmore
            );

            if ($offset > 0 && empty($ids)) {
                $args['offset'] = $offset;
            }

            $args = reisen_query_add_sort_order($args, $orderby, $order);
            $args = reisen_query_add_posts_and_cats($args, $ids, 'services', $cat, 'services_group');

            $query = new WP_Query( $args );

            $post_number = 0;

            while ( $query->have_posts() ) {
                $query->the_post();
                $post_number++;
                $args = array(
                    'layout' => $style,
                    'show' => false,
                    'number' => $post_number,
                    'posts_on_page' => ($count > 0 ? $count : $query->found_posts),
                    "descr" => reisen_get_custom_option('post_excerpt_maxlength'.($columns > 1 ? '_masonry' : '')),
                    "orderby" => $orderby,
                    'content' => false,
                    'terms_list' => false,
                    'readmore' => $readmore,
                    'tag_type' => $type,
                    'columns_count' => $columns,
                    'slider' => $slider,
                    'tag_id' => $id ? $id . '_' . $post_number : '',
                    'tag_class' => '',
                    'tag_animation' => '',
                    'tag_css' => '',
                    'tag_css_wh' => $ws . $hs
                );
                $output .= reisen_show_post_layout($args);
            }
            wp_reset_postdata();
        }

        if (reisen_param_is_on($slider)) {
            $output .= '</div>'
                . '<div class="sc_slider_controls_wrap"><a class="sc_slider_prev" href="#"></a><a class="sc_slider_next" href="#"></a></div>'
                . '<div class="sc_slider_pagination_wrap"></div>'
                . '</div>';
        } else if ($columns > 1) {
            $output .= '</div>';
            if ($style == 'services-5' && !empty($image))
                $output .= '</div>';
        }

        $output .=  (!empty($link) ? '<div class="sc_services_button sc_item_button">'.reisen_do_shortcode('[trx_button link="'.esc_url($link).'" icon="icon-right"]'.esc_html($link_caption).'[/trx_button]').'</div>' : '')
            . '</div><!-- /.sc_services -->'
            . '</div><!-- /.sc_services_wrap -->';

        // Add template specific scripts and styles
        do_action('reisen_action_blog_scripts', $style);

        return apply_filters('reisen_shortcode_output', $output, 'trx_services', $atts, $content);
    }
    add_shortcode('trx_services', 'reisen_sc_services');
}


if ( !function_exists( 'reisen_sc_services_item' ) ) {
    function reisen_sc_services_item($atts, $content=null) {
        if (reisen_in_shortcode_blogger()) return '';
        extract(reisen_html_decode(shortcode_atts( array(
            // Individual params
            "icon" => "",
            "image" => "",
            "title" => "",
            "link" => "",
            "readmore" => "(none)",
            // Common params
            "id" => "",
            "class" => "",
            "animation" => "",
            "css" => ""
        ), $atts)));

        reisen_storage_inc_array('sc_services_data', 'counter');

        $id = $id ? $id : (reisen_storage_get_array('sc_services_data', 'id') ? reisen_storage_get_array('sc_services_data', 'id') . '_' . reisen_storage_get_array('sc_services_data', 'counter') : '');

        $descr = trim(chop(do_shortcode($content)));
        $readmore = $readmore=='(none)' ? reisen_storage_get_array('sc_services_data', 'readmore') : $readmore;

        $type = reisen_storage_get_array('sc_services_data', 'type');
        if (!empty($icon)) {
            $type = 'icons';
        } else if (!empty($image)) {
            $type = 'images';
            if ($image > 0) {
                $attach = wp_get_attachment_image_src( $image, 'full' );
                if (isset($attach[0]) && $attach[0]!='')
                    $image = $attach[0];
            }
            $thumb_sizes = reisen_get_thumb_sizes(array('layout' => reisen_storage_get_array('sc_services_data', 'style')));
            $image = reisen_get_resized_image_tag($image, $thumb_sizes['w'], $thumb_sizes['h']);
        }

        $post_data = array(
            'post_title' => $title,
            'post_excerpt' => $descr,
            'post_thumb' => $image,
            'post_icon' => $icon,
            'post_link' => $link,
            'post_protected' => false,
            'post_format' => 'standard'
        );
        $args = array(
            'layout' => reisen_storage_get_array('sc_services_data', 'style'),
            'number' => reisen_storage_get_array('sc_services_data', 'counter'),
            'columns_count' => reisen_storage_get_array('sc_services_data', 'columns'),
            'slider' => reisen_storage_get_array('sc_services_data', 'slider'),
            'show' => false,
            'descr'  => -1,		// -1 - don't strip tags, 0 - strip_tags, >0 - strip_tags and truncate string
            'readmore' => $readmore,
            'tag_type' => $type,
            'tag_id' => $id,
            'tag_class' => $class,
            'tag_animation' => $animation,
            'tag_css' => $css,
            'tag_css_wh' => reisen_storage_get_array('sc_services_data', 'css_wh')
        );
        $output = reisen_show_post_layout($args, $post_data);
        return apply_filters('reisen_shortcode_output', $output, 'trx_services_item', $atts, $content);
    }
    add_shortcode('trx_services_item', 'reisen_sc_services_item');
}
// ---------------------------------- [/trx_services] ---------------------------------------



// Add [trx_services] and [trx_services_item] in the shortcodes list
if (!function_exists('reisen_services_reg_shortcodes')) {
    //Handler of add_filter('reisen_action_shortcodes_list',	'reisen_services_reg_shortcodes');
    function reisen_services_reg_shortcodes() {
        if (reisen_storage_isset('shortcodes')) {

            $services_groups = reisen_get_list_terms(false, 'services_group');
            $services_styles = reisen_get_list_templates('services');
            $controls 		 = reisen_get_list_slider_controls();

            reisen_sc_map_after('trx_section', array(

                // Services
                "trx_services" => array(
                    "title" => esc_html__("Services", 'trx_utils'),
                    "desc" => wp_kses_data( __("Insert services list in your page (post)", 'trx_utils') ),
                    "decorate" => true,
                    "container" => false,
                    "params" => array(
                        "title" => array(
                            "title" => esc_html__("Title", 'trx_utils'),
                            "desc" => wp_kses_data( __("Title for the block", 'trx_utils') ),
                            "value" => "",
                            "type" => "text"
                        ),
                        "subtitle" => array(
                            "title" => esc_html__("Subtitle", 'trx_utils'),
                            "desc" => wp_kses_data( __("Subtitle for the block", 'trx_utils') ),
                            "value" => "",
                            "type" => "text"
                        ),
                        "description" => array(
                            "title" => esc_html__("Description", 'trx_utils'),
                            "desc" => wp_kses_data( __("Short description for the block", 'trx_utils') ),
                            "value" => "",
                            "type" => "textarea"
                        ),
                        "style" => array(
                            "title" => esc_html__("Services style", 'trx_utils'),
                            "desc" => wp_kses_data( __("Select style to display services list", 'trx_utils') ),
                            "value" => "services-1",
                            "type" => "select",
                            "options" => $services_styles
                        ),
                        "image" => array(
                            "title" => esc_html__("Item's image", 'trx_utils'),
                            "desc" => wp_kses_data( __("Item's image", 'trx_utils') ),
                            "dependency" => array(
                                'style' => 'services-5'
                            ),
                            "value" => "",
                            "readonly" => false,
                            "type" => "media"
                        ),
                        "image_align" => array(
                            "title" => esc_html__("Image alignment", 'trx_utils'),
                            "desc" => wp_kses_data( __("Alignment of the image", 'trx_utils') ),
                            "divider" => true,
                            "value" => "",
                            "type" => "checklist",
                            "dir" => "horizontal",
                            "options" => reisen_get_sc_param('align')
                        ),
                        "type" => array(
                            "title" => esc_html__("Icon's type", 'trx_utils'),
                            "desc" => wp_kses_data( __("Select type of icons: font icon or image", 'trx_utils') ),
                            "value" => "icons",
                            "type" => "checklist",
                            "dir" => "horizontal",
                            "options" => array(
                            	'icons'  => esc_html__('Icons', 'trx_utils'),
                                'images' => esc_html__('Images', 'trx_utils'),
		                            'numbers'  => esc_html__('Numbers', 'trx_utils'),
                            )
                        ),
                        "columns" => array(
                            "title" => esc_html__("Columns", 'trx_utils'),
                            "desc" => wp_kses_data( __("How many columns use to show services list", 'trx_utils') ),
                            "value" => 4,
                            "min" => 2,
                            "max" => 6,
                            "step" => 1,
                            "type" => "spinner"
                        ),
                        "scheme" => array(
                            "title" => esc_html__("Color scheme", 'trx_utils'),
                            "desc" => wp_kses_data( __("Select color scheme for this block", 'trx_utils') ),
                            "value" => "",
                            "type" => "checklist",
                            "options" => reisen_get_sc_param('schemes')
                        ),
                        "slider" => array(
                            "title" => esc_html__("Slider", 'trx_utils'),
                            "desc" => wp_kses_data( __("Use slider to show services", 'trx_utils') ),
                            "value" => "no",
                            "type" => "switch",
                            "options" => reisen_get_sc_param('yes_no')
                        ),
                        "controls" => array(
                            "title" => esc_html__("Controls", 'trx_utils'),
                            "desc" => wp_kses_data( __("Slider controls style and position", 'trx_utils') ),
                            "dependency" => array(
                                'slider' => array('yes')
                            ),
                            "divider" => true,
                            "value" => "",
                            "type" => "checklist",
                            "dir" => "horizontal",
                            "options" => $controls
                        ),
                        "slides_space" => array(
                            "title" => esc_html__("Space between slides", 'trx_utils'),
                            "desc" => wp_kses_data( __("Size of space (in px) between slides", 'trx_utils') ),
                            "dependency" => array(
                                'slider' => array('yes')
                            ),
                            "value" => 0,
                            "min" => 0,
                            "max" => 100,
                            "step" => 10,
                            "type" => "spinner"
                        ),
                        "interval" => array(
                            "title" => esc_html__("Slides change interval", 'trx_utils'),
                            "desc" => wp_kses_data( __("Slides change interval (in milliseconds: 1000ms = 1s)", 'trx_utils') ),
                            "dependency" => array(
                                'slider' => array('yes')
                            ),
                            "value" => 7000,
                            "step" => 500,
                            "min" => 0,
                            "type" => "spinner"
                        ),
                        "autoheight" => array(
                            "title" => esc_html__("Autoheight", 'trx_utils'),
                            "desc" => wp_kses_data( __("Change whole slider's height (make it equal current slide's height)", 'trx_utils') ),
                            "dependency" => array(
                                'slider' => array('yes')
                            ),
                            "value" => "yes",
                            "type" => "switch",
                            "options" => reisen_get_sc_param('yes_no')
                        ),
                        "align" => array(
                            "title" => esc_html__("Alignment", 'trx_utils'),
                            "desc" => wp_kses_data( __("Alignment of the services block", 'trx_utils') ),
                            "divider" => true,
                            "value" => "",
                            "type" => "checklist",
                            "dir" => "horizontal",
                            "options" => reisen_get_sc_param('align')
                        ),
                        "custom" => array(
                            "title" => esc_html__("Custom", 'trx_utils'),
                            "desc" => wp_kses_data( __("Allow get services items from inner shortcodes (custom) or get it from specified group (cat)", 'trx_utils') ),
                            "divider" => true,
                            "value" => "no",
                            "type" => "switch",
                            "options" => reisen_get_sc_param('yes_no')
                        ),
                        "cat" => array(
                            "title" => esc_html__("Categories", 'trx_utils'),
                            "desc" => wp_kses_data( __("Select categories (groups) to show services list. If empty - select services from any category (group) or from IDs list", 'trx_utils') ),
                            "dependency" => array(
                                'custom' => array('no')
                            ),
                            "divider" => true,
                            "value" => "",
                            "type" => "select",
                            "style" => "list",
                            "multiple" => true,
                            "options" => reisen_array_merge(array(0 => esc_html__('- Select category -', 'trx_utils')), $services_groups)
                        ),
                        "count" => array(
                            "title" => esc_html__("Number of posts", 'trx_utils'),
                            "desc" => wp_kses_data( __("How many posts will be displayed? If used IDs - this parameter ignored.", 'trx_utils') ),
                            "dependency" => array(
                                'custom' => array('no')
                            ),
                            "value" => 4,
                            "min" => 1,
                            "max" => 100,
                            "type" => "spinner"
                        ),
                        "offset" => array(
                            "title" => esc_html__("Offset before select posts", 'trx_utils'),
                            "desc" => wp_kses_data( __("Skip posts before select next part.", 'trx_utils') ),
                            "dependency" => array(
                                'custom' => array('no')
                            ),
                            "value" => 0,
                            "min" => 0,
                            "type" => "spinner"
                        ),
                        "orderby" => array(
                            "title" => esc_html__("Post order by", 'trx_utils'),
                            "desc" => wp_kses_data( __("Select desired posts sorting method", 'trx_utils') ),
                            "dependency" => array(
                                'custom' => array('no')
                            ),
                            "value" => "date",
                            "type" => "select",
                            "options" => reisen_get_sc_param('sorting')
                        ),
                        "order" => array(
                            "title" => esc_html__("Post order", 'trx_utils'),
                            "desc" => wp_kses_data( __("Select desired posts order", 'trx_utils') ),
                            "dependency" => array(
                                'custom' => array('no')
                            ),
                            "value" => "desc",
                            "type" => "switch",
                            "size" => "big",
                            "options" => reisen_get_sc_param('ordering')
                        ),
                        "ids" => array(
                            "title" => esc_html__("Post IDs list", 'trx_utils'),
                            "desc" => wp_kses_data( __("Comma separated list of posts ID. If set - parameters above are ignored!", 'trx_utils') ),
                            "dependency" => array(
                                'custom' => array('no')
                            ),
                            "value" => "",
                            "type" => "text"
                        ),
                        "readmore" => array(
                            "title" => esc_html__("Read more", 'trx_utils'),
                            "desc" => wp_kses_data( __("Caption for the Read more link (if empty - link not showed)", 'trx_utils') ),
                            "value" => "",
                            "type" => "text"
                        ),
                        "link" => array(
                            "title" => esc_html__("Button URL", 'trx_utils'),
                            "desc" => wp_kses_data( __("Link URL for the button at the bottom of the block", 'trx_utils') ),
                            "value" => "",
                            "type" => "text"
                        ),
                        "link_caption" => array(
                            "title" => esc_html__("Button caption", 'trx_utils'),
                            "desc" => wp_kses_data( __("Caption for the button at the bottom of the block", 'trx_utils') ),
                            "value" => "",
                            "type" => "text"
                        ),
                        "width" => reisen_shortcodes_width(),
                        "height" => reisen_shortcodes_height(),
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
                        "name" => "trx_services_item",
                        "title" => esc_html__("Service item", 'trx_utils'),
                        "desc" => wp_kses_data( __("Service item", 'trx_utils') ),
                        "container" => true,
                        "params" => array(
                            "title" => array(
                                "title" => esc_html__("Title", 'trx_utils'),
                                "desc" => wp_kses_data( __("Item's title", 'trx_utils') ),
                                "divider" => true,
                                "value" => "",
                                "type" => "text"
                            ),
                            "icon" => array(
                                "title" => esc_html__("Item's icon",  'trx_utils'),
                                "desc" => wp_kses_data( __('Select icon for the item from Fontello icons set',  'trx_utils') ),
                                "value" => "",
                                "type" => "icons",
                                "options" => reisen_get_sc_param('icons')
                            ),
                            "image" => array(
                                "title" => esc_html__("Item's image", 'trx_utils'),
                                "desc" => wp_kses_data( __("Item's image (if icon not selected)", 'trx_utils') ),
                                "dependency" => array(
                                    'icon' => array('is_empty', 'none')
                                ),
                                "value" => "",
                                "readonly" => false,
                                "type" => "media"
                            ),
                            "link" => array(
                                "title" => esc_html__("Link", 'trx_utils'),
                                "desc" => wp_kses_data( __("Link on service's item page", 'trx_utils') ),
                                "divider" => true,
                                "value" => "",
                                "type" => "text"
                            ),
                            "readmore" => array(
                                "title" => esc_html__("Read more", 'trx_utils'),
                                "desc" => wp_kses_data( __("Caption for the Read more link (if empty - link not showed)", 'trx_utils') ),
                                "value" => "",
                                "type" => "text"
                            ),
                            "_content_" => array(
                                "title" => esc_html__("Description", 'trx_utils'),
                                "desc" => wp_kses_data( __("Item's short description", 'trx_utils') ),
                                "divider" => true,
                                "rows" => 4,
                                "value" => "",
                                "type" => "textarea"
                            ),
                            "id" => reisen_get_sc_param('id'),
                            "class" => reisen_get_sc_param('class'),
                            "animation" => reisen_get_sc_param('animation'),
                            "css" => reisen_get_sc_param('css')
                        )
                    )
                )

            ));
        }
    }
}


// Add [trx_services] and [trx_services_item] in the VC shortcodes list
if (!function_exists('reisen_services_reg_shortcodes_vc')) {
    //Handler of add_filter('reisen_action_shortcodes_list_vc',	'reisen_services_reg_shortcodes_vc');
    function reisen_services_reg_shortcodes_vc() {

        $services_groups = reisen_get_list_terms(false, 'services_group');
        $services_styles = reisen_get_list_templates('services');
        $controls		 = reisen_get_list_slider_controls();

        // Services
        vc_map( array(
            "base" => "trx_services",
            "name" => esc_html__("Services", 'trx_utils'),
            "description" => wp_kses_data( __("Insert services list", 'trx_utils') ),
            "category" => esc_html__('Content', 'trx_utils'),
            "icon" => 'icon_trx_services',
            "class" => "trx_sc_columns trx_sc_services",
            "content_element" => true,
            "is_container" => true,
            "show_settings_on_create" => true,
            "as_parent" => array('only' => 'trx_services_item'),
            "params" => array(
                array(
                    "param_name" => "style",
                    "heading" => esc_html__("Services style", 'trx_utils'),
                    "description" => wp_kses_data( __("Select style to display services list", 'trx_utils') ),
                    "class" => "",
                    "admin_label" => true,
                    "value" => array_flip($services_styles),
                    "type" => "dropdown"
                ),
                array(
                    "param_name" => "type",
                    "heading" => esc_html__("Icon's type", 'trx_utils'),
                    "description" => wp_kses_data( __("Select type of icons: font icon or image", 'trx_utils') ),
                    "class" => "",
                    "admin_label" => true,
                    "value" => array(
                    	esc_html__('Icons', 'trx_utils') => 'icons',
                        esc_html__('Images', 'trx_utils') => 'images',
		                esc_html__('Numbers', 'trx_utils') => 'numbers',
                    ),
                    "type" => "dropdown"
                ),
                array(
                    "param_name" => "equalheight",
                    "heading" => esc_html__("Equal height", 'trx_utils'),
                    "description" => wp_kses_data( __("Make equal height for all items in the row", 'trx_utils') ),
                    "value" => array("Equal height" => "yes" ),
                    "type" => "checkbox"
                ),
                array(
                    "param_name" => "scheme",
                    "heading" => esc_html__("Color scheme", 'trx_utils'),
                    "description" => wp_kses_data( __("Select color scheme for this block", 'trx_utils') ),
                    "class" => "",
                    "value" => array_flip(reisen_get_sc_param('schemes')),
                    "type" => "dropdown"
                ),
                array(
                    "param_name" => "image",
                    "heading" => esc_html__("Image", 'trx_utils'),
                    "description" => wp_kses_data( __("Item's image", 'trx_utils') ),
                    'dependency' => array(
                        'element' => 'style',
                        'value' => 'services-5'
                    ),
                    "class" => "",
                    "value" => "",
                    "type" => "attach_image"
                ),
                array(
                    "param_name" => "image_align",
                    "heading" => esc_html__("Image alignment", 'trx_utils'),
                    "description" => wp_kses_data( __("Alignment of the image", 'trx_utils') ),
                    "class" => "",
                    "value" => array_flip(reisen_get_sc_param('align')),
                    "type" => "dropdown"
                ),
                array(
                    "param_name" => "slider",
                    "heading" => esc_html__("Slider", 'trx_utils'),
                    "description" => wp_kses_data( __("Use slider to show services", 'trx_utils') ),
                    "admin_label" => true,
                    "group" => esc_html__('Slider', 'trx_utils'),
                    "class" => "",
                    "std" => "no",
                    "value" => array_flip(reisen_get_sc_param('yes_no')),
                    "type" => "dropdown"
                ),
                array(
                    "param_name" => "controls",
                    "heading" => esc_html__("Controls", 'trx_utils'),
                    "description" => wp_kses_data( __("Slider controls style and position", 'trx_utils') ),
                    "admin_label" => true,
                    "group" => esc_html__('Slider', 'trx_utils'),
                    'dependency' => array(
                        'element' => 'slider',
                        'value' => 'yes'
                    ),
                    "class" => "",
                    "std" => "no",
                    "value" => array_flip($controls),
                    "type" => "dropdown"
                ),
                array(
                    "param_name" => "slides_space",
                    "heading" => esc_html__("Space between slides", 'trx_utils'),
                    "description" => wp_kses_data( __("Size of space (in px) between slides", 'trx_utils') ),
                    "admin_label" => true,
                    "group" => esc_html__('Slider', 'trx_utils'),
                    'dependency' => array(
                        'element' => 'slider',
                        'value' => 'yes'
                    ),
                    "class" => "",
                    "value" => "0",
                    "type" => "textfield"
                ),
                array(
                    "param_name" => "interval",
                    "heading" => esc_html__("Slides change interval", 'trx_utils'),
                    "description" => wp_kses_data( __("Slides change interval (in milliseconds: 1000ms = 1s)", 'trx_utils') ),
                    "group" => esc_html__('Slider', 'trx_utils'),
                    'dependency' => array(
                        'element' => 'slider',
                        'value' => 'yes'
                    ),
                    "class" => "",
                    "value" => "7000",
                    "type" => "textfield"
                ),
                array(
                    "param_name" => "autoheight",
                    "heading" => esc_html__("Autoheight", 'trx_utils'),
                    "description" => wp_kses_data( __("Change whole slider's height (make it equal current slide's height)", 'trx_utils') ),
                    "group" => esc_html__('Slider', 'trx_utils'),
                    'dependency' => array(
                        'element' => 'slider',
                        'value' => 'yes'
                    ),
                    "class" => "",
                    "value" => array("Autoheight" => "yes" ),
                    "type" => "checkbox"
                ),
                array(
                    "param_name" => "align",
                    "heading" => esc_html__("Alignment", 'trx_utils'),
                    "description" => wp_kses_data( __("Alignment of the services block", 'trx_utils') ),
                    "class" => "",
                    "value" => array_flip(reisen_get_sc_param('align')),
                    "type" => "dropdown"
                ),
                array(
                    "param_name" => "custom",
                    "heading" => esc_html__("Custom", 'trx_utils'),
                    "description" => wp_kses_data( __("Allow get services from inner shortcodes (custom) or get it from specified group (cat)", 'trx_utils') ),
                    "class" => "",
                    "value" => array("Custom services" => "yes" ),
                    "type" => "checkbox"
                ),
                array(
                    "param_name" => "title",
                    "heading" => esc_html__("Title", 'trx_utils'),
                    "description" => wp_kses_data( __("Title for the block", 'trx_utils') ),
                    "admin_label" => true,
                    "group" => esc_html__('Captions', 'trx_utils'),
                    "class" => "",
                    "value" => "",
                    "type" => "textfield"
                ),
                array(
                    "param_name" => "subtitle",
                    "heading" => esc_html__("Subtitle", 'trx_utils'),
                    "description" => wp_kses_data( __("Subtitle for the block", 'trx_utils') ),
                    "group" => esc_html__('Captions', 'trx_utils'),
                    "class" => "",
                    "value" => "",
                    "type" => "textfield"
                ),
                array(
                    "param_name" => "description",
                    "heading" => esc_html__("Description", 'trx_utils'),
                    "description" => wp_kses_data( __("Description for the block", 'trx_utils') ),
                    "group" => esc_html__('Captions', 'trx_utils'),
                    "class" => "",
                    "value" => "",
                    "type" => "textarea"
                ),
                array(
                    "param_name" => "cat",
                    "heading" => esc_html__("Categories", 'trx_utils'),
                    "description" => wp_kses_data( __("Select category to show services. If empty - select services from any category (group) or from IDs list", 'trx_utils') ),
                    "group" => esc_html__('Query', 'trx_utils'),
                    'dependency' => array(
                        'element' => 'custom',
                        'is_empty' => true
                    ),
                    "class" => "",
                    "value" => array_flip(reisen_array_merge(array(0 => esc_html__('- Select category -', 'trx_utils')), $services_groups)),
                    "type" => "dropdown"
                ),
                array(
                    "param_name" => "columns",
                    "heading" => esc_html__("Columns", 'trx_utils'),
                    "description" => wp_kses_data( __("How many columns use to show services list", 'trx_utils') ),
                    "group" => esc_html__('Query', 'trx_utils'),
                    "admin_label" => true,
                    "class" => "",
                    "value" => "4",
                    "type" => "textfield"
                ),
                array(
                    "param_name" => "count",
                    "heading" => esc_html__("Number of posts", 'trx_utils'),
                    "description" => wp_kses_data( __("How many posts will be displayed? If used IDs - this parameter ignored.", 'trx_utils') ),
                    "admin_label" => true,
                    "group" => esc_html__('Query', 'trx_utils'),
                    'dependency' => array(
                        'element' => 'custom',
                        'is_empty' => true
                    ),
                    "class" => "",
                    "value" => "4",
                    "type" => "textfield"
                ),
                array(
                    "param_name" => "offset",
                    "heading" => esc_html__("Offset before select posts", 'trx_utils'),
                    "description" => wp_kses_data( __("Skip posts before select next part.", 'trx_utils') ),
                    "group" => esc_html__('Query', 'trx_utils'),
                    'dependency' => array(
                        'element' => 'custom',
                        'is_empty' => true
                    ),
                    "class" => "",
                    "value" => "0",
                    "type" => "textfield"
                ),
                array(
                    "param_name" => "orderby",
                    "heading" => esc_html__("Post sorting", 'trx_utils'),
                    "description" => wp_kses_data( __("Select desired posts sorting method", 'trx_utils') ),
                    "group" => esc_html__('Query', 'trx_utils'),
                    'dependency' => array(
                        'element' => 'custom',
                        'is_empty' => true
                    ),
                    "std" => "date",
                    "class" => "",
                    "value" => array_flip(reisen_get_sc_param('sorting')),
                    "type" => "dropdown"
                ),
                array(
                    "param_name" => "order",
                    "heading" => esc_html__("Post order", 'trx_utils'),
                    "description" => wp_kses_data( __("Select desired posts order", 'trx_utils') ),
                    "group" => esc_html__('Query', 'trx_utils'),
                    'dependency' => array(
                        'element' => 'custom',
                        'is_empty' => true
                    ),
                    "std" => "desc",
                    "class" => "",
                    "value" => array_flip(reisen_get_sc_param('ordering')),
                    "type" => "dropdown"
                ),
                array(
                    "param_name" => "ids",
                    "heading" => esc_html__("Service's IDs list", 'trx_utils'),
                    "description" => wp_kses_data( __("Comma separated list of service's ID. If set - parameters above (category, count, order, etc.)  are ignored!", 'trx_utils') ),
                    "group" => esc_html__('Query', 'trx_utils'),
                    'dependency' => array(
                        'element' => 'custom',
                        'is_empty' => true
                    ),
                    "class" => "",
                    "value" => "",
                    "type" => "textfield"
                ),
                array(
                    "param_name" => "readmore",
                    "heading" => esc_html__("Read more", 'trx_utils'),
                    "description" => wp_kses_data( __("Caption for the Read more link (if empty - link not showed)", 'trx_utils') ),
                    "admin_label" => true,
                    "group" => esc_html__('Captions', 'trx_utils'),
                    "class" => "",
                    "value" => "",
                    "type" => "textfield"
                ),
                array(
                    "param_name" => "link",
                    "heading" => esc_html__("Button URL", 'trx_utils'),
                    "description" => wp_kses_data( __("Link URL for the button at the bottom of the block", 'trx_utils') ),
                    "group" => esc_html__('Captions', 'trx_utils'),
                    "class" => "",
                    "value" => "",
                    "type" => "textfield"
                ),
                array(
                    "param_name" => "link_caption",
                    "heading" => esc_html__("Button caption", 'trx_utils'),
                    "description" => wp_kses_data( __("Caption for the button at the bottom of the block", 'trx_utils') ),
                    "group" => esc_html__('Captions', 'trx_utils'),
                    "class" => "",
                    "value" => "",
                    "type" => "textfield"
                ),
                reisen_vc_width(),
                reisen_vc_height(),
                reisen_get_vc_param('margin_top'),
                reisen_get_vc_param('margin_bottom'),
                reisen_get_vc_param('margin_left'),
                reisen_get_vc_param('margin_right'),
                reisen_get_vc_param('id'),
                reisen_get_vc_param('class'),
                reisen_get_vc_param('animation'),
                reisen_get_vc_param('css')
            ),
            'default_content' => '
					[trx_services_item title="' . esc_html__( 'Service item 1', 'trx_utils' ) . '"][/trx_services_item]
					[trx_services_item title="' . esc_html__( 'Service item 2', 'trx_utils' ) . '"][/trx_services_item]
					[trx_services_item title="' . esc_html__( 'Service item 3', 'trx_utils' ) . '"][/trx_services_item]
					[trx_services_item title="' . esc_html__( 'Service item 4', 'trx_utils' ) . '"][/trx_services_item]
				',
            'js_view' => 'VcTrxColumnsView'
        ) );


        vc_map( array(
            "base" => "trx_services_item",
            "name" => esc_html__("Services item", 'trx_utils'),
            "description" => wp_kses_data( __("Custom services item - all data pull out from shortcode parameters", 'trx_utils') ),
            "show_settings_on_create" => true,
            "class" => "trx_sc_collection trx_sc_column_item trx_sc_services_item",
            "content_element" => true,
            "is_container" => true,
            'icon' => 'icon_trx_services_item',
            "as_child" => array('only' => 'trx_services'),
            "as_parent" => array('except' => 'trx_services'),
            "params" => array(
                array(
                    "param_name" => "title",
                    "heading" => esc_html__("Title", 'trx_utils'),
                    "description" => wp_kses_data( __("Item's title", 'trx_utils') ),
                    "admin_label" => true,
                    "class" => "",
                    "value" => "",
                    "type" => "textfield"
                ),
                array(
                    "param_name" => "icon",
                    "heading" => esc_html__("Icon", 'trx_utils'),
                    "description" => wp_kses_data( __("Select icon for the item from Fontello icons set", 'trx_utils') ),
                    "admin_label" => true,
                    "class" => "",
                    "value" => reisen_get_sc_param('icons'),
                    "type" => "dropdown"
                ),
                array(
                    "param_name" => "image",
                    "heading" => esc_html__("Image", 'trx_utils'),
                    "description" => wp_kses_data( __("Item's image (if icon is empty)", 'trx_utils') ),
                    "class" => "",
                    "value" => "",
                    "type" => "attach_image"
                ),
                array(
                    "param_name" => "link",
                    "heading" => esc_html__("Link", 'trx_utils'),
                    "description" => wp_kses_data( __("Link on item's page", 'trx_utils') ),
                    "admin_label" => true,
                    "class" => "",
                    "value" => "",
                    "type" => "textfield"
                ),
                array(
                    "param_name" => "readmore",
                    "heading" => esc_html__("Read more", 'trx_utils'),
                    "description" => wp_kses_data( __("Caption for the Read more link (if empty - link not showed)", 'trx_utils') ),
                    "class" => "",
                    "value" => "",
                    "type" => "textfield"
                ),
                reisen_get_vc_param('id'),
                reisen_get_vc_param('class'),
                reisen_get_vc_param('animation'),
                reisen_get_vc_param('css')
            ),
            'js_view' => 'VcTrxColumnItemView'
        ) );

        class WPBakeryShortCode_Trx_Services extends Reisen_Vc_ShortCodeColumns {}
        class WPBakeryShortCode_Trx_Services_Item extends Reisen_Vc_ShortCodeCollection {}

    }
}

// ---------------------------------- [trx_testimonials] ---------------------------------------

if (!function_exists('reisen_sc_testimonials')) {
    function reisen_sc_testimonials($atts, $content=null){
        if (reisen_in_shortcode_blogger()) return '';
        extract(reisen_html_decode(shortcode_atts(array(
            // Individual params
            "style" => "testimonials-1",
            "columns" => 1,
            "slider" => "yes",
            "slides_space" => 0,
            "controls" => "no",
            "interval" => "",
            "autoheight" => "no",
            "align" => "",
            "custom" => "no",
            "ids" => "",
            "cat" => "",
            "count" => "3",
            "offset" => "",
            "orderby" => "date",
            "order" => "desc",
            "scheme" => "",
            "bg_color" => "",
            "bg_image" => "",
            "bg_overlay" => "",
            "bg_texture" => "",
            "title" => "",
            "subtitle" => "",
            "description" => "",
            // Common params
            "id" => "",
            "class" => "",
            "animation" => "",
            "css" => "",
            "width" => "",
            "height" => "",
            "top" => "",
            "bottom" => "",
            "left" => "",
            "right" => ""
        ), $atts)));

        if (empty($id)) $id = "sc_testimonials_".str_replace('.', '', mt_rand());
        if (empty($width)) $width = "100%";
        if (!empty($height) && reisen_param_is_on($autoheight)) $autoheight = "no";
        if (empty($interval)) $interval = mt_rand(5000, 10000);

        if ($bg_image > 0) {
            $attach = wp_get_attachment_image_src( $bg_image, 'full' );
            if (isset($attach[0]) && $attach[0]!='')
                $bg_image = $attach[0];
        }

        if ($bg_overlay > 0) {
            if ($bg_color=='') $bg_color = reisen_get_scheme_color('bg');
            $rgb = reisen_hex2rgb($bg_color);
        }

        $class .= ($class ? ' ' : '') . reisen_get_css_position_as_classes($top, $right, $bottom, $left);

        $ws = reisen_get_css_dimensions_from_values($width);
        $hs = reisen_get_css_dimensions_from_values('', $height);
        $css .= ($hs) . ($ws);

        $count = max(1, (int) $count);
        $columns = max(1, min(12, (int) $columns));
        if (reisen_param_is_off($custom) && $count < $columns) $columns = $count;

        reisen_storage_set('sc_testimonials_data', array(
                'id' => $id,
                'style' => $style,
                'columns' => $columns,
                'counter' => 0,
                'slider' => $slider,
                'css_wh' => $ws . $hs
            )
        );

        if (reisen_param_is_on($slider)) reisen_enqueue_slider('swiper');

        $output = ($bg_color!='' || $bg_image!='' || $bg_overlay>0 || $bg_texture>0 || reisen_strlen($bg_texture)>2 || ($scheme && !reisen_param_is_off($scheme) && !reisen_param_is_inherit($scheme))
                ? '<div class="sc_testimonials_wrap sc_section'
                . ($scheme && !reisen_param_is_off($scheme) && !reisen_param_is_inherit($scheme) ? ' scheme_'.esc_attr($scheme) : '')
                . '"'
                .' style="'
                . ($bg_color !== '' && $bg_overlay==0 ? 'background-color:' . esc_attr($bg_color) . ';' : '')
                . ($bg_image !== '' ? 'background-image:url(' . esc_url($bg_image) . ');' : '')
                . '"'
                . (!reisen_param_is_off($animation) ? ' data-animation="'.esc_attr(reisen_get_animation_classes($animation)).'"' : '')
                . '>'
                . '<div class="sc_section_overlay'.($bg_texture>0 ? ' texture_bg_'.esc_attr($bg_texture) : '') . '"'
                . ' style="' . ($bg_overlay>0 ? 'background-color:rgba('.(int)$rgb['r'].','.(int)$rgb['g'].','.(int)$rgb['b'].','.min(1, max(0, $bg_overlay)).');' : '')
                . (reisen_strlen($bg_texture)>2 ? 'background-image:url('.esc_url($bg_texture).');' : '')
                . '"'
                . ($bg_overlay > 0 ? ' data-overlay="'.esc_attr($bg_overlay).'" data-bg_color="'.esc_attr($bg_color).'"' : '')
                . '>'
                : '')
            . '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
            . ' class="sc_testimonials sc_testimonials_style_'.esc_attr($style)
            . ' ' . esc_attr(reisen_get_template_property($style, 'container_classes'))
            . (!empty($class) ? ' '.esc_attr($class) : '')
            . ($align!='' && $align!='none' ? ' align'.esc_attr($align) : '')
            . '"'
            . ($bg_color=='' && $bg_image=='' && $bg_overlay==0 && ($bg_texture=='' || $bg_texture=='0') && !reisen_param_is_off($animation) ? ' data-animation="'.esc_attr(reisen_get_animation_classes($animation)).'"' : '')
            . ($css!='' ? ' style="'.esc_attr($css).'"' : '')
            . '>'
            . (!empty($subtitle) ? '<h6 class="sc_testimonials_subtitle sc_item_subtitle">' . trim(reisen_strmacros($subtitle)) . '</h6>' : '')
            . (!empty($title) ? '<h3 class="sc_testimonials_title sc_item_title' . (empty($description) ? ' sc_item_title_without_descr' : ' sc_item_title_without_descr') . '">' . trim(reisen_strmacros($title)) . '</h3>' : '')
            . (!empty($description) ? '<div class="sc_testimonials_descr sc_item_descr">' . trim(reisen_strmacros($description)) . '</div>' : '')
            . (reisen_param_is_on($slider)
                ? ('<div class="sc_slider_swiper swiper-slider-container'
                    . ' ' . esc_attr(reisen_get_slider_controls_classes($controls))
                    . (reisen_param_is_on($autoheight) ? ' sc_slider_height_auto' : '')
                    . ($hs ? ' sc_slider_height_fixed' : '')
                    . '"'
                    . (!empty($width) && reisen_strpos($width, '%')===false ? ' data-old-width="' . esc_attr($width) . '"' : '')
                    . (!empty($height) && reisen_strpos($height, '%')===false ? ' data-old-height="' . esc_attr($height) . '"' : '')
                    . ((int) $interval > 0 ? ' data-interval="'.esc_attr($interval).'"' : '')
                    . ($columns > 1 ? ' data-slides-per-view="' . esc_attr($columns) . '"' : '')
                    . ($slides_space > 0 ? ' data-slides-space="' . esc_attr($slides_space) . '"' : '')
                    . ' data-slides-min-width="250"'
                    . '>'
                    . '<div class="slides swiper-wrapper">')
                : ($columns > 1
                    ? '<div class="sc_columns columns_wrap">'
                    : '')
            );

        if (reisen_param_is_on($custom) && $content) {
            $output .= do_shortcode($content);
        } else {
            global $post;

            if (!empty($ids)) {
                $posts = explode(',', $ids);
                $count = count($posts);
            }

            $args = array(
                'post_type' => 'testimonial',
                'post_status' => 'publish',
                'posts_per_page' => $count,
                'ignore_sticky_posts' => true,
                'order' => $order=='asc' ? 'asc' : 'desc',
            );

            if ($offset > 0 && empty($ids)) {
                $args['offset'] = $offset;
            }

            $args = reisen_query_add_sort_order($args, $orderby, $order);
            $args = reisen_query_add_posts_and_cats($args, $ids, 'testimonial', $cat, 'testimonial_group');

            $query = new WP_Query( $args );

            $post_number = 0;

            while ( $query->have_posts() ) {
                $query->the_post();
                $post_number++;
                $args = array(
                    'layout' => $style,
                    'show' => false,
                    'number' => $post_number,
                    'posts_on_page' => ($count > 0 ? $count : $query->found_posts),
                    "descr" => reisen_get_custom_option('post_excerpt_maxlength'.($columns > 1 ? '_masonry' : '')),
                    "orderby" => $orderby,
                    'content' => false,
                    'terms_list' => false,
                    'columns_count' => $columns,
                    'slider' => $slider,
                    'tag_id' => $id ? $id . '_' . $post_number : '',
                    'tag_class' => '',
                    'tag_animation' => '',
                    'tag_css' => '',
                    'tag_css_wh' => $ws . $hs
                );
                $post_data = reisen_get_post_data($args);
                $post_data['post_content'] = wpautop($post_data['post_content']);	// Add <p> around text and paragraphs. Need separate call because 'content'=>false (see above)
                $post_meta = get_post_meta($post_data['post_id'], reisen_storage_get('options_prefix').'_testimonial_data', true);
                $thumb_sizes = reisen_get_thumb_sizes(array('layout' => $style));
                $args['author'] = $post_meta['testimonial_author'];
                $args['position'] = $post_meta['testimonial_position'];
                $args['link'] = !empty($post_meta['testimonial_link']) ? $post_meta['testimonial_link'] : '';
                $args['email'] = $post_meta['testimonial_email'];
                $args['photo'] = $post_data['post_thumb'];
                $mult = reisen_get_retina_multiplier();
                if (empty($args['photo']) && !empty($args['email'])) $args['photo'] = get_avatar($args['email'], $thumb_sizes['w']*$mult);
                $output .= reisen_show_post_layout($args, $post_data);
            }
            wp_reset_postdata();
        }

        if (reisen_param_is_on($slider)) {
            $output .= '</div>'
                . '<div class="sc_slider_controls_wrap"><a class="sc_slider_prev" href="#"></a><a class="sc_slider_next" href="#"></a></div>'
                . '<div class="sc_slider_pagination_wrap"></div>'
                . '</div>';
        } else if ($columns > 1) {
            $output .= '</div>';
        }

        $output .= '</div>'
            . ($bg_color!='' || $bg_image!='' || $bg_overlay>0 || $bg_texture>0 || reisen_strlen($bg_texture)>2 || ($scheme && !reisen_param_is_off($scheme) && !reisen_param_is_inherit($scheme))
                ?  '</div></div>'
                : '');

        // Add template specific scripts and styles
        do_action('reisen_action_blog_scripts', $style);

        return apply_filters('reisen_shortcode_output', $output, 'trx_testimonials', $atts, $content);
    }
    add_shortcode('trx_testimonials', 'reisen_sc_testimonials');
}


if (!function_exists('reisen_sc_testimonials_item')) {
    function reisen_sc_testimonials_item($atts, $content=null){
        if (reisen_in_shortcode_blogger()) return '';
        extract(reisen_html_decode(shortcode_atts(array(
            // Individual params
            "author" => "",
            "position" => "",
            "link" => "",
            "photo" => "",
            "email" => "",
            // Common params
            "id" => "",
            "class" => "",
            "css" => "",
        ), $atts)));

        reisen_storage_inc_array('sc_testimonials_data', 'counter');

        $id = $id ? $id : (reisen_storage_get_array('sc_testimonials_data', 'id') ? reisen_storage_get_array('sc_testimonials_data', 'id') . '_' . reisen_storage_get_array('sc_testimonials_data', 'counter') : '');

        $thumb_sizes = reisen_get_thumb_sizes(array('layout' => reisen_storage_get_array('sc_testimonials_data', 'style')));

        if (empty($photo)) {
            if (!empty($email))
                $mult = reisen_get_retina_multiplier();
            $photo = get_avatar($email, $thumb_sizes['w']*$mult);
        } else {
            if ($photo > 0) {
                $attach = wp_get_attachment_image_src( $photo, 'full' );
                if (isset($attach[0]) && $attach[0]!='')
                    $photo = $attach[0];
            }
            $photo = reisen_get_resized_image_tag($photo, $thumb_sizes['w'], $thumb_sizes['h']);
        }

        $post_data = array(
            'post_content' => do_shortcode($content)
        );
        $args = array(
            'layout' => reisen_storage_get_array('sc_testimonials_data', 'style'),
            'number' => reisen_storage_get_array('sc_testimonials_data', 'counter'),
            'columns_count' => reisen_storage_get_array('sc_testimonials_data', 'columns'),
            'slider' => reisen_storage_get_array('sc_testimonials_data', 'slider'),
            'show' => false,
            'descr'  => 0,
            'tag_id' => $id,
            'tag_class' => $class,
            'tag_animation' => '',
            'tag_css' => $css,
            'tag_css_wh' => reisen_storage_get_array('sc_testimonials_data', 'css_wh'),
            'author' => $author,
            'position' => $position,
            'link' => $link,
            'email' => $email,
            'photo' => $photo
        );
        $output = reisen_show_post_layout($args, $post_data);

        return apply_filters('reisen_shortcode_output', $output, 'trx_testimonials_item', $atts, $content);
    }
    add_shortcode('trx_testimonials_item', 'reisen_sc_testimonials_item');
}
// ---------------------------------- [/trx_testimonials] ---------------------------------------



// Add [trx_testimonials] and [trx_testimonials_item] in the shortcodes list
if (!function_exists('reisen_testimonials_reg_shortcodes')) {
    //Handler of add_filter('reisen_action_shortcodes_list',	'reisen_testimonials_reg_shortcodes');
    function reisen_testimonials_reg_shortcodes() {
        if (reisen_storage_isset('shortcodes')) {

            $testimonials_groups = reisen_get_list_terms(false, 'testimonial_group');
            $testimonials_styles = reisen_get_list_templates('testimonials');
            $controls = reisen_get_list_slider_controls();

            reisen_sc_map_before('trx_title', array(

                // Testimonials
                "trx_testimonials" => array(
                    "title" => esc_html__("Testimonials", 'trx_utils'),
                    "desc" => wp_kses_data( __("Insert testimonials into post (page)", 'trx_utils') ),
                    "decorate" => true,
                    "container" => false,
                    "params" => array(
                        "title" => array(
                            "title" => esc_html__("Title", 'trx_utils'),
                            "desc" => wp_kses_data( __("Title for the block", 'trx_utils') ),
                            "value" => "",
                            "type" => "text"
                        ),
                        "subtitle" => array(
                            "title" => esc_html__("Subtitle", 'trx_utils'),
                            "desc" => wp_kses_data( __("Subtitle for the block", 'trx_utils') ),
                            "value" => "",
                            "type" => "text"
                        ),
                        "description" => array(
                            "title" => esc_html__("Description", 'trx_utils'),
                            "desc" => wp_kses_data( __("Short description for the block", 'trx_utils') ),
                            "value" => "",
                            "type" => "textarea"
                        ),
                        "style" => array(
                            "title" => esc_html__("Testimonials style", 'trx_utils'),
                            "desc" => wp_kses_data( __("Select style to display testimonials", 'trx_utils') ),
                            "value" => "testimonials-1",
                            "type" => "select",
                            "options" => $testimonials_styles
                        ),
                        "columns" => array(
                            "title" => esc_html__("Columns", 'trx_utils'),
                            "desc" => wp_kses_data( __("How many columns use to show testimonials", 'trx_utils') ),
                            "value" => 1,
                            "min" => 1,
                            "max" => 6,
                            "step" => 1,
                            "type" => "spinner"
                        ),
                        "slider" => array(
                            "title" => esc_html__("Slider", 'trx_utils'),
                            "desc" => wp_kses_data( __("Use slider to show testimonials", 'trx_utils') ),
                            "value" => "yes",
                            "type" => "switch",
                            "options" => reisen_get_sc_param('yes_no')
                        ),
                        "controls" => array(
                            "title" => esc_html__("Controls", 'trx_utils'),
                            "desc" => wp_kses_data( __("Slider controls style and position", 'trx_utils') ),
                            "dependency" => array(
                                'slider' => array('yes')
                            ),
                            "divider" => true,
                            "value" => "",
                            "type" => "checklist",
                            "dir" => "horizontal",
                            "options" => $controls
                        ),
                        "slides_space" => array(
                            "title" => esc_html__("Space between slides", 'trx_utils'),
                            "desc" => wp_kses_data( __("Size of space (in px) between slides", 'trx_utils') ),
                            "dependency" => array(
                                'slider' => array('yes')
                            ),
                            "value" => 0,
                            "min" => 0,
                            "max" => 100,
                            "step" => 10,
                            "type" => "spinner"
                        ),
                        "interval" => array(
                            "title" => esc_html__("Slides change interval", 'trx_utils'),
                            "desc" => wp_kses_data( __("Slides change interval (in milliseconds: 1000ms = 1s)", 'trx_utils') ),
                            "dependency" => array(
                                'slider' => array('yes')
                            ),
                            "value" => 7000,
                            "step" => 500,
                            "min" => 0,
                            "type" => "spinner"
                        ),
                        "autoheight" => array(
                            "title" => esc_html__("Autoheight", 'trx_utils'),
                            "desc" => wp_kses_data( __("Change whole slider's height (make it equal current slide's height)", 'trx_utils') ),
                            "dependency" => array(
                                'slider' => array('yes')
                            ),
                            "value" => "yes",
                            "type" => "switch",
                            "options" => reisen_get_sc_param('yes_no')
                        ),
                        "align" => array(
                            "title" => esc_html__("Alignment", 'trx_utils'),
                            "desc" => wp_kses_data( __("Alignment of the testimonials block", 'trx_utils') ),
                            "divider" => true,
                            "value" => "",
                            "type" => "checklist",
                            "dir" => "horizontal",
                            "options" => reisen_get_sc_param('align')
                        ),
                        "custom" => array(
                            "title" => esc_html__("Custom", 'trx_utils'),
                            "desc" => wp_kses_data( __("Allow get testimonials from inner shortcodes (custom) or get it from specified group (cat)", 'trx_utils') ),
                            "divider" => true,
                            "value" => "no",
                            "type" => "switch",
                            "options" => reisen_get_sc_param('yes_no')
                        ),
                        "cat" => array(
                            "title" => esc_html__("Categories", 'trx_utils'),
                            "desc" => wp_kses_data( __("Select categories (groups) to show testimonials. If empty - select testimonials from any category (group) or from IDs list", 'trx_utils') ),
                            "dependency" => array(
                                'custom' => array('no')
                            ),
                            "divider" => true,
                            "value" => "",
                            "type" => "select",
                            "style" => "list",
                            "multiple" => true,
                            "options" => reisen_array_merge(array(0 => esc_html__('- Select category -', 'trx_utils')), $testimonials_groups)
                        ),
                        "count" => array(
                            "title" => esc_html__("Number of posts", 'trx_utils'),
                            "desc" => wp_kses_data( __("How many posts will be displayed? If used IDs - this parameter ignored.", 'trx_utils') ),
                            "dependency" => array(
                                'custom' => array('no')
                            ),
                            "value" => 3,
                            "min" => 1,
                            "max" => 100,
                            "type" => "spinner"
                        ),
                        "offset" => array(
                            "title" => esc_html__("Offset before select posts", 'trx_utils'),
                            "desc" => wp_kses_data( __("Skip posts before select next part.", 'trx_utils') ),
                            "dependency" => array(
                                'custom' => array('no')
                            ),
                            "value" => 0,
                            "min" => 0,
                            "type" => "spinner"
                        ),
                        "orderby" => array(
                            "title" => esc_html__("Post order by", 'trx_utils'),
                            "desc" => wp_kses_data( __("Select desired posts sorting method", 'trx_utils') ),
                            "dependency" => array(
                                'custom' => array('no')
                            ),
                            "value" => "date",
                            "type" => "select",
                            "options" => reisen_get_sc_param('sorting')
                        ),
                        "order" => array(
                            "title" => esc_html__("Post order", 'trx_utils'),
                            "desc" => wp_kses_data( __("Select desired posts order", 'trx_utils') ),
                            "dependency" => array(
                                'custom' => array('no')
                            ),
                            "value" => "desc",
                            "type" => "switch",
                            "size" => "big",
                            "options" => reisen_get_sc_param('ordering')
                        ),
                        "ids" => array(
                            "title" => esc_html__("Post IDs list", 'trx_utils'),
                            "desc" => wp_kses_data( __("Comma separated list of posts ID. If set - parameters above are ignored!", 'trx_utils') ),
                            "dependency" => array(
                                'custom' => array('no')
                            ),
                            "value" => "",
                            "type" => "text"
                        ),
                        "scheme" => array(
                            "title" => esc_html__("Color scheme", 'trx_utils'),
                            "desc" => wp_kses_data( __("Select color scheme for this block", 'trx_utils') ),
                            "value" => "",
                            "type" => "checklist",
                            "options" => reisen_get_sc_param('schemes')
                        ),
                        "bg_color" => array(
                            "title" => esc_html__("Background color", 'trx_utils'),
                            "desc" => wp_kses_data( __("Any background color for this section", 'trx_utils') ),
                            "value" => "",
                            "type" => "color"
                        ),
                        "bg_image" => array(
                            "title" => esc_html__("Background image URL", 'trx_utils'),
                            "desc" => wp_kses_data( __("Select or upload image or write URL from other site for the background", 'trx_utils') ),
                            "readonly" => false,
                            "value" => "",
                            "type" => "media"
                        ),
                        "bg_overlay" => array(
                            "title" => esc_html__("Overlay", 'trx_utils'),
                            "desc" => wp_kses_data( __("Overlay color opacity (from 0.0 to 1.0)", 'trx_utils') ),
                            "min" => "0",
                            "max" => "1",
                            "step" => "0.1",
                            "value" => "0",
                            "type" => "spinner"
                        ),
                        "bg_texture" => array(
                            "title" => esc_html__("Texture", 'trx_utils'),
                            "desc" => wp_kses_data( __("Predefined texture style from 1 to 11. 0 - without texture.", 'trx_utils') ),
                            "min" => "0",
                            "max" => "11",
                            "step" => "1",
                            "value" => "0",
                            "type" => "spinner"
                        ),
                        "width" => reisen_shortcodes_width(),
                        "height" => reisen_shortcodes_height(),
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
                        "name" => "trx_testimonials_item",
                        "title" => esc_html__("Item", 'trx_utils'),
                        "desc" => wp_kses_data( __("Testimonials item (custom parameters)", 'trx_utils') ),
                        "container" => true,
                        "params" => array(
                            "author" => array(
                                "title" => esc_html__("Author", 'trx_utils'),
                                "desc" => wp_kses_data( __("Name of the testimonmials author", 'trx_utils') ),
                                "value" => "",
                                "type" => "text"
                            ),
                            "link" => array(
                                "title" => esc_html__("Link", 'trx_utils'),
                                "desc" => wp_kses_data( __("Link URL to the testimonmials author page", 'trx_utils') ),
                                "value" => "",
                                "type" => "text"
                            ),
                            "email" => array(
                                "title" => esc_html__("E-mail", 'trx_utils'),
                                "desc" => wp_kses_data( __("E-mail of the testimonmials author (to get gravatar)", 'trx_utils') ),
                                "value" => "",
                                "type" => "text"
                            ),
                            "photo" => array(
                                "title" => esc_html__("Photo", 'trx_utils'),
                                "desc" => wp_kses_data( __("Select or upload photo of testimonmials author or write URL of photo from other site", 'trx_utils') ),
                                "value" => "",
                                "type" => "media"
                            ),
                            "_content_" => array(
                                "title" => esc_html__("Testimonials text", 'trx_utils'),
                                "desc" => wp_kses_data( __("Current testimonials text", 'trx_utils') ),
                                "divider" => true,
                                "rows" => 4,
                                "value" => "",
                                "type" => "textarea"
                            ),
                            "id" => reisen_get_sc_param('id'),
                            "class" => reisen_get_sc_param('class'),
                            "css" => reisen_get_sc_param('css')
                        )
                    )
                )

            ));
        }
    }
}


// Add [trx_testimonials] and [trx_testimonials_item] in the VC shortcodes list
if (!function_exists('reisen_testimonials_reg_shortcodes_vc')) {
    //Handler of add_filter('reisen_action_shortcodes_list_vc',	'reisen_testimonials_reg_shortcodes_vc');
    function reisen_testimonials_reg_shortcodes_vc() {

        $testimonials_groups = reisen_get_list_terms(false, 'testimonial_group');
        $testimonials_styles = reisen_get_list_templates('testimonials');
        $controls			 = reisen_get_list_slider_controls();

        // Testimonials
        vc_map( array(
            "base" => "trx_testimonials",
            "name" => esc_html__("Testimonials", 'trx_utils'),
            "description" => wp_kses_data( __("Insert testimonials slider", 'trx_utils') ),
            "category" => esc_html__('Content', 'trx_utils'),
            'icon' => 'icon_trx_testimonials',
            "class" => "trx_sc_columns trx_sc_testimonials",
            "content_element" => true,
            "is_container" => true,
            "show_settings_on_create" => true,
            "as_parent" => array('only' => 'trx_testimonials_item'),
            "params" => array(
                array(
                    "param_name" => "style",
                    "heading" => esc_html__("Testimonials style", 'trx_utils'),
                    "description" => wp_kses_data( __("Select style to display testimonials", 'trx_utils') ),
                    "class" => "",
                    "admin_label" => true,
                    "value" => array_flip($testimonials_styles),
                    "type" => "dropdown"
                ),
                array(
                    "param_name" => "slider",
                    "heading" => esc_html__("Slider", 'trx_utils'),
                    "description" => wp_kses_data( __("Use slider to show testimonials", 'trx_utils') ),
                    "admin_label" => true,
                    "group" => esc_html__('Slider', 'trx_utils'),
                    "class" => "",
                    "std" => "yes",
                    "value" => array_flip(reisen_get_sc_param('yes_no')),
                    "type" => "dropdown"
                ),
                array(
                    "param_name" => "controls",
                    "heading" => esc_html__("Controls", 'trx_utils'),
                    "description" => wp_kses_data( __("Slider controls style and position", 'trx_utils') ),
                    "admin_label" => true,
                    "group" => esc_html__('Slider', 'trx_utils'),
                    'dependency' => array(
                        'element' => 'slider',
                        'value' => 'yes'
                    ),
                    "class" => "",
                    "std" => "no",
                    "value" => array_flip($controls),
                    "type" => "dropdown"
                ),
                array(
                    "param_name" => "slides_space",
                    "heading" => esc_html__("Space between slides", 'trx_utils'),
                    "description" => wp_kses_data( __("Size of space (in px) between slides", 'trx_utils') ),
                    "admin_label" => true,
                    "group" => esc_html__('Slider', 'trx_utils'),
                    'dependency' => array(
                        'element' => 'slider',
                        'value' => 'yes'
                    ),
                    "class" => "",
                    "value" => "0",
                    "type" => "textfield"
                ),
                array(
                    "param_name" => "interval",
                    "heading" => esc_html__("Slides change interval", 'trx_utils'),
                    "description" => wp_kses_data( __("Slides change interval (in milliseconds: 1000ms = 1s)", 'trx_utils') ),
                    "group" => esc_html__('Slider', 'trx_utils'),
                    'dependency' => array(
                        'element' => 'slider',
                        'value' => 'yes'
                    ),
                    "class" => "",
                    "value" => "7000",
                    "type" => "textfield"
                ),
                array(
                    "param_name" => "autoheight",
                    "heading" => esc_html__("Autoheight", 'trx_utils'),
                    "description" => wp_kses_data( __("Change whole slider's height (make it equal current slide's height)", 'trx_utils') ),
                    "group" => esc_html__('Slider', 'trx_utils'),
                    'dependency' => array(
                        'element' => 'slider',
                        'value' => 'yes'
                    ),
                    "class" => "",
                    "value" => array("Autoheight" => "yes" ),
                    "type" => "checkbox"
                ),
                array(
                    "param_name" => "align",
                    "heading" => esc_html__("Alignment", 'trx_utils'),
                    "description" => wp_kses_data( __("Alignment of the testimonials block", 'trx_utils') ),
                    "class" => "",
                    "value" => array_flip(reisen_get_sc_param('align')),
                    "type" => "dropdown"
                ),
                array(
                    "param_name" => "custom",
                    "heading" => esc_html__("Custom", 'trx_utils'),
                    "description" => wp_kses_data( __("Allow get testimonials from inner shortcodes (custom) or get it from specified group (cat)", 'trx_utils') ),
                    "class" => "",
                    "value" => array("Custom slides" => "yes" ),
                    "type" => "checkbox"
                ),
                array(
                    "param_name" => "title",
                    "heading" => esc_html__("Title", 'trx_utils'),
                    "description" => wp_kses_data( __("Title for the block", 'trx_utils') ),
                    "admin_label" => true,
                    "group" => esc_html__('Captions', 'trx_utils'),
                    "class" => "",
                    "value" => "",
                    "type" => "textfield"
                ),
                array(
                    "param_name" => "subtitle",
                    "heading" => esc_html__("Subtitle", 'trx_utils'),
                    "description" => wp_kses_data( __("Subtitle for the block", 'trx_utils') ),
                    "group" => esc_html__('Captions', 'trx_utils'),
                    "class" => "",
                    "value" => "",
                    "type" => "textfield"
                ),
                array(
                    "param_name" => "description",
                    "heading" => esc_html__("Description", 'trx_utils'),
                    "description" => wp_kses_data( __("Description for the block", 'trx_utils') ),
                    "group" => esc_html__('Captions', 'trx_utils'),
                    "class" => "",
                    "value" => "",
                    "type" => "textarea"
                ),
                array(
                    "param_name" => "cat",
                    "heading" => esc_html__("Categories", 'trx_utils'),
                    "description" => wp_kses_data( __("Select categories (groups) to show testimonials. If empty - select testimonials from any category (group) or from IDs list", 'trx_utils') ),
                    "group" => esc_html__('Query', 'trx_utils'),
                    'dependency' => array(
                        'element' => 'custom',
                        'is_empty' => true
                    ),
                    "class" => "",
                    "value" => array_flip(reisen_array_merge(array(0 => esc_html__('- Select category -', 'trx_utils')), $testimonials_groups)),
                    "type" => "dropdown"
                ),
                array(
                    "param_name" => "columns",
                    "heading" => esc_html__("Columns", 'trx_utils'),
                    "description" => wp_kses_data( __("How many columns use to show testimonials", 'trx_utils') ),
                    "group" => esc_html__('Query', 'trx_utils'),
                    "admin_label" => true,
                    "class" => "",
                    "value" => "1",
                    "type" => "textfield"
                ),
                array(
                    "param_name" => "count",
                    "heading" => esc_html__("Number of posts", 'trx_utils'),
                    "description" => wp_kses_data( __("How many posts will be displayed? If used IDs - this parameter ignored.", 'trx_utils') ),
                    "group" => esc_html__('Query', 'trx_utils'),
                    'dependency' => array(
                        'element' => 'custom',
                        'is_empty' => true
                    ),
                    "class" => "",
                    "value" => "3",
                    "type" => "textfield"
                ),
                array(
                    "param_name" => "offset",
                    "heading" => esc_html__("Offset before select posts", 'trx_utils'),
                    "description" => wp_kses_data( __("Skip posts before select next part.", 'trx_utils') ),
                    "group" => esc_html__('Query', 'trx_utils'),
                    'dependency' => array(
                        'element' => 'custom',
                        'is_empty' => true
                    ),
                    "class" => "",
                    "value" => "0",
                    "type" => "textfield"
                ),
                array(
                    "param_name" => "orderby",
                    "heading" => esc_html__("Post sorting", 'trx_utils'),
                    "description" => wp_kses_data( __("Select desired posts sorting method", 'trx_utils') ),
                    "group" => esc_html__('Query', 'trx_utils'),
                    'dependency' => array(
                        'element' => 'custom',
                        'is_empty' => true
                    ),
                    "std" => "date",
                    "class" => "",
                    "value" => array_flip(reisen_get_sc_param('sorting')),
                    "type" => "dropdown"
                ),
                array(
                    "param_name" => "order",
                    "heading" => esc_html__("Post order", 'trx_utils'),
                    "description" => wp_kses_data( __("Select desired posts order", 'trx_utils') ),
                    "group" => esc_html__('Query', 'trx_utils'),
                    'dependency' => array(
                        'element' => 'custom',
                        'is_empty' => true
                    ),
                    "std" => "desc",
                    "class" => "",
                    "value" => array_flip(reisen_get_sc_param('ordering')),
                    "type" => "dropdown"
                ),
                array(
                    "param_name" => "ids",
                    "heading" => esc_html__("Post IDs list", 'trx_utils'),
                    "description" => wp_kses_data( __("Comma separated list of posts ID. If set - parameters above are ignored!", 'trx_utils') ),
                    "group" => esc_html__('Query', 'trx_utils'),
                    'dependency' => array(
                        'element' => 'custom',
                        'is_empty' => true
                    ),
                    "class" => "",
                    "value" => "",
                    "type" => "textfield"
                ),
                array(
                    "param_name" => "scheme",
                    "heading" => esc_html__("Color scheme", 'trx_utils'),
                    "description" => wp_kses_data( __("Select color scheme for this block", 'trx_utils') ),
                    "group" => esc_html__('Colors and Images', 'trx_utils'),
                    "class" => "",
                    "value" => array_flip(reisen_get_sc_param('schemes')),
                    "type" => "dropdown"
                ),
                array(
                    "param_name" => "bg_color",
                    "heading" => esc_html__("Background color", 'trx_utils'),
                    "description" => wp_kses_data( __("Any background color for this section", 'trx_utils') ),
                    "group" => esc_html__('Colors and Images', 'trx_utils'),
                    "class" => "",
                    "value" => "",
                    "type" => "colorpicker"
                ),
                array(
                    "param_name" => "bg_image",
                    "heading" => esc_html__("Background image URL", 'trx_utils'),
                    "description" => wp_kses_data( __("Select background image from library for this section", 'trx_utils') ),
                    "group" => esc_html__('Colors and Images', 'trx_utils'),
                    "class" => "",
                    "value" => "",
                    "type" => "attach_image"
                ),
                array(
                    "param_name" => "bg_overlay",
                    "heading" => esc_html__("Overlay", 'trx_utils'),
                    "description" => wp_kses_data( __("Overlay color opacity (from 0.0 to 1.0)", 'trx_utils') ),
                    "group" => esc_html__('Colors and Images', 'trx_utils'),
                    "class" => "",
                    "value" => "",
                    "type" => "textfield"
                ),
                array(
                    "param_name" => "bg_texture",
                    "heading" => esc_html__("Texture", 'trx_utils'),
                    "description" => wp_kses_data( __("Texture style from 1 to 11. Empty or 0 - without texture.", 'trx_utils') ),
                    "group" => esc_html__('Colors and Images', 'trx_utils'),
                    "class" => "",
                    "value" => "",
                    "type" => "textfield"
                ),
                reisen_vc_width(),
                reisen_vc_height(),
                reisen_get_vc_param('margin_top'),
                reisen_get_vc_param('margin_bottom'),
                reisen_get_vc_param('margin_left'),
                reisen_get_vc_param('margin_right'),
                reisen_get_vc_param('id'),
                reisen_get_vc_param('class'),
                reisen_get_vc_param('animation'),
                reisen_get_vc_param('css')
            ),
            'js_view' => 'VcTrxColumnsView'
        ) );


        vc_map( array(
            "base" => "trx_testimonials_item",
            "name" => esc_html__("Testimonial", 'trx_utils'),
            "description" => wp_kses_data( __("Single testimonials item", 'trx_utils') ),
            "show_settings_on_create" => true,
            "class" => "trx_sc_collection trx_sc_column_item trx_sc_testimonials_item",
            "content_element" => true,
            "is_container" => true,
            'icon' => 'icon_trx_testimonials_item',
            "as_child" => array('only' => 'trx_testimonials'),
            "as_parent" => array('except' => 'trx_testimonials'),
            "params" => array(
                array(
                    "param_name" => "author",
                    "heading" => esc_html__("Author", 'trx_utils'),
                    "description" => wp_kses_data( __("Name of the testimonmials author", 'trx_utils') ),
                    "admin_label" => true,
                    "class" => "",
                    "value" => "",
                    "type" => "textfield"
                ),
                array(
                    "param_name" => "link",
                    "heading" => esc_html__("Link", 'trx_utils'),
                    "description" => wp_kses_data( __("Link URL to the testimonmials author page", 'trx_utils') ),
                    "class" => "",
                    "value" => "",
                    "type" => "textfield"
                ),
                array(
                    "param_name" => "email",
                    "heading" => esc_html__("E-mail", 'trx_utils'),
                    "description" => wp_kses_data( __("E-mail of the testimonmials author", 'trx_utils') ),
                    "class" => "",
                    "value" => "",
                    "type" => "textfield"
                ),
                array(
                    "param_name" => "photo",
                    "heading" => esc_html__("Photo", 'trx_utils'),
                    "description" => wp_kses_data( __("Select or upload photo of testimonmials author or write URL of photo from other site", 'trx_utils') ),
                    "class" => "",
                    "value" => "",
                    "type" => "attach_image"
                ),
                reisen_get_vc_param('id'),
                reisen_get_vc_param('class'),
                reisen_get_vc_param('css')
            ),
            'js_view' => 'VcTrxColumnItemView'
        ) );

        class WPBakeryShortCode_Trx_Testimonials extends Reisen_Vc_ShortCodeColumns {}
        class WPBakeryShortCode_Trx_Testimonials_Item extends Reisen_Vc_ShortCodeCollection {}

    }
}

// Register shortcodes to the internal builder
//------------------------------------------------------------------------
if ( !function_exists( 'reisen_woocommerce_reg_shortcodes' ) ) {
    //Handler of add_action('reisen_action_shortcodes_list', 'reisen_woocommerce_reg_shortcodes', 20);
    function reisen_woocommerce_reg_shortcodes() {

        // WooCommerce - Cart
        reisen_sc_map("woocommerce_cart", array(
                "title" => esc_html__("Woocommerce: Cart", 'reisen'),
                "desc" => wp_kses_data( __("WooCommerce shortcode: show Cart page", 'reisen') ),
                "decorate" => false,
                "container" => false,
                "params" => array()
            )
        );

        // WooCommerce - Checkout
        reisen_sc_map("woocommerce_checkout", array(
                "title" => esc_html__("Woocommerce: Checkout", 'reisen'),
                "desc" => wp_kses_data( __("WooCommerce shortcode: show Checkout page", 'reisen') ),
                "decorate" => false,
                "container" => false,
                "params" => array()
            )
        );

        // WooCommerce - My Account
        reisen_sc_map("woocommerce_my_account", array(
                "title" => esc_html__("Woocommerce: My Account", 'reisen'),
                "desc" => wp_kses_data( __("WooCommerce shortcode: show My Account page", 'reisen') ),
                "decorate" => false,
                "container" => false,
                "params" => array()
            )
        );

        // WooCommerce - Order Tracking
        reisen_sc_map("woocommerce_order_tracking", array(
                "title" => esc_html__("Woocommerce: Order Tracking", 'reisen'),
                "desc" => wp_kses_data( __("WooCommerce shortcode: show Order Tracking page", 'reisen') ),
                "decorate" => false,
                "container" => false,
                "params" => array()
            )
        );

        // WooCommerce - Shop Messages
        reisen_sc_map("shop_messages", array(
                "title" => esc_html__("Woocommerce: Shop Messages", 'reisen'),
                "desc" => wp_kses_data( __("WooCommerce shortcode: show shop messages", 'reisen') ),
                "decorate" => false,
                "container" => false,
                "params" => array()
            )
        );

        // WooCommerce - Product Page
        reisen_sc_map("product_page", array(
                "title" => esc_html__("Woocommerce: Product Page", 'reisen'),
                "desc" => wp_kses_data( __("WooCommerce shortcode: display single product page", 'reisen') ),
                "decorate" => false,
                "container" => false,
                "params" => array(
                    "sku" => array(
                        "title" => esc_html__("SKU", 'reisen'),
                        "desc" => wp_kses_data( __("SKU code of displayed product", 'reisen') ),
                        "value" => "",
                        "type" => "text"
                    ),
                    "id" => array(
                        "title" => esc_html__("ID", 'reisen'),
                        "desc" => wp_kses_data( __("ID of displayed product", 'reisen') ),
                        "value" => "",
                        "type" => "text"
                    ),
                    "posts_per_page" => array(
                        "title" => esc_html__("Number", 'reisen'),
                        "desc" => wp_kses_data( __("How many products showed", 'reisen') ),
                        "value" => "1",
                        "min" => 1,
                        "type" => "spinner"
                    ),
                    "post_type" => array(
                        "title" => esc_html__("Post type", 'reisen'),
                        "desc" => wp_kses_data( __("Post type for the WP query (leave 'product')", 'reisen') ),
                        "value" => "product",
                        "type" => "text"
                    ),
                    "post_status" => array(
                        "title" => esc_html__("Post status", 'reisen'),
                        "desc" => wp_kses_data( __("Display posts only with this status", 'reisen') ),
                        "value" => "publish",
                        "type" => "select",
                        "options" => array(
                            "publish" => esc_html__('Publish', 'reisen'),
                            "protected" => esc_html__('Protected', 'reisen'),
                            "private" => esc_html__('Private', 'reisen'),
                            "pending" => esc_html__('Pending', 'reisen'),
                            "draft" => esc_html__('Draft', 'reisen')
                        )
                    )
                )
            )
        );

        // WooCommerce - Product
        reisen_sc_map("product", array(
                "title" => esc_html__("Woocommerce: Product", 'reisen'),
                "desc" => wp_kses_data( __("WooCommerce shortcode: display one product", 'reisen') ),
                "decorate" => false,
                "container" => false,
                "params" => array(
                    "sku" => array(
                        "title" => esc_html__("SKU", 'reisen'),
                        "desc" => wp_kses_data( __("SKU code of displayed product", 'reisen') ),
                        "value" => "",
                        "type" => "text"
                    ),
                    "id" => array(
                        "title" => esc_html__("ID", 'reisen'),
                        "desc" => wp_kses_data( __("ID of displayed product", 'reisen') ),
                        "value" => "",
                        "type" => "text"
                    )
                )
            )
        );

        // WooCommerce - Best Selling Products
        reisen_sc_map("best_selling_products", array(
                "title" => esc_html__("Woocommerce: Best Selling Products", 'reisen'),
                "desc" => wp_kses_data( __("WooCommerce shortcode: show best selling products", 'reisen') ),
                "decorate" => false,
                "container" => false,
                "params" => array(
                    "per_page" => array(
                        "title" => esc_html__("Number", 'reisen'),
                        "desc" => wp_kses_data( __("How many products showed", 'reisen') ),
                        "value" => 4,
                        "min" => 1,
                        "type" => "spinner"
                    ),
                    "columns" => array(
                        "title" => esc_html__("Columns", 'reisen'),
                        "desc" => wp_kses_data( __("How many columns per row use for products output", 'reisen') ),
                        "value" => 4,
                        "min" => 2,
                        "max" => 4,
                        "type" => "spinner"
                    )
                )
            )
        );

        // WooCommerce - Recent Products
        reisen_sc_map("recent_products", array(
                "title" => esc_html__("Woocommerce: Recent Products", 'reisen'),
                "desc" => wp_kses_data( __("WooCommerce shortcode: show recent products", 'reisen') ),
                "decorate" => false,
                "container" => false,
                "params" => array(
                    "per_page" => array(
                        "title" => esc_html__("Number", 'reisen'),
                        "desc" => wp_kses_data( __("How many products showed", 'reisen') ),
                        "value" => 4,
                        "min" => 1,
                        "type" => "spinner"
                    ),
                    "columns" => array(
                        "title" => esc_html__("Columns", 'reisen'),
                        "desc" => wp_kses_data( __("How many columns per row use for products output", 'reisen') ),
                        "value" => 4,
                        "min" => 2,
                        "max" => 4,
                        "type" => "spinner"
                    ),
                    "orderby" => array(
                        "title" => esc_html__("Order by", 'reisen'),
                        "desc" => wp_kses_data( __("Sorting order for products output", 'reisen') ),
                        "value" => "date",
                        "type" => "select",
                        "options" => array(
                            "date" => esc_html__('Date', 'reisen'),
                            "title" => esc_html__('Title', 'reisen')
                        )
                    ),
                    "order" => array(
                        "title" => esc_html__("Order", 'reisen'),
                        "desc" => wp_kses_data( __("Sorting order for products output", 'reisen') ),
                        "value" => "desc",
                        "type" => "switch",
                        "size" => "big",
                        "options" => reisen_get_sc_param('ordering')
                    )
                )
            )
        );

        // WooCommerce - Related Products
        reisen_sc_map("related_products", array(
                "title" => esc_html__("Woocommerce: Related Products", 'reisen'),
                "desc" => wp_kses_data( __("WooCommerce shortcode: show related products", 'reisen') ),
                "decorate" => false,
                "container" => false,
                "params" => array(
                    "posts_per_page" => array(
                        "title" => esc_html__("Number", 'reisen'),
                        "desc" => wp_kses_data( __("How many products showed", 'reisen') ),
                        "value" => 4,
                        "min" => 1,
                        "type" => "spinner"
                    ),
                    "columns" => array(
                        "title" => esc_html__("Columns", 'reisen'),
                        "desc" => wp_kses_data( __("How many columns per row use for products output", 'reisen') ),
                        "value" => 4,
                        "min" => 2,
                        "max" => 4,
                        "type" => "spinner"
                    ),
                    "orderby" => array(
                        "title" => esc_html__("Order by", 'reisen'),
                        "desc" => wp_kses_data( __("Sorting order for products output", 'reisen') ),
                        "value" => "date",
                        "type" => "select",
                        "options" => array(
                            "date" => esc_html__('Date', 'reisen'),
                            "title" => esc_html__('Title', 'reisen')
                        )
                    )
                )
            )
        );

        // WooCommerce - Featured Products
        reisen_sc_map("featured_products", array(
                "title" => esc_html__("Woocommerce: Featured Products", 'reisen'),
                "desc" => wp_kses_data( __("WooCommerce shortcode: show featured products", 'reisen') ),
                "decorate" => false,
                "container" => false,
                "params" => array(
                    "per_page" => array(
                        "title" => esc_html__("Number", 'reisen'),
                        "desc" => wp_kses_data( __("How many products showed", 'reisen') ),
                        "value" => 4,
                        "min" => 1,
                        "type" => "spinner"
                    ),
                    "columns" => array(
                        "title" => esc_html__("Columns", 'reisen'),
                        "desc" => wp_kses_data( __("How many columns per row use for products output", 'reisen') ),
                        "value" => 4,
                        "min" => 2,
                        "max" => 4,
                        "type" => "spinner"
                    ),
                    "orderby" => array(
                        "title" => esc_html__("Order by", 'reisen'),
                        "desc" => wp_kses_data( __("Sorting order for products output", 'reisen') ),
                        "value" => "date",
                        "type" => "select",
                        "options" => array(
                            "date" => esc_html__('Date', 'reisen'),
                            "title" => esc_html__('Title', 'reisen')
                        )
                    ),
                    "order" => array(
                        "title" => esc_html__("Order", 'reisen'),
                        "desc" => wp_kses_data( __("Sorting order for products output", 'reisen') ),
                        "value" => "desc",
                        "type" => "switch",
                        "size" => "big",
                        "options" => reisen_get_sc_param('ordering')
                    )
                )
            )
        );

        // WooCommerce - Top Rated Products
        reisen_sc_map("featured_products", array(
                "title" => esc_html__("Woocommerce: Top Rated Products", 'reisen'),
                "desc" => wp_kses_data( __("WooCommerce shortcode: show top rated products", 'reisen') ),
                "decorate" => false,
                "container" => false,
                "params" => array(
                    "per_page" => array(
                        "title" => esc_html__("Number", 'reisen'),
                        "desc" => wp_kses_data( __("How many products showed", 'reisen') ),
                        "value" => 4,
                        "min" => 1,
                        "type" => "spinner"
                    ),
                    "columns" => array(
                        "title" => esc_html__("Columns", 'reisen'),
                        "desc" => wp_kses_data( __("How many columns per row use for products output", 'reisen') ),
                        "value" => 4,
                        "min" => 2,
                        "max" => 4,
                        "type" => "spinner"
                    ),
                    "orderby" => array(
                        "title" => esc_html__("Order by", 'reisen'),
                        "desc" => wp_kses_data( __("Sorting order for products output", 'reisen') ),
                        "value" => "date",
                        "type" => "select",
                        "options" => array(
                            "date" => esc_html__('Date', 'reisen'),
                            "title" => esc_html__('Title', 'reisen')
                        )
                    ),
                    "order" => array(
                        "title" => esc_html__("Order", 'reisen'),
                        "desc" => wp_kses_data( __("Sorting order for products output", 'reisen') ),
                        "value" => "desc",
                        "type" => "switch",
                        "size" => "big",
                        "options" => reisen_get_sc_param('ordering')
                    )
                )
            )
        );

        // WooCommerce - Sale Products
        reisen_sc_map("featured_products", array(
                "title" => esc_html__("Woocommerce: Sale Products", 'reisen'),
                "desc" => wp_kses_data( __("WooCommerce shortcode: list products on sale", 'reisen') ),
                "decorate" => false,
                "container" => false,
                "params" => array(
                    "per_page" => array(
                        "title" => esc_html__("Number", 'reisen'),
                        "desc" => wp_kses_data( __("How many products showed", 'reisen') ),
                        "value" => 4,
                        "min" => 1,
                        "type" => "spinner"
                    ),
                    "columns" => array(
                        "title" => esc_html__("Columns", 'reisen'),
                        "desc" => wp_kses_data( __("How many columns per row use for products output", 'reisen') ),
                        "value" => 4,
                        "min" => 2,
                        "max" => 4,
                        "type" => "spinner"
                    ),
                    "orderby" => array(
                        "title" => esc_html__("Order by", 'reisen'),
                        "desc" => wp_kses_data( __("Sorting order for products output", 'reisen') ),
                        "value" => "date",
                        "type" => "select",
                        "options" => array(
                            "date" => esc_html__('Date', 'reisen'),
                            "title" => esc_html__('Title', 'reisen')
                        )
                    ),
                    "order" => array(
                        "title" => esc_html__("Order", 'reisen'),
                        "desc" => wp_kses_data( __("Sorting order for products output", 'reisen') ),
                        "value" => "desc",
                        "type" => "switch",
                        "size" => "big",
                        "options" => reisen_get_sc_param('ordering')
                    )
                )
            )
        );

        // WooCommerce - Product Category
        reisen_sc_map("product_category", array(
                "title" => esc_html__("Woocommerce: Products from category", 'reisen'),
                "desc" => wp_kses_data( __("WooCommerce shortcode: list products in specified category(-ies)", 'reisen') ),
                "decorate" => false,
                "container" => false,
                "params" => array(
                    "per_page" => array(
                        "title" => esc_html__("Number", 'reisen'),
                        "desc" => wp_kses_data( __("How many products showed", 'reisen') ),
                        "value" => 4,
                        "min" => 1,
                        "type" => "spinner"
                    ),
                    "columns" => array(
                        "title" => esc_html__("Columns", 'reisen'),
                        "desc" => wp_kses_data( __("How many columns per row use for products output", 'reisen') ),
                        "value" => 4,
                        "min" => 2,
                        "max" => 4,
                        "type" => "spinner"
                    ),
                    "orderby" => array(
                        "title" => esc_html__("Order by", 'reisen'),
                        "desc" => wp_kses_data( __("Sorting order for products output", 'reisen') ),
                        "value" => "date",
                        "type" => "select",
                        "options" => array(
                            "date" => esc_html__('Date', 'reisen'),
                            "title" => esc_html__('Title', 'reisen')
                        )
                    ),
                    "order" => array(
                        "title" => esc_html__("Order", 'reisen'),
                        "desc" => wp_kses_data( __("Sorting order for products output", 'reisen') ),
                        "value" => "desc",
                        "type" => "switch",
                        "size" => "big",
                        "options" => reisen_get_sc_param('ordering')
                    ),
                    "category" => array(
                        "title" => esc_html__("Categories", 'reisen'),
                        "desc" => wp_kses_data( __("Comma separated category slugs", 'reisen') ),
                        "value" => '',
                        "type" => "text"
                    ),
                    "operator" => array(
                        "title" => esc_html__("Operator", 'reisen'),
                        "desc" => wp_kses_data( __("Categories operator", 'reisen') ),
                        "value" => "IN",
                        "type" => "checklist",
                        "size" => "medium",
                        "options" => array(
                            "IN" => esc_html__('IN', 'reisen'),
                            "NOT IN" => esc_html__('NOT IN', 'reisen'),
                            "AND" => esc_html__('AND', 'reisen')
                        )
                    )
                )
            )
        );

        // WooCommerce - Products
        reisen_sc_map("products", array(
                "title" => esc_html__("Woocommerce: Products", 'reisen'),
                "desc" => wp_kses_data( __("WooCommerce shortcode: list all products", 'reisen') ),
                "decorate" => false,
                "container" => false,
                "params" => array(
                    "skus" => array(
                        "title" => esc_html__("SKUs", 'reisen'),
                        "desc" => wp_kses_data( __("Comma separated SKU codes of products", 'reisen') ),
                        "value" => "",
                        "type" => "text"
                    ),
                    "ids" => array(
                        "title" => esc_html__("IDs", 'reisen'),
                        "desc" => wp_kses_data( __("Comma separated ID of products", 'reisen') ),
                        "value" => "",
                        "type" => "text"
                    ),
                    "columns" => array(
                        "title" => esc_html__("Columns", 'reisen'),
                        "desc" => wp_kses_data( __("How many columns per row use for products output", 'reisen') ),
                        "value" => 4,
                        "min" => 2,
                        "max" => 4,
                        "type" => "spinner"
                    ),
                    "orderby" => array(
                        "title" => esc_html__("Order by", 'reisen'),
                        "desc" => wp_kses_data( __("Sorting order for products output", 'reisen') ),
                        "value" => "date",
                        "type" => "select",
                        "options" => array(
                            "date" => esc_html__('Date', 'reisen'),
                            "title" => esc_html__('Title', 'reisen')
                        )
                    ),
                    "order" => array(
                        "title" => esc_html__("Order", 'reisen'),
                        "desc" => wp_kses_data( __("Sorting order for products output", 'reisen') ),
                        "value" => "desc",
                        "type" => "switch",
                        "size" => "big",
                        "options" => reisen_get_sc_param('ordering')
                    )
                )
            )
        );

        // WooCommerce - Product attribute
        reisen_sc_map("product_attribute", array(
                "title" => esc_html__("Woocommerce: Products by Attribute", 'reisen'),
                "desc" => wp_kses_data( __("WooCommerce shortcode: show products with specified attribute", 'reisen') ),
                "decorate" => false,
                "container" => false,
                "params" => array(
                    "per_page" => array(
                        "title" => esc_html__("Number", 'reisen'),
                        "desc" => wp_kses_data( __("How many products showed", 'reisen') ),
                        "value" => 4,
                        "min" => 1,
                        "type" => "spinner"
                    ),
                    "columns" => array(
                        "title" => esc_html__("Columns", 'reisen'),
                        "desc" => wp_kses_data( __("How many columns per row use for products output", 'reisen') ),
                        "value" => 4,
                        "min" => 2,
                        "max" => 4,
                        "type" => "spinner"
                    ),
                    "orderby" => array(
                        "title" => esc_html__("Order by", 'reisen'),
                        "desc" => wp_kses_data( __("Sorting order for products output", 'reisen') ),
                        "value" => "date",
                        "type" => "select",
                        "options" => array(
                            "date" => esc_html__('Date', 'reisen'),
                            "title" => esc_html__('Title', 'reisen')
                        )
                    ),
                    "order" => array(
                        "title" => esc_html__("Order", 'reisen'),
                        "desc" => wp_kses_data( __("Sorting order for products output", 'reisen') ),
                        "value" => "desc",
                        "type" => "switch",
                        "size" => "big",
                        "options" => reisen_get_sc_param('ordering')
                    ),
                    "attribute" => array(
                        "title" => esc_html__("Attribute", 'reisen'),
                        "desc" => wp_kses_data( __("Attribute name", 'reisen') ),
                        "value" => "",
                        "type" => "text"
                    ),
                    "filter" => array(
                        "title" => esc_html__("Filter", 'reisen'),
                        "desc" => wp_kses_data( __("Attribute value", 'reisen') ),
                        "value" => "",
                        "type" => "text"
                    )
                )
            )
        );

        // WooCommerce - Products Categories
        reisen_sc_map("product_categories", array(
                "title" => esc_html__("Woocommerce: Product Categories", 'reisen'),
                "desc" => wp_kses_data( __("WooCommerce shortcode: show categories with products", 'reisen') ),
                "decorate" => false,
                "container" => false,
                "params" => array(
                    "number" => array(
                        "title" => esc_html__("Number", 'reisen'),
                        "desc" => wp_kses_data( __("How many categories showed", 'reisen') ),
                        "value" => 4,
                        "min" => 1,
                        "type" => "spinner"
                    ),
                    "columns" => array(
                        "title" => esc_html__("Columns", 'reisen'),
                        "desc" => wp_kses_data( __("How many columns per row use for categories output", 'reisen') ),
                        "value" => 4,
                        "min" => 2,
                        "max" => 4,
                        "type" => "spinner"
                    ),
                    "orderby" => array(
                        "title" => esc_html__("Order by", 'reisen'),
                        "desc" => wp_kses_data( __("Sorting order for products output", 'reisen') ),
                        "value" => "date",
                        "type" => "select",
                        "options" => array(
                            "date" => esc_html__('Date', 'reisen'),
                            "title" => esc_html__('Title', 'reisen')
                        )
                    ),
                    "order" => array(
                        "title" => esc_html__("Order", 'reisen'),
                        "desc" => wp_kses_data( __("Sorting order for products output", 'reisen') ),
                        "value" => "desc",
                        "type" => "switch",
                        "size" => "big",
                        "options" => reisen_get_sc_param('ordering')
                    ),
                    "parent" => array(
                        "title" => esc_html__("Parent", 'reisen'),
                        "desc" => wp_kses_data( __("Parent category slug", 'reisen') ),
                        "value" => "",
                        "type" => "text"
                    ),
                    "ids" => array(
                        "title" => esc_html__("IDs", 'reisen'),
                        "desc" => wp_kses_data( __("Comma separated ID of products", 'reisen') ),
                        "value" => "",
                        "type" => "text"
                    ),
                    "hide_empty" => array(
                        "title" => esc_html__("Hide empty", 'reisen'),
                        "desc" => wp_kses_data( __("Hide empty categories", 'reisen') ),
                        "value" => "yes",
                        "type" => "switch",
                        "options" => reisen_get_sc_param('yes_no')
                    )
                )
            )
        );
    }
}



// Register shortcodes to the VC builder
//------------------------------------------------------------------------
if ( !function_exists( 'reisen_woocommerce_reg_shortcodes_vc' ) ) {
    //Handler of add_action('reisen_action_shortcodes_list_vc', 'reisen_woocommerce_reg_shortcodes_vc');
    function reisen_woocommerce_reg_shortcodes_vc() {

        if (false && function_exists('reisen_exists_woocommerce') && reisen_exists_woocommerce()) {

            // WooCommerce - Cart
            //-------------------------------------------------------------------------------------

            vc_map( array(
                "base" => "woocommerce_cart",
                "name" => esc_html__("Cart", 'reisen'),
                "description" => wp_kses_data( __("WooCommerce shortcode: show cart page", 'reisen') ),
                "category" => esc_html__('WooCommerce', 'reisen'),
                'icon' => 'icon_trx_wooc_cart',
                "class" => "trx_sc_alone trx_sc_woocommerce_cart",
                "content_element" => true,
                "is_container" => false,
                "show_settings_on_create" => false,
                "params" => array(
                    array(
                        "param_name" => "dummy",
                        "heading" => esc_html__("Dummy data", 'reisen'),
                        "description" => wp_kses_data( __("Dummy data - not used in shortcodes", 'reisen') ),
                        "class" => "",
                        "value" => "",
                        "type" => "textfield"
                    )
                )
            ) );

            class WPBakeryShortCode_Woocommerce_Cart extends Reisen_Vc_ShortCodeAlone {}


            // WooCommerce - Checkout
            //-------------------------------------------------------------------------------------

            vc_map( array(
                "base" => "woocommerce_checkout",
                "name" => esc_html__("Checkout", 'reisen'),
                "description" => wp_kses_data( __("WooCommerce shortcode: show checkout page", 'reisen') ),
                "category" => esc_html__('WooCommerce', 'reisen'),
                'icon' => 'icon_trx_wooc_checkout',
                "class" => "trx_sc_alone trx_sc_woocommerce_checkout",
                "content_element" => true,
                "is_container" => false,
                "show_settings_on_create" => false,
                "params" => array(
                    array(
                        "param_name" => "dummy",
                        "heading" => esc_html__("Dummy data", 'reisen'),
                        "description" => wp_kses_data( __("Dummy data - not used in shortcodes", 'reisen') ),
                        "class" => "",
                        "value" => "",
                        "type" => "textfield"
                    )
                )
            ) );

            class WPBakeryShortCode_Woocommerce_Checkout extends Reisen_Vc_ShortCodeAlone {}


            // WooCommerce - My Account
            //-------------------------------------------------------------------------------------

            vc_map( array(
                "base" => "woocommerce_my_account",
                "name" => esc_html__("My Account", 'reisen'),
                "description" => wp_kses_data( __("WooCommerce shortcode: show my account page", 'reisen') ),
                "category" => esc_html__('WooCommerce', 'reisen'),
                'icon' => 'icon_trx_wooc_my_account',
                "class" => "trx_sc_alone trx_sc_woocommerce_my_account",
                "content_element" => true,
                "is_container" => false,
                "show_settings_on_create" => false,
                "params" => array(
                    array(
                        "param_name" => "dummy",
                        "heading" => esc_html__("Dummy data", 'reisen'),
                        "description" => wp_kses_data( __("Dummy data - not used in shortcodes", 'reisen') ),
                        "class" => "",
                        "value" => "",
                        "type" => "textfield"
                    )
                )
            ) );

            class WPBakeryShortCode_Woocommerce_My_Account extends Reisen_Vc_ShortCodeAlone {}


            // WooCommerce - Order Tracking
            //-------------------------------------------------------------------------------------

            vc_map( array(
                "base" => "woocommerce_order_tracking",
                "name" => esc_html__("Order Tracking", 'reisen'),
                "description" => wp_kses_data( __("WooCommerce shortcode: show order tracking page", 'reisen') ),
                "category" => esc_html__('WooCommerce', 'reisen'),
                'icon' => 'icon_trx_wooc_order_tracking',
                "class" => "trx_sc_alone trx_sc_woocommerce_order_tracking",
                "content_element" => true,
                "is_container" => false,
                "show_settings_on_create" => false,
                "params" => array(
                    array(
                        "param_name" => "dummy",
                        "heading" => esc_html__("Dummy data", 'reisen'),
                        "description" => wp_kses_data( __("Dummy data - not used in shortcodes", 'reisen') ),
                        "class" => "",
                        "value" => "",
                        "type" => "textfield"
                    )
                )
            ) );

            class WPBakeryShortCode_Woocommerce_Order_Tracking extends Reisen_Vc_ShortCodeAlone {}


            // WooCommerce - Shop Messages
            //-------------------------------------------------------------------------------------

            vc_map( array(
                "base" => "shop_messages",
                "name" => esc_html__("Shop Messages", 'reisen'),
                "description" => wp_kses_data( __("WooCommerce shortcode: show shop messages", 'reisen') ),
                "category" => esc_html__('WooCommerce', 'reisen'),
                'icon' => 'icon_trx_wooc_shop_messages',
                "class" => "trx_sc_alone trx_sc_shop_messages",
                "content_element" => true,
                "is_container" => false,
                "show_settings_on_create" => false,
                "params" => array(
                    array(
                        "param_name" => "dummy",
                        "heading" => esc_html__("Dummy data", 'reisen'),
                        "description" => wp_kses_data( __("Dummy data - not used in shortcodes", 'reisen') ),
                        "class" => "",
                        "value" => "",
                        "type" => "textfield"
                    )
                )
            ) );

            class WPBakeryShortCode_Shop_Messages extends Reisen_Vc_ShortCodeAlone {}


            // WooCommerce - Product Page
            //-------------------------------------------------------------------------------------

            vc_map( array(
                "base" => "product_page",
                "name" => esc_html__("Product Page", 'reisen'),
                "description" => wp_kses_data( __("WooCommerce shortcode: display single product page", 'reisen') ),
                "category" => esc_html__('WooCommerce', 'reisen'),
                'icon' => 'icon_trx_product_page',
                "class" => "trx_sc_single trx_sc_product_page",
                "content_element" => true,
                "is_container" => false,
                "show_settings_on_create" => true,
                "params" => array(
                    array(
                        "param_name" => "sku",
                        "heading" => esc_html__("SKU", 'reisen'),
                        "description" => wp_kses_data( __("SKU code of displayed product", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => "",
                        "type" => "textfield"
                    ),
                    array(
                        "param_name" => "id",
                        "heading" => esc_html__("ID", 'reisen'),
                        "description" => wp_kses_data( __("ID of displayed product", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => "",
                        "type" => "textfield"
                    ),
                    array(
                        "param_name" => "posts_per_page",
                        "heading" => esc_html__("Number", 'reisen'),
                        "description" => wp_kses_data( __("How many products showed", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => "1",
                        "type" => "textfield"
                    ),
                    array(
                        "param_name" => "post_type",
                        "heading" => esc_html__("Post type", 'reisen'),
                        "description" => wp_kses_data( __("Post type for the WP query (leave 'product')", 'reisen') ),
                        "class" => "",
                        "value" => "product",
                        "type" => "textfield"
                    ),
                    array(
                        "param_name" => "post_status",
                        "heading" => esc_html__("Post status", 'reisen'),
                        "description" => wp_kses_data( __("Display posts only with this status", 'reisen') ),
                        "class" => "",
                        "value" => array(
                            esc_html__('Publish', 'reisen') => 'publish',
                            esc_html__('Protected', 'reisen') => 'protected',
                            esc_html__('Private', 'reisen') => 'private',
                            esc_html__('Pending', 'reisen') => 'pending',
                            esc_html__('Draft', 'reisen') => 'draft'
                        ),
                        "type" => "dropdown"
                    )
                )
            ) );

            class WPBakeryShortCode_Product_Page extends Reisen_Vc_ShortCodeSingle {}



            // WooCommerce - Product
            //-------------------------------------------------------------------------------------

            vc_map( array(
                "base" => "product",
                "name" => esc_html__("Product", 'reisen'),
                "description" => wp_kses_data( __("WooCommerce shortcode: display one product", 'reisen') ),
                "category" => esc_html__('WooCommerce', 'reisen'),
                'icon' => 'icon_trx_product',
                "class" => "trx_sc_single trx_sc_product",
                "content_element" => true,
                "is_container" => false,
                "show_settings_on_create" => true,
                "params" => array(
                    array(
                        "param_name" => "sku",
                        "heading" => esc_html__("SKU", 'reisen'),
                        "description" => wp_kses_data( __("Product's SKU code", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => "",
                        "type" => "textfield"
                    ),
                    array(
                        "param_name" => "id",
                        "heading" => esc_html__("ID", 'reisen'),
                        "description" => wp_kses_data( __("Product's ID", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => "",
                        "type" => "textfield"
                    )
                )
            ) );

            class WPBakeryShortCode_Product extends Reisen_Vc_ShortCodeSingle {}


            // WooCommerce - Best Selling Products
            //-------------------------------------------------------------------------------------

            vc_map( array(
                "base" => "best_selling_products",
                "name" => esc_html__("Best Selling Products", 'reisen'),
                "description" => wp_kses_data( __("WooCommerce shortcode: show best selling products", 'reisen') ),
                "category" => esc_html__('WooCommerce', 'reisen'),
                'icon' => 'icon_trx_best_selling_products',
                "class" => "trx_sc_single trx_sc_best_selling_products",
                "content_element" => true,
                "is_container" => false,
                "show_settings_on_create" => true,
                "params" => array(
                    array(
                        "param_name" => "per_page",
                        "heading" => esc_html__("Number", 'reisen'),
                        "description" => wp_kses_data( __("How many products showed", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => "4",
                        "type" => "textfield"
                    ),
                    array(
                        "param_name" => "columns",
                        "heading" => esc_html__("Columns", 'reisen'),
                        "description" => wp_kses_data( __("How many columns per row use for products output", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => "1",
                        "type" => "textfield"
                    )
                )
            ) );

            class WPBakeryShortCode_Best_Selling_Products extends Reisen_Vc_ShortCodeSingle {}



            // WooCommerce - Recent Products
            //-------------------------------------------------------------------------------------

            vc_map( array(
                "base" => "recent_products",
                "name" => esc_html__("Recent Products", 'reisen'),
                "description" => wp_kses_data( __("WooCommerce shortcode: show recent products", 'reisen') ),
                "category" => esc_html__('WooCommerce', 'reisen'),
                'icon' => 'icon_trx_recent_products',
                "class" => "trx_sc_single trx_sc_recent_products",
                "content_element" => true,
                "is_container" => false,
                "show_settings_on_create" => true,
                "params" => array(
                    array(
                        "param_name" => "per_page",
                        "heading" => esc_html__("Number", 'reisen'),
                        "description" => wp_kses_data( __("How many products showed", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => "4",
                        "type" => "textfield"

                    ),
                    array(
                        "param_name" => "columns",
                        "heading" => esc_html__("Columns", 'reisen'),
                        "description" => wp_kses_data( __("How many columns per row use for products output", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => "1",
                        "type" => "textfield"
                    ),
                    array(
                        "param_name" => "orderby",
                        "heading" => esc_html__("Order by", 'reisen'),
                        "description" => wp_kses_data( __("Sorting order for products output", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => array(
                            esc_html__('Date', 'reisen') => 'date',
                            esc_html__('Title', 'reisen') => 'title'
                        ),
                        "type" => "dropdown"
                    ),
                    array(
                        "param_name" => "order",
                        "heading" => esc_html__("Order", 'reisen'),
                        "description" => wp_kses_data( __("Sorting order for products output", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => array_flip(reisen_get_sc_param('ordering')),
                        "type" => "dropdown"
                    )
                )
            ) );

            class WPBakeryShortCode_Recent_Products extends Reisen_Vc_ShortCodeSingle {}



            // WooCommerce - Related Products
            //-------------------------------------------------------------------------------------

            vc_map( array(
                "base" => "related_products",
                "name" => esc_html__("Related Products", 'reisen'),
                "description" => wp_kses_data( __("WooCommerce shortcode: show related products", 'reisen') ),
                "category" => esc_html__('WooCommerce', 'reisen'),
                'icon' => 'icon_trx_related_products',
                "class" => "trx_sc_single trx_sc_related_products",
                "content_element" => true,
                "is_container" => false,
                "show_settings_on_create" => true,
                "params" => array(
                    array(
                        "param_name" => "posts_per_page",
                        "heading" => esc_html__("Number", 'reisen'),
                        "description" => wp_kses_data( __("How many products showed", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => "4",
                        "type" => "textfield"
                    ),
                    array(
                        "param_name" => "columns",
                        "heading" => esc_html__("Columns", 'reisen'),
                        "description" => wp_kses_data( __("How many columns per row use for products output", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => "1",
                        "type" => "textfield"
                    ),
                    array(
                        "param_name" => "orderby",
                        "heading" => esc_html__("Order by", 'reisen'),
                        "description" => wp_kses_data( __("Sorting order for products output", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => array(
                            esc_html__('Date', 'reisen') => 'date',
                            esc_html__('Title', 'reisen') => 'title'
                        ),
                        "type" => "dropdown"
                    )
                )
            ) );

            class WPBakeryShortCode_Related_Products extends Reisen_Vc_ShortCodeSingle {}



            // WooCommerce - Featured Products
            //-------------------------------------------------------------------------------------

            vc_map( array(
                "base" => "featured_products",
                "name" => esc_html__("Featured Products", 'reisen'),
                "description" => wp_kses_data( __("WooCommerce shortcode: show featured products", 'reisen') ),
                "category" => esc_html__('WooCommerce', 'reisen'),
                'icon' => 'icon_trx_featured_products',
                "class" => "trx_sc_single trx_sc_featured_products",
                "content_element" => true,
                "is_container" => false,
                "show_settings_on_create" => true,
                "params" => array(
                    array(
                        "param_name" => "per_page",
                        "heading" => esc_html__("Number", 'reisen'),
                        "description" => wp_kses_data( __("How many products showed", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => "4",
                        "type" => "textfield"
                    ),
                    array(
                        "param_name" => "columns",
                        "heading" => esc_html__("Columns", 'reisen'),
                        "description" => wp_kses_data( __("How many columns per row use for products output", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => "1",
                        "type" => "textfield"
                    ),
                    array(
                        "param_name" => "orderby",
                        "heading" => esc_html__("Order by", 'reisen'),
                        "description" => wp_kses_data( __("Sorting order for products output", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => array(
                            esc_html__('Date', 'reisen') => 'date',
                            esc_html__('Title', 'reisen') => 'title'
                        ),
                        "type" => "dropdown"
                    ),
                    array(
                        "param_name" => "order",
                        "heading" => esc_html__("Order", 'reisen'),
                        "description" => wp_kses_data( __("Sorting order for products output", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => array_flip(reisen_get_sc_param('ordering')),
                        "type" => "dropdown"
                    )
                )
            ) );

            class WPBakeryShortCode_Featured_Products extends Reisen_Vc_ShortCodeSingle {}



            // WooCommerce - Top Rated Products
            //-------------------------------------------------------------------------------------

            vc_map( array(
                "base" => "top_rated_products",
                "name" => esc_html__("Top Rated Products", 'reisen'),
                "description" => wp_kses_data( __("WooCommerce shortcode: show top rated products", 'reisen') ),
                "category" => esc_html__('WooCommerce', 'reisen'),
                'icon' => 'icon_trx_top_rated_products',
                "class" => "trx_sc_single trx_sc_top_rated_products",
                "content_element" => true,
                "is_container" => false,
                "show_settings_on_create" => true,
                "params" => array(
                    array(
                        "param_name" => "per_page",
                        "heading" => esc_html__("Number", 'reisen'),
                        "description" => wp_kses_data( __("How many products showed", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => "4",
                        "type" => "textfield"
                    ),
                    array(
                        "param_name" => "columns",
                        "heading" => esc_html__("Columns", 'reisen'),
                        "description" => wp_kses_data( __("How many columns per row use for products output", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => "1",
                        "type" => "textfield"
                    ),
                    array(
                        "param_name" => "orderby",
                        "heading" => esc_html__("Order by", 'reisen'),
                        "description" => wp_kses_data( __("Sorting order for products output", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => array(
                            esc_html__('Date', 'reisen') => 'date',
                            esc_html__('Title', 'reisen') => 'title'
                        ),
                        "type" => "dropdown"
                    ),
                    array(
                        "param_name" => "order",
                        "heading" => esc_html__("Order", 'reisen'),
                        "description" => wp_kses_data( __("Sorting order for products output", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => array_flip(reisen_get_sc_param('ordering')),
                        "type" => "dropdown"
                    )
                )
            ) );

            class WPBakeryShortCode_Top_Rated_Products extends Reisen_Vc_ShortCodeSingle {}



            // WooCommerce - Sale Products
            //-------------------------------------------------------------------------------------

            vc_map( array(
                "base" => "sale_products",
                "name" => esc_html__("Sale Products", 'reisen'),
                "description" => wp_kses_data( __("WooCommerce shortcode: list products on sale", 'reisen') ),
                "category" => esc_html__('WooCommerce', 'reisen'),
                'icon' => 'icon_trx_sale_products',
                "class" => "trx_sc_single trx_sc_sale_products",
                "content_element" => true,
                "is_container" => false,
                "show_settings_on_create" => true,
                "params" => array(
                    array(
                        "param_name" => "per_page",
                        "heading" => esc_html__("Number", 'reisen'),
                        "description" => wp_kses_data( __("How many products showed", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => "4",
                        "type" => "textfield"
                    ),
                    array(
                        "param_name" => "columns",
                        "heading" => esc_html__("Columns", 'reisen'),
                        "description" => wp_kses_data( __("How many columns per row use for products output", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => "1",
                        "type" => "textfield"
                    ),
                    array(
                        "param_name" => "orderby",
                        "heading" => esc_html__("Order by", 'reisen'),
                        "description" => wp_kses_data( __("Sorting order for products output", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => array(
                            esc_html__('Date', 'reisen') => 'date',
                            esc_html__('Title', 'reisen') => 'title'
                        ),
                        "type" => "dropdown"
                    ),
                    array(
                        "param_name" => "order",
                        "heading" => esc_html__("Order", 'reisen'),
                        "description" => wp_kses_data( __("Sorting order for products output", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => array_flip(reisen_get_sc_param('ordering')),
                        "type" => "dropdown"
                    )
                )
            ) );

            class WPBakeryShortCode_Sale_Products extends Reisen_Vc_ShortCodeSingle {}



            // WooCommerce - Product Category
            //-------------------------------------------------------------------------------------

            vc_map( array(
                "base" => "product_category",
                "name" => esc_html__("Products from category", 'reisen'),
                "description" => wp_kses_data( __("WooCommerce shortcode: list products in specified category(-ies)", 'reisen') ),
                "category" => esc_html__('WooCommerce', 'reisen'),
                'icon' => 'icon_trx_product_category',
                "class" => "trx_sc_single trx_sc_product_category",
                "content_element" => true,
                "is_container" => false,
                "show_settings_on_create" => true,
                "params" => array(
                    array(
                        "param_name" => "per_page",
                        "heading" => esc_html__("Number", 'reisen'),
                        "description" => wp_kses_data( __("How many products showed", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => "4",
                        "type" => "textfield"
                    ),
                    array(
                        "param_name" => "columns",
                        "heading" => esc_html__("Columns", 'reisen'),
                        "description" => wp_kses_data( __("How many columns per row use for products output", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => "1",
                        "type" => "textfield"
                    ),
                    array(
                        "param_name" => "orderby",
                        "heading" => esc_html__("Order by", 'reisen'),
                        "description" => wp_kses_data( __("Sorting order for products output", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => array(
                            esc_html__('Date', 'reisen') => 'date',
                            esc_html__('Title', 'reisen') => 'title'
                        ),
                        "type" => "dropdown"
                    ),
                    array(
                        "param_name" => "order",
                        "heading" => esc_html__("Order", 'reisen'),
                        "description" => wp_kses_data( __("Sorting order for products output", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => array_flip(reisen_get_sc_param('ordering')),
                        "type" => "dropdown"
                    ),
                    array(
                        "param_name" => "category",
                        "heading" => esc_html__("Categories", 'reisen'),
                        "description" => wp_kses_data( __("Comma separated category slugs", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => "",
                        "type" => "textfield"
                    ),
                    array(
                        "param_name" => "operator",
                        "heading" => esc_html__("Operator", 'reisen'),
                        "description" => wp_kses_data( __("Categories operator", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => array(
                            esc_html__('IN', 'reisen') => 'IN',
                            esc_html__('NOT IN', 'reisen') => 'NOT IN',
                            esc_html__('AND', 'reisen') => 'AND'
                        ),
                        "type" => "dropdown"
                    )
                )
            ) );

            class WPBakeryShortCode_Product_Category extends Reisen_Vc_ShortCodeSingle {}



            // WooCommerce - Products
            //-------------------------------------------------------------------------------------

            vc_map( array(
                "base" => "products",
                "name" => esc_html__("Products", 'reisen'),
                "description" => wp_kses_data( __("WooCommerce shortcode: list all products", 'reisen') ),
                "category" => esc_html__('WooCommerce', 'reisen'),
                'icon' => 'icon_trx_products',
                "class" => "trx_sc_single trx_sc_products",
                "content_element" => true,
                "is_container" => false,
                "show_settings_on_create" => true,
                "params" => array(
                    array(
                        "param_name" => "skus",
                        "heading" => esc_html__("SKUs", 'reisen'),
                        "description" => wp_kses_data( __("Comma separated SKU codes of products", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => "",
                        "type" => "textfield"
                    ),
                    array(
                        "param_name" => "ids",
                        "heading" => esc_html__("IDs", 'reisen'),
                        "description" => wp_kses_data( __("Comma separated ID of products", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => "",
                        "type" => "textfield"
                    ),
                    array(
                        "param_name" => "columns",
                        "heading" => esc_html__("Columns", 'reisen'),
                        "description" => wp_kses_data( __("How many columns per row use for products output", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => "1",
                        "type" => "textfield"
                    ),
                    array(
                        "param_name" => "orderby",
                        "heading" => esc_html__("Order by", 'reisen'),
                        "description" => wp_kses_data( __("Sorting order for products output", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => array(
                            esc_html__('Date', 'reisen') => 'date',
                            esc_html__('Title', 'reisen') => 'title'
                        ),
                        "type" => "dropdown"
                    ),
                    array(
                        "param_name" => "order",
                        "heading" => esc_html__("Order", 'reisen'),
                        "description" => wp_kses_data( __("Sorting order for products output", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => array_flip(reisen_get_sc_param('ordering')),
                        "type" => "dropdown"
                    )
                )
            ) );

            class WPBakeryShortCode_Products extends Reisen_Vc_ShortCodeSingle {}




            // WooCommerce - Product Attribute
            //-------------------------------------------------------------------------------------

            vc_map( array(
                "base" => "product_attribute",
                "name" => esc_html__("Products by Attribute", 'reisen'),
                "description" => wp_kses_data( __("WooCommerce shortcode: show products with specified attribute", 'reisen') ),
                "category" => esc_html__('WooCommerce', 'reisen'),
                'icon' => 'icon_trx_product_attribute',
                "class" => "trx_sc_single trx_sc_product_attribute",
                "content_element" => true,
                "is_container" => false,
                "show_settings_on_create" => true,
                "params" => array(
                    array(
                        "param_name" => "per_page",
                        "heading" => esc_html__("Number", 'reisen'),
                        "description" => wp_kses_data( __("How many products showed", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => "4",
                        "type" => "textfield"
                    ),
                    array(
                        "param_name" => "columns",
                        "heading" => esc_html__("Columns", 'reisen'),
                        "description" => wp_kses_data( __("How many columns per row use for products output", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => "1",
                        "type" => "textfield"
                    ),
                    array(
                        "param_name" => "orderby",
                        "heading" => esc_html__("Order by", 'reisen'),
                        "description" => wp_kses_data( __("Sorting order for products output", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => array(
                            esc_html__('Date', 'reisen') => 'date',
                            esc_html__('Title', 'reisen') => 'title'
                        ),
                        "type" => "dropdown"
                    ),
                    array(
                        "param_name" => "order",
                        "heading" => esc_html__("Order", 'reisen'),
                        "description" => wp_kses_data( __("Sorting order for products output", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => array_flip(reisen_get_sc_param('ordering')),
                        "type" => "dropdown"
                    ),
                    array(
                        "param_name" => "attribute",
                        "heading" => esc_html__("Attribute", 'reisen'),
                        "description" => wp_kses_data( __("Attribute name", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => "",
                        "type" => "textfield"
                    ),
                    array(
                        "param_name" => "filter",
                        "heading" => esc_html__("Filter", 'reisen'),
                        "description" => wp_kses_data( __("Attribute value", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => "",
                        "type" => "textfield"
                    )
                )
            ) );

            class WPBakeryShortCode_Product_Attribute extends Reisen_Vc_ShortCodeSingle {}



            // WooCommerce - Products Categories
            //-------------------------------------------------------------------------------------

            vc_map( array(
                "base" => "product_categories",
                "name" => esc_html__("Product Categories", 'reisen'),
                "description" => wp_kses_data( __("WooCommerce shortcode: show categories with products", 'reisen') ),
                "category" => esc_html__('WooCommerce', 'reisen'),
                'icon' => 'icon_trx_product_categories',
                "class" => "trx_sc_single trx_sc_product_categories",
                "content_element" => true,
                "is_container" => false,
                "show_settings_on_create" => true,
                "params" => array(
                    array(
                        "param_name" => "number",
                        "heading" => esc_html__("Number", 'reisen'),
                        "description" => wp_kses_data( __("How many categories showed", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => "4",
                        "type" => "textfield"
                    ),
                    array(
                        "param_name" => "columns",
                        "heading" => esc_html__("Columns", 'reisen'),
                        "description" => wp_kses_data( __("How many columns per row use for categories output", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => "1",
                        "type" => "textfield"
                    ),
                    array(
                        "param_name" => "orderby",
                        "heading" => esc_html__("Order by", 'reisen'),
                        "description" => wp_kses_data( __("Sorting order for products output", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => array(
                            esc_html__('Date', 'reisen') => 'date',
                            esc_html__('Title', 'reisen') => 'title'
                        ),
                        "type" => "dropdown"
                    ),
                    array(
                        "param_name" => "order",
                        "heading" => esc_html__("Order", 'reisen'),
                        "description" => wp_kses_data( __("Sorting order for products output", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => array_flip(reisen_get_sc_param('ordering')),
                        "type" => "dropdown"
                    ),
                    array(
                        "param_name" => "parent",
                        "heading" => esc_html__("Parent", 'reisen'),
                        "description" => wp_kses_data( __("Parent category slug", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => "date",
                        "type" => "textfield"
                    ),
                    array(
                        "param_name" => "ids",
                        "heading" => esc_html__("IDs", 'reisen'),
                        "description" => wp_kses_data( __("Comma separated ID of products", 'reisen') ),
                        "admin_label" => true,
                        "class" => "",
                        "value" => "",
                        "type" => "textfield"
                    ),
                    array(
                        "param_name" => "hide_empty",
                        "heading" => esc_html__("Hide empty", 'reisen'),
                        "description" => wp_kses_data( __("Hide empty categories", 'reisen') ),
                        "class" => "",
                        "value" => array("Hide empty" => "1" ),
                        "type" => "checkbox"
                    )
                )
            ) );

            class WPBakeryShortCode_Products_Categories extends Reisen_Vc_ShortCodeSingle {}

        }
    }
}

// Add RevSlider in the shortcodes params
if ( !function_exists( 'reisen_revslider_shortcodes_params' ) ) {
    //Handler of add_filter( 'reisen_filter_shortcodes_params',			'reisen_revslider_shortcodes_params' );
    function reisen_revslider_shortcodes_params($list=array()) {
        $list["revo_sliders"] = reisen_get_list_revo_sliders();
        return $list;
    }
}

// Reisen shortcodes builder settings
require_once trx_utils_get_file_dir('shortcodes/shortcodes_settings.php');
require_once trx_utils_get_file_dir('shortcodes/theme.shortcodes.php');

// VC shortcodes settings
if ( class_exists('WPBakeryShortCode') ) {
    require_once trx_utils_get_file_dir('shortcodes/shortcodes_vc.php');
}

// Reisen shortcodes implementation
// Using get_template_part(), because shortcodes can be replaced in the child theme
require_once trx_utils_get_file_dir('shortcodes/trx_basic/anchor.php');
require_once trx_utils_get_file_dir('shortcodes/trx_basic/audio.php');
require_once trx_utils_get_file_dir('shortcodes/trx_basic/blogger.php');
require_once trx_utils_get_file_dir('shortcodes/trx_basic/br.php');
require_once trx_utils_get_file_dir('shortcodes/trx_basic/call_to_action.php');
require_once trx_utils_get_file_dir('shortcodes/trx_basic/chat.php');
require_once trx_utils_get_file_dir('shortcodes/trx_basic/columns.php');
require_once trx_utils_get_file_dir('shortcodes/trx_basic/content.php');
require_once trx_utils_get_file_dir('shortcodes/trx_basic/form.php');
require_once trx_utils_get_file_dir('shortcodes/trx_basic/googlemap.php');
require_once trx_utils_get_file_dir('shortcodes/trx_basic/hide.php');
require_once trx_utils_get_file_dir('shortcodes/trx_basic/image.php');
require_once trx_utils_get_file_dir('shortcodes/trx_basic/infobox.php');
require_once trx_utils_get_file_dir('shortcodes/trx_basic/intro.php');
require_once trx_utils_get_file_dir('shortcodes/trx_basic/line.php');
require_once trx_utils_get_file_dir('shortcodes/trx_basic/list.php');
require_once trx_utils_get_file_dir('shortcodes/trx_basic/price_block.php');
require_once trx_utils_get_file_dir('shortcodes/trx_basic/promo.php');
require_once trx_utils_get_file_dir('shortcodes/trx_basic/quote.php');
require_once trx_utils_get_file_dir('shortcodes/trx_basic/reviews.php');
require_once trx_utils_get_file_dir('shortcodes/trx_basic/search.php');
require_once trx_utils_get_file_dir('shortcodes/trx_basic/section.php');
require_once trx_utils_get_file_dir('shortcodes/trx_basic/skills.php');
require_once trx_utils_get_file_dir('shortcodes/trx_basic/slider.php');
require_once trx_utils_get_file_dir('shortcodes/trx_basic/socials.php');
require_once trx_utils_get_file_dir('shortcodes/trx_basic/table.php');
require_once trx_utils_get_file_dir('shortcodes/trx_basic/title.php');
require_once trx_utils_get_file_dir('shortcodes/trx_basic/twitter.php');
require_once trx_utils_get_file_dir('shortcodes/trx_basic/video.php');

require_once trx_utils_get_file_dir('shortcodes/trx_optional/accordion.php');
require_once trx_utils_get_file_dir('shortcodes/trx_optional/button.php');
require_once trx_utils_get_file_dir('shortcodes/trx_optional/countdown.php');
require_once trx_utils_get_file_dir('shortcodes/trx_optional/dropcaps.php');
require_once trx_utils_get_file_dir('shortcodes/trx_optional/gap.php');
require_once trx_utils_get_file_dir('shortcodes/trx_optional/highlight.php');
require_once trx_utils_get_file_dir('shortcodes/trx_optional/icon.php');
require_once trx_utils_get_file_dir('shortcodes/trx_optional/number.php');
require_once trx_utils_get_file_dir('shortcodes/trx_optional/parallax.php');
require_once trx_utils_get_file_dir('shortcodes/trx_optional/popup.php');
require_once trx_utils_get_file_dir('shortcodes/trx_optional/price.php');
require_once trx_utils_get_file_dir('shortcodes/trx_optional/tabs.php');
require_once trx_utils_get_file_dir('shortcodes/trx_optional/toggles.php');
require_once trx_utils_get_file_dir('shortcodes/trx_optional/tooltip.php');
require_once trx_utils_get_file_dir('shortcodes/trx_optional/zoom.php');
?>