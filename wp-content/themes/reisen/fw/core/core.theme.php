<?php
/**
 * Reisen Framework: Theme specific actions
 *
 * @package	reisen
 * @since	reisen 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'reisen_core_theme_setup' ) ) {
	add_action( 'reisen_action_before_init_theme', 'reisen_core_theme_setup', 11 );
	function reisen_core_theme_setup() {
		
		// Editor custom stylesheet - for user
		add_editor_style(reisen_get_file_url('css/editor-style.css'));

		// Make theme available for translation
		// Translations can be filed in the /languages directory
		load_theme_textdomain( 'reisen', reisen_get_folder_dir('languages') );


		/* Front and Admin actions and filters:
		------------------------------------------------------------------------ */

		if ( !is_admin() ) {
			
			/* Front actions and filters:
			------------------------------------------------------------------------ */
	
			// Filters wp_title to print a neat <title> tag based on what is being viewed
			if (floatval(get_bloginfo('version')) < "4.1") {
				add_action('wp_head',						'reisen_wp_title_show');
				add_filter('wp_title',						'reisen_wp_title_modify', 10, 2);
			}

			// Prepare logo text
			add_filter('reisen_filter_prepare_logo_text',	'reisen_prepare_logo_text', 10, 1);
	
			// Add class "widget_number_#' for each widget
			add_filter('dynamic_sidebar_params', 			'reisen_add_widget_number', 10, 1);
	
			// Enqueue scripts and styles
			add_action('wp_enqueue_scripts', 				'reisen_core_frontend_scripts');
			add_action('wp_footer',		 					'reisen_core_frontend_scripts_inline', 1);
			add_action('wp_footer',		 					'reisen_core_frontend_add_js_vars', 2);
			add_action('reisen_action_add_scripts_inline','reisen_core_add_scripts_inline');
			add_filter('reisen_filter_localize_script',	'reisen_core_localize_script');

			// Prepare theme core global variables
			add_action('reisen_action_prepare_globals',	'reisen_core_prepare_globals');
		}

		// Frontend editor: Save post data
		add_action('wp_ajax_frontend_editor_save',		'reisen_callback_frontend_editor_save');

		// Frontend editor: Delete post
		add_action('wp_ajax_frontend_editor_delete', 	'reisen_callback_frontend_editor_delete');

		// Register theme specific nav menus
		reisen_register_theme_menus();

        reisen_register_theme_sidebars();
	}
}




/* Theme init
------------------------------------------------------------------------ */

// Init theme template
function reisen_core_init_theme() {
	if (reisen_storage_get('theme_inited')===true) return;
	reisen_storage_set('theme_inited', true);

	// Load custom options from GET and post/page/cat options
	if (isset($_GET['set']) && $_GET['set']==1) {
		foreach ($_GET as $k=>$v) {
			if (reisen_get_theme_option($k, null) !== null) {
				setcookie($k, $v, 0, '/');
				$_COOKIE[$k] = $v;
			}
		}
	}

	// Get custom options from current category / page / post / shop / event
	reisen_load_custom_options();

	// Fire init theme actions (after custom options are loaded)
	do_action('reisen_action_init_theme');

	// Prepare theme core global variables
	do_action('reisen_action_prepare_globals');

	// Fire after init theme actions
	do_action('reisen_action_after_init_theme');
}


// Prepare theme global variables
if ( !function_exists( 'reisen_core_prepare_globals' ) ) {
	function reisen_core_prepare_globals() {
		if (!is_admin()) {
			// Logo text and slogan
			reisen_storage_set('logo_text', apply_filters('reisen_filter_prepare_logo_text', reisen_get_custom_option('logo_text')));
			reisen_storage_set('logo_slogan', get_bloginfo('description'));
			
			// Logo image and icons
			$logo        = reisen_get_logo_icon('logo');
			$logo_side   = reisen_get_logo_icon('logo_side');
			$logo_fixed  = reisen_get_logo_icon('logo_fixed');
			$logo_footer = reisen_get_logo_icon('logo_footer');
			reisen_storage_set('logo', $logo);
			reisen_storage_set('logo_icon',   reisen_get_logo_icon('logo_icon'));
			reisen_storage_set('logo_side',   $logo_side   ? $logo_side   : $logo);
			reisen_storage_set('logo_fixed',  $logo_fixed  ? $logo_fixed  : $logo);
			reisen_storage_set('logo_footer', $logo_footer ? $logo_footer : $logo);
	
			$shop_mode = '';
			if (reisen_get_custom_option('show_mode_buttons')=='yes')
				$shop_mode = reisen_get_value_gpc('reisen_shop_mode');
			if (empty($shop_mode))
				$shop_mode = reisen_get_custom_option('shop_mode', '');
			if (empty($shop_mode) || !is_archive())
				$shop_mode = 'thumbs';
			reisen_storage_set('shop_mode', $shop_mode);
		}
	}
}


// Return url for the uploaded logo image
if ( !function_exists( 'reisen_get_logo_icon' ) ) {
	function reisen_get_logo_icon($slug) {
		// This way ignore the 'Retina' setting and load retina logo on any display with retina support
		$mult = (int) reisen_get_value_gpc('reisen_retina', 0) > 0 ? 2 : 1;
		$logo_icon = '';
		if ($mult > 1) 			$logo_icon = reisen_get_custom_option($slug.'_retina');
		if (empty($logo_icon))	$logo_icon = reisen_get_custom_option($slug);
		return $logo_icon;
	}
}


// Display logo image with text and slogan (if specified)
if ( !function_exists( 'reisen_show_logo' ) ) {
	function reisen_show_logo($logo_main=true, $logo_fixed=false, $logo_footer=false, $logo_side=false, $logo_text=true, $logo_slogan=true) {
		if ($logo_main===true) 		$logo_main   = reisen_storage_get('logo');
		if ($logo_fixed===true)		$logo_fixed  = reisen_storage_get('logo_fixed');
		if ($logo_footer===true)	$logo_footer = reisen_storage_get('logo_footer');
		if ($logo_side===true)		$logo_side   = reisen_storage_get('logo_side');
		if ($logo_text===true)		$logo_text   = reisen_storage_get('logo_text');
		if ($logo_slogan===true)	$logo_slogan = reisen_storage_get('logo_slogan');
		if (empty($logo_main) && empty($logo_fixed) && empty($logo_footer) && empty($logo_side) && empty($logo_text))
			 $logo_text = get_bloginfo('name');
		if ($logo_main || $logo_fixed || $logo_footer || $logo_side || $logo_text) {
		?>
		<div class="logo">
			<a href="<?php echo esc_url(home_url('/')); ?>"><?php
				if (!empty($logo_main)) {
					$attr = reisen_getimagesize($logo_main);
					echo '<img src="'.esc_url($logo_main).'" class="logo_main" alt="'.esc_attr($logo_text).'"'.(!empty($attr[3]) ? ' '.trim($attr[3]) : '').'>';
				}
				if (!empty($logo_fixed)) {
					$attr = reisen_getimagesize($logo_fixed);
					echo '<img src="'.esc_url($logo_fixed).'" class="logo_fixed" alt="'.esc_attr($logo_text).'"'.(!empty($attr[3]) ? ' '.trim($attr[3]) : '').'>';
				}
				if (!empty($logo_footer)) {
					$attr = reisen_getimagesize($logo_footer);
					echo '<img src="'.esc_url($logo_footer).'" class="logo_footer" alt="'.esc_attr($logo_text).'"'.(!empty($attr[3]) ? ' '.trim($attr[3]) : '').'>';
				}
				if (!empty($logo_side)) {
					$attr = reisen_getimagesize($logo_side);
					echo '<img src="'.esc_url($logo_side).'" class="logo_side" alt="'.esc_attr($logo_text).'"'.(!empty($attr[3]) ? ' '.trim($attr[3]) : '').'>';
				}
				echo !empty($logo_text) ? '<div class="logo_text">'.trim($logo_text).'</div>' : '';
				echo !empty($logo_slogan) ? '<br><div class="logo_slogan">' . esc_html($logo_slogan) . '</div>' : '';
			?></a>
		</div>
		<?php 
		}
	} 
}

// Display logo image with text and slogan (if specified)
if ( !function_exists( 'reisen_show_hlogo' ) ) {
    function reisen_show_hlogo($logo_main=true, $logo_fixed=false, $logo_footer=false, $logo_side=false, $logo_text=true, $logo_slogan=true) {
        if ($logo_main===true) 		$logo_main   = reisen_get_theme_option('logo');
        if ($logo_fixed===true)		$logo_fixed  = reisen_storage_get('logo_fixed');
        if ($logo_footer===true)	$logo_footer = reisen_storage_get('logo_footer');
        if ($logo_side===true)		$logo_side   = reisen_storage_get('logo_side');
        if ($logo_text===true)		$logo_text   = reisen_storage_get('logo_text');
        if ($logo_slogan===true)	$logo_slogan = reisen_storage_get('logo_slogan');
        if (empty($logo_main) && empty($logo_fixed) && empty($logo_footer) && empty($logo_side) && empty($logo_text))
            $logo_text = get_bloginfo('name');
        if ($logo_main || $logo_fixed || $logo_footer || $logo_side || $logo_text) {
            ?>
            <div class="logo">
                <a href="<?php echo esc_url(home_url('/')); ?>"><?php
                    if (!empty($logo_main)) {
                        $attr = reisen_getimagesize($logo_main);
                        echo '<img src="'.esc_url($logo_main).'" class="logo_main" alt="'.esc_attr($logo_text).'"'.(!empty($attr[3]) ? ' '.trim($attr[3]) : '').'>';
                    }
                    if (!empty($logo_fixed)) {
                        $attr = reisen_getimagesize($logo_fixed);
                        echo '<img src="'.esc_url($logo_fixed).'" class="logo_fixed" alt="'.esc_attr($logo_text).'"'.(!empty($attr[3]) ? ' '.trim($attr[3]) : '').'>';
                    }
                    if (!empty($logo_footer)) {
                        $attr = reisen_getimagesize($logo_footer);
                        echo '<img src="'.esc_url($logo_footer).'" class="logo_footer" alt="'.esc_attr($logo_text).'"'.(!empty($attr[3]) ? ' '.trim($attr[3]) : '').'>';
                    }
                    if (!empty($logo_side)) {
                        $attr = reisen_getimagesize($logo_side);
                        echo '<img src="'.esc_url($logo_side).'" class="logo_side" alt="'.esc_attr($logo_text).'"'.(!empty($attr[3]) ? ' '.trim($attr[3]) : '').'>';
                    }
                    echo !empty($logo_text) ? '<div class="logo_text">'.trim($logo_text).'</div>' : '';
                    echo !empty($logo_slogan) ? '<br><div class="logo_slogan">' . esc_html($logo_slogan) . '</div>' : '';
                    ?></a>
            </div>
        <?php
        }
    }
}

// Add menu locations
if ( !function_exists( 'reisen_register_theme_menus' ) ) {
	function reisen_register_theme_menus() {
		register_nav_menus(apply_filters('reisen_filter_add_theme_menus', array(
			'menu_main'		=> esc_html__('Main Menu', 'reisen'),
			'menu_footer'	=> esc_html__('Footer Menu', 'reisen'),
			'menu_side'		=> esc_html__('Side Menu', 'reisen')
		)));
	}
}


// Register widgetized area
if ( !function_exists( 'reisen_register_theme_sidebars' ) ) {
    add_action('widgets_init', 'reisen_register_theme_sidebars');
	function reisen_register_theme_sidebars($sidebars=array()) {
		if (!is_array($sidebars)) $sidebars = array();
		// Custom sidebars
		$custom = reisen_get_theme_option('custom_sidebars');
		if (is_array($custom) && count($custom) > 0) {
			foreach ($custom as $i => $sb) {
				if (trim(chop($sb))=='') continue;
				$sidebars['sidebar_custom_'.($i)]  = $sb;
			}
		}
		$sidebars = apply_filters( 'reisen_filter_add_theme_sidebars', $sidebars );
        $registered = reisen_storage_get('registered_sidebars');
        if (!is_array($registered)) $registered = array();
		if (is_array($sidebars) && count($sidebars) > 0) {
			foreach ($sidebars as $id=>$name) {
                if (isset($registered[$id])) continue;
                $registered[$id] = $name;
				register_sidebar( array_merge( array(
													'name'          => $name,
													'id'            => $id
												),
												reisen_storage_get('widgets_args')
									)
				);
			}
		}
        reisen_storage_set('registered_sidebars', $registered);
	}
}





/* Front actions and filters:
------------------------------------------------------------------------ */

//  Enqueue scripts and styles
if ( !function_exists( 'reisen_core_frontend_scripts' ) ) {
	function reisen_core_frontend_scripts() {
		
		// Modernizr will load in head before other scripts and styles
		// Use older version (from photostack)
		wp_enqueue_script( 'modernizr', reisen_get_file_url('js/photostack/modernizr.min.js'), array(), null, false );
		
		// Enqueue styles
		//-----------------------------------------------------------------------------------------------------
		
		// Prepare custom fonts
	    if ( 'off' !== _x( 'on', 'Google fonts: on or off', 'reisen' ) ) {
			$fonts = reisen_get_list_fonts(false);
			$theme_fonts = array();
			$custom_fonts = reisen_get_custom_fonts();
			if (is_array($custom_fonts) && count($custom_fonts) > 0) {
				foreach ($custom_fonts as $s=>$f) {
					if (!empty($f['font-family']) && !reisen_is_inherit_option($f['font-family'])) $theme_fonts[$f['font-family']] = 1;
				}
			}
			// Prepare current theme fonts
			$theme_fonts = apply_filters('reisen_filter_used_fonts', $theme_fonts);
			// Link to selected fonts
			if (is_array($theme_fonts) && count($theme_fonts) > 0) {
				$google_fonts = '';
				foreach ($theme_fonts as $font=>$v) {
					if (isset($fonts[$font])) {
						$font_name = ($pos=reisen_strpos($font,' ('))!==false ? reisen_substr($font, 0, $pos) : $font;
						if (!empty($fonts[$font]['css'])) {
							$css = $fonts[$font]['css'];
							wp_enqueue_style( 'reisen-font-'.str_replace(' ', '-', $font_name).'-style', $css, array(), null );
						} else {
							$google_fonts .= ($google_fonts ? '|' : '')
								. (!empty($fonts[$font]['link']) ? $fonts[$font]['link'] : str_replace(' ', '+', $font_name).':300,300italic,400,400italic,700,700italic');
						}
					}
				}
				if ($google_fonts) {
                    /*
                   Translators: If there are characters in your language that are not supported
                   by chosen font(s), translate this to 'off'. Do not translate into your own language.
                   */
                    $google_fonts_enabled = ( 'off' !== esc_html_x( 'on', 'Google fonts: on or off', 'reisen' ) );
                    if ( $google_fonts_enabled ) {
                        wp_enqueue_style( 'reisen-font-google-fonts-style', add_query_arg( 'family', $google_fonts . '&subset=' . reisen_get_theme_option('fonts_subset'), "//fonts.googleapis.com/css" ), array(), null );

                    }
				}
			}
		}
		
		// Fontello styles must be loaded before main stylesheet
		wp_enqueue_style( 'fontello-style',  reisen_get_file_url('css/fontello/css/fontello.css'),  array(), null);

		// Main stylesheet
		wp_enqueue_style( 'reisen-main-style', get_stylesheet_uri(), array(), null );
		
		// Animations
		if (reisen_get_theme_option('css_animation')=='yes' && (reisen_get_theme_option('animation_on_mobile')=='yes' || !wp_is_mobile()) && !reisen_vc_is_frontend())
			wp_enqueue_style( 'reisen-animation-style',	reisen_get_file_url('css/core.animation.css'), array(), null );

		// Theme stylesheets
		do_action('reisen_action_add_styles');

		// Responsive
		if (reisen_get_theme_option('responsive_layouts') == 'yes') {
			$suffix = reisen_param_is_off(reisen_get_custom_option('show_sidebar_outer')) ? '' : '-outer';
			wp_enqueue_style( 'reisen-responsive-style', reisen_get_file_url('css/responsive'.($suffix).'.css'), array(), null );
			do_action('reisen_action_add_responsive');
			$css = apply_filters('reisen_filter_add_responsive_inline', '');
			if (!empty($css)) wp_add_inline_style( 'reisen-responsive-style', $css );
		}

		// Disable loading JQuery UI CSS
		wp_deregister_style('jquery_ui');
		wp_deregister_style('date-picker-css');


		// Enqueue scripts	
		//----------------------------------------------------------------------------------------------------------------------------
		
		// Load separate theme scripts
		wp_enqueue_script( 'superfish', reisen_get_file_url('js/superfish.js'), array('jquery'), null, true );
		if (in_array(reisen_get_theme_option('menu_hover'), array('slide_line', 'slide_box'))) {
			wp_enqueue_script( 'slidemenu-script', reisen_get_file_url('js/jquery.slidemenu.js'), array('jquery'), null, true );
		}

		wp_enqueue_script( 'reisen-core-utils-script',	reisen_get_file_url('js/core.utils.js'), array('jquery'), null, true );
		wp_enqueue_script( 'reisen-core-init-script',	reisen_get_file_url('js/core.init.js'), array('jquery'), null, true );	
		wp_enqueue_script( 'reisen-theme-init-script',	reisen_get_file_url('js/theme.init.js'), array('jquery'), null, true );	

		// Media elements library	
		if (reisen_get_theme_option('use_mediaelement')=='yes') {
			wp_enqueue_style ( 'mediaelement' );
			wp_enqueue_style ( 'wp-mediaelement' );
			wp_enqueue_script( 'mediaelement' );
			wp_enqueue_script( 'wp-mediaelement' );
		}
		
		// Video background
		if (reisen_get_custom_option('show_video_bg') == 'yes' && reisen_get_custom_option('video_bg_youtube_code') != '') {
			wp_enqueue_script( 'video-bg-script', reisen_get_file_url('js/jquery.tubular.1.0.js'), array('jquery'), null, true );
		}
			
		// Social share buttons
		if (is_singular() && !reisen_storage_get('blog_streampage') && reisen_get_custom_option('show_share')!='hide') {
			wp_enqueue_script( 'social-share-script', reisen_get_file_url('js/social/social-share.js'), array('jquery'), null, true );
		}

		// Comments
		if ( is_singular() && !reisen_storage_get('blog_streampage') && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply', false, array(), null, true );
		}

		// Custom panel
		if (reisen_get_theme_option('show_theme_customizer') == 'yes') {
			if (file_exists(reisen_get_file_dir('core/core.customizer/front.customizer.css')))
				wp_enqueue_style(  'reisen-customizer-style',  reisen_get_file_url('core/core.customizer/front.customizer.css'), array(), null );
			if (file_exists(reisen_get_file_dir('core/core.customizer/front.customizer.js')))
				wp_enqueue_script( 'reisen-customizer-script', reisen_get_file_url('core/core.customizer/front.customizer.js'), array(), null, true );	
		}
		
		//Debug utils
		if (reisen_get_theme_option('debug_mode')=='yes') {
			wp_enqueue_script( 'reisen-core-debug-script', reisen_get_file_url('js/core.debug.js'), array(), null, true );
		}

		// Theme scripts
		do_action('reisen_action_add_scripts');
	}
}

//  Enqueue Swiper Slider scripts and styles
if ( !function_exists( 'reisen_enqueue_slider' ) ) {
	function reisen_enqueue_slider($engine='all') {
		if ($engine=='all' || $engine=='swiper') {
			wp_enqueue_style(  'swiperslider-style', 			reisen_get_file_url('js/swiper/swiper.css'), array(), null );
			// jQuery version of Swiper conflict with Revolution Slider!!! Use DOM version
			wp_enqueue_script( 'swiperslider-script', 			reisen_get_file_url('js/swiper/swiper.js'), array(), null, true );
		}
	}
}

//  Enqueue Photostack gallery
if ( !function_exists( 'reisen_enqueue_polaroid' ) ) {
	function reisen_enqueue_polaroid() {
		wp_enqueue_style(  'polaroid-style', 	reisen_get_file_url('js/photostack/component.css'), array(), null );
		wp_enqueue_script( 'classie-script',		reisen_get_file_url('js/photostack/classie.js'), array(), null, true );
		wp_enqueue_script( 'polaroid-script',	reisen_get_file_url('js/photostack/photostack.js'), array(), null, true );
	}
}

//  Enqueue Messages scripts and styles
if ( !function_exists( 'reisen_enqueue_messages' ) ) {
	function reisen_enqueue_messages() {
		wp_enqueue_style(  'reisen-messages-style',		reisen_get_file_url('js/core.messages/core.messages.css'), array(), null );
		wp_enqueue_script( 'reisen-messages-script',	reisen_get_file_url('js/core.messages/core.messages.js'),  array('jquery'), null, true );
	}
}

//  Enqueue Portfolio hover scripts and styles
if ( !function_exists( 'reisen_enqueue_portfolio' ) ) {
	function reisen_enqueue_portfolio($hover='') {
		wp_enqueue_style( 'reisen-portfolio-style',  reisen_get_file_url('css/core.portfolio.css'), array(), null );
		if (reisen_strpos($hover, 'effect_dir')!==false)
			wp_enqueue_script( 'hoverdir', reisen_get_file_url('js/hover/jquery.hoverdir.js'), array(), null, true );
	}
}

//  Enqueue Charts and Diagrams scripts and styles
if ( !function_exists( 'reisen_enqueue_diagram' ) ) {
	function reisen_enqueue_diagram($type='all') {
		if ($type=='all' || $type=='pie') wp_enqueue_script( 'diagram-chart-script',	reisen_get_file_url('js/diagram/chart.min.js'), array(), null, true );
		if ($type=='all' || $type=='arc') wp_enqueue_script( 'diagram-raphael-script',	reisen_get_file_url('js/diagram/diagram.raphael.min.js'), array(), 'no-compose', true );
	}
}

// Enqueue Theme Popup scripts and styles
// Link must have attribute: data-rel="popup" or data-rel="popup[gallery]"
if ( !function_exists( 'reisen_enqueue_popup' ) ) {
	function reisen_enqueue_popup($engine='') {
		if ($engine=='pretty' || (empty($engine) && reisen_get_theme_option('popup_engine')=='pretty')) {
			wp_enqueue_style(  'prettyphoto-style',	reisen_get_file_url('js/prettyphoto/css/prettyPhoto.css'), array(), null );
			wp_enqueue_script( 'prettyphoto-script',	reisen_get_file_url('js/prettyphoto/jquery.prettyPhoto.min.js'), array('jquery'), 'no-compose', true );
		} else if ($engine=='magnific' || (empty($engine) && reisen_get_theme_option('popup_engine')=='magnific')) {
			wp_enqueue_style(  'magnific-style',	reisen_get_file_url('js/magnific/magnific-popup.css'), array(), null );
			wp_enqueue_script( 'magnific-script',reisen_get_file_url('js/magnific/jquery.magnific-popup.min.js'), array('jquery'), '', true );
		} else if ($engine=='internal' || (empty($engine) && reisen_get_theme_option('popup_engine')=='internal')) {
			reisen_enqueue_messages();
		}
	}
}

//  Add inline scripts in the footer hook
if ( !function_exists( 'reisen_core_frontend_scripts_inline' ) ) {
	//Handler of add_action('wp_footer', 'reisen_core_frontend_scripts_inline', 1);
	function reisen_core_frontend_scripts_inline() {
		do_action('reisen_action_add_scripts_inline');
	}
}

//  Localize scripts in the footer hook
if ( !function_exists( 'reisen_core_frontend_add_js_vars' ) ) {
	//Handler of add_action('wp_footer', 'reisen_core_frontend_add_js_vars', 2);
	function reisen_core_frontend_add_js_vars() {
		$vars = apply_filters( 'reisen_filter_localize_script', reisen_storage_empty('js_vars') ? array() : reisen_storage_get('js_vars'));
		if (!empty($vars)) wp_localize_script( 'reisen-core-init-script', 'REISEN_STORAGE', $vars);
	}
}


//  Add property="stylesheet" into all tags <link> in the footer
if (!function_exists('reisen_core_add_property_to_link')) {
	function reisen_core_add_property_to_link($link, $handle='', $href='') {
		return str_replace('<link ', '<link property="stylesheet" ', $link);
	}
}

//  Add inline scripts in the footer
if (!function_exists('reisen_core_add_scripts_inline')) {
	//Handler of add_action('reisen_action_add_scripts_inline','reisen_core_add_scripts_inline');
	function reisen_core_add_scripts_inline() {
		// System message
		$msg = reisen_get_system_message(true); 
		if (!empty($msg['message'])) reisen_enqueue_messages();
		reisen_storage_set_array('js_vars', 'system_message',	$msg);
	}
}

//  Localize script
if (!function_exists('reisen_core_localize_script')) {
	//Handler of add_filter('reisen_filter_localize_script',	'reisen_core_localize_script');
	function reisen_core_localize_script($vars) {

		// AJAX parameters
		$vars['ajax_url'] = esc_url(admin_url('admin-ajax.php'));
		$vars['ajax_nonce'] = wp_create_nonce(admin_url('admin-ajax.php'));

		// Site base url
		$vars['site_url'] = esc_url(get_site_url());

		// Site protocol
		$vars['site_protocol'] = reisen_get_protocol();
			
		// VC frontend edit mode
		$vars['vc_edit_mode'] = function_exists('reisen_vc_is_frontend') && reisen_vc_is_frontend();
			
		// Theme base font
		$vars['theme_font'] = reisen_get_custom_font_settings('p', 'font-family');
			
		// Theme colors
		$vars['theme_color'] = reisen_get_scheme_color('text_dark');
		$vars['theme_bg_color'] = reisen_get_scheme_color('bg_color');
		$vars['accent1_color'] = reisen_get_scheme_color('text_link');
		$vars['accent1_hover'] = reisen_get_scheme_color('text_hover');
			
		// Slider height
		$vars['slider_height'] = max(100, reisen_get_custom_option('slider_height'));
			
		// User logged in
		$vars['user_logged_in'] = is_user_logged_in();
			
		// Show table of content for the current page
		$vars['toc_menu'] = reisen_get_custom_option('menu_toc');
		$vars['toc_menu_home'] = reisen_get_custom_option('menu_toc')!='hide' && reisen_get_custom_option('menu_toc_home')=='yes';
		$vars['toc_menu_top'] = reisen_get_custom_option('menu_toc')!='hide' && reisen_get_custom_option('menu_toc_top')=='yes';

		// Fix main menu
		$vars['menu_fixed'] = reisen_get_theme_option('menu_attachment')=='fixed';
			
		// Use responsive version for main menu
		$vars['menu_mobile'] = reisen_get_theme_option('responsive_layouts') == 'yes' ? max(0, (int) reisen_get_theme_option('menu_mobile')) : 0;
		$vars['menu_hover'] = reisen_get_theme_option('menu_hover');
			
		// Theme's buttons hover
		$vars['button_hover'] = reisen_get_theme_option('button_hover');

		// Theme's form fields style
		$vars['input_hover'] = reisen_get_theme_option('input_hover');

		// Right panel demo timer
		$vars['demo_time'] = reisen_get_theme_option('show_theme_customizer')=='yes' ? max(0, (int) reisen_get_theme_option('customizer_demo')) : 0;

		// Video and Audio tag wrapper
		$vars['media_elements_enabled'] = reisen_get_theme_option('use_mediaelement')=='yes';
			
		// Use AJAX search
		$vars['ajax_search_enabled'] = reisen_get_theme_option('use_ajax_search')=='yes';
		$vars['ajax_search_min_length'] = min(3, reisen_get_theme_option('ajax_search_min_length'));
		$vars['ajax_search_delay'] = min(200, max(1000, reisen_get_theme_option('ajax_search_delay')));

		// Use CSS animation
		$vars['css_animation'] = reisen_get_theme_option('css_animation')=='yes';
		$vars['menu_animation_in'] = reisen_get_theme_option('menu_animation_in');
		$vars['menu_animation_out'] = reisen_get_theme_option('menu_animation_out');

		// Popup windows engine
		$vars['popup_engine'] = reisen_get_theme_option('popup_engine');

		// E-mail mask
		$vars['email_mask'] = '^([a-zA-Z0-9_\\-]+\\.)*[a-zA-Z0-9_\\-]+@[a-z0-9_\\-]+(\\.[a-z0-9_\\-]+)*\\.[a-z]{2,6}$';
			
		// Messages max length
		$vars['contacts_maxlength'] = reisen_get_theme_option('message_maxlength_contacts');
		$vars['comments_maxlength'] = reisen_get_theme_option('message_maxlength_comments');

		// Remember visitors settings
		$vars['remember_visitors_settings'] = reisen_get_theme_option('remember_visitors_settings')=='yes';

		// Internal vars - do not change it!
		// Flag for review mechanism
		$vars['admin_mode'] = false;
		// Max scale factor for the portfolio and other isotope elements before relayout
		$vars['isotope_resize_delta'] = 0.3;
		// jQuery object for the message box in the form
		$vars['error_message_box'] = null;
		// Waiting for the viewmore results
		$vars['viewmore_busy'] = false;
		$vars['video_resize_inited'] = false;
		$vars['top_panel_height'] = 0;

		return $vars;
	}
}


// Show content with the html layout (if not empty)
if ( !function_exists('reisen_show_layout') ) {
    function reisen_show_layout($str, $before='', $after='') {
        if ($str != '') {
            printf("%s%s%s", $before, $str, $after);
        }
	}
}

// Add class "widget_number_#' for each widget
if ( !function_exists( 'reisen_add_widget_number' ) ) {
	//Handler of add_filter('dynamic_sidebar_params', 'reisen_add_widget_number', 10, 1);
	function reisen_add_widget_number($prm) {
		if (is_admin()) return $prm;
		static $num=0, $last_sidebar='', $last_sidebar_id='', $last_sidebar_columns=0, $last_sidebar_count=0, $sidebars_widgets=array();
		$cur_sidebar = reisen_storage_get('current_sidebar');
		if (empty($cur_sidebar)) $cur_sidebar = 'undefined';
		if (count($sidebars_widgets) == 0)
			$sidebars_widgets = wp_get_sidebars_widgets();
		if ($last_sidebar != $cur_sidebar) {
			$num = 0;
			$last_sidebar = $cur_sidebar;
			$last_sidebar_id = $prm[0]['id'];
			$last_sidebar_columns = max(1, (int) reisen_get_custom_option('sidebar_'.($cur_sidebar).'_columns'));
			$last_sidebar_count = count($sidebars_widgets[$last_sidebar_id]);
		}
		$num++;
		$prm[0]['before_widget'] = str_replace(' class="', ' class="widget_number_'.esc_attr($num).($last_sidebar_columns > 1 ? ' column-1_'.esc_attr($last_sidebar_columns) : '').' ', $prm[0]['before_widget']);
		return $prm;
	}
}


// Show <title> tag under old WP (version < 4.1)
if ( !function_exists( 'reisen_wp_title_show' ) ) {
	// Handler of add_action('wp_head', 'reisen_wp_title_show');
	function reisen_wp_title_show() {
		?><title><?php wp_title( '|', true, 'right' ); ?></title><?php
	}
}

// Filters wp_title to print a neat <title> tag based on what is being viewed.
if ( !function_exists( 'reisen_wp_title_modify' ) ) {
	// Handler of add_filter( 'wp_title', 'reisen_wp_title_modify', 10, 2 );
	function reisen_wp_title_modify( $title, $sep ) {
		global $page, $paged;
		if ( is_feed() ) return $title;
		// Add the blog name
		$title .= get_bloginfo( 'name' );
		// Add the blog description for the home/front page.
		if ( is_home() || is_front_page() ) {
			$site_description = get_bloginfo( 'description', 'display' );
			if ( $site_description )
				$title .= " $sep $site_description";
		}
		// Add a page number if necessary:
		if ( $paged >= 2 || $page >= 2 )
			$title .= " $sep " . sprintf( esc_html__( 'Page %s', 'reisen' ), max( $paged, $page ) );
		return $title;
	}
}

// Add main menu classes
if ( !function_exists( 'reisen_add_mainmenu_classes' ) ) {
	// Handler of add_filter('wp_nav_menu_objects', 'reisen_add_mainmenu_classes', 10, 2);
	function reisen_add_mainmenu_classes($items, $args) {
		if (is_admin()) return $items;
		if ($args->menu_id == 'mainmenu' && reisen_get_theme_option('menu_colored')=='yes' && is_array($items) && count($items) > 0) {
			foreach($items as $k=>$item) {
				if ($item->menu_item_parent==0) {
					if ($item->type=='taxonomy' && $item->object=='category') {
						$cur_tint = reisen_taxonomy_get_inherited_property('category', $item->object_id, 'bg_tint');
						if (!empty($cur_tint) && !reisen_is_inherit_option($cur_tint))
							$items[$k]->classes[] = 'bg_tint_'.esc_attr($cur_tint);
					}
				}
			}
		}
		return $items;
	}
}


// Save post data from frontend editor
if ( !function_exists( 'reisen_callback_frontend_editor_save' ) ) {
	function reisen_callback_frontend_editor_save() {

		if ( !wp_verify_nonce( reisen_get_value_gp('nonce'), admin_url('admin-ajax.php') ) )
			wp_die();
		$response = array('error'=>'');

		parse_str(reisen_get_value_gp('data'), $output);
		$post_id = $output['frontend_editor_post_id'];

		if ( reisen_get_theme_option("allow_editor")=='yes' && (current_user_can('edit_posts', $post_id) || current_user_can('edit_pages', $post_id)) ) {
			if ($post_id > 0) {
				$title   = stripslashes($output['frontend_editor_post_title']);
				$content = stripslashes($output['frontend_editor_post_content']);
				$excerpt = stripslashes($output['frontend_editor_post_excerpt']);
				$rez = wp_update_post(array(
					'ID'           => $post_id,
					'post_content' => $content,
					'post_excerpt' => $excerpt,
					'post_title'   => $title
				));
				if ($rez == 0) 
					$response['error'] = esc_html__('Post update error!', 'reisen');
			} else {
				$response['error'] = esc_html__('Post update error!', 'reisen');
			}
		} else
			$response['error'] = esc_html__('Post update denied!', 'reisen');
		
		echo json_encode($response);
		wp_die();
	}
}

// Delete post from frontend editor
if ( !function_exists( 'reisen_callback_frontend_editor_delete' ) ) {
	function reisen_callback_frontend_editor_delete() {

		if ( !wp_verify_nonce( reisen_get_value_gp('nonce'), admin_url('admin-ajax.php') ) )
			wp_die();

		$response = array('error'=>'');
		
		$post_id = reisen_get_value_gp('post_id');

		if ( reisen_get_theme_option("allow_editor")=='yes' && (current_user_can('delete_posts', $post_id) || current_user_can('delete_pages', $post_id)) ) {
			if ($post_id > 0) {
				$rez = wp_delete_post($post_id);
				if ($rez === false) 
					$response['error'] = esc_html__('Post delete error!', 'reisen');
			} else {
				$response['error'] = esc_html__('Post delete error!', 'reisen');
			}
		} else
			$response['error'] = esc_html__('Post delete denied!', 'reisen');

		echo json_encode($response);
		wp_die();
	}
}


// Prepare logo text
if ( !function_exists( 'reisen_prepare_logo_text' ) ) {
	function reisen_prepare_logo_text($text) {
		$text = str_replace(array('[', ']'), array('<span class="theme_accent">', '</span>'), $text);
		$text = str_replace(array('{', '}'), array('<strong>', '</strong>'), $text);
		return $text;
	}
}

?>