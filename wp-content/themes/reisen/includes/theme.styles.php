<?php
/**
 * Theme custom styles
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


/* Theme setup section
-------------------------------------------------------------------- */

if (!function_exists('reisen_action_theme_styles_theme_setup')) {
	add_action( 'reisen_action_before_init_theme', 'reisen_action_theme_styles_theme_setup', 1 );
	function reisen_action_theme_styles_theme_setup() {
	
		// Add theme fonts in the used fonts list
		add_filter('reisen_filter_used_fonts',			'reisen_filter_theme_styles_used_fonts');
		// Add theme fonts (from Google fonts) in the main fonts list (if not present).
		add_filter('reisen_filter_list_fonts',			'reisen_filter_theme_styles_list_fonts');

		// Add theme stylesheets
		add_action('reisen_action_add_styles',			'reisen_action_theme_styles_add_styles');

        add_action( 'reisen_action_add_styles', 'reisen_style_styles_method' );
		// Add theme inline styles
		add_filter('reisen_filter_add_styles_inline',		'reisen_filter_theme_styles_add_styles_inline');

		// Add theme scripts
		add_action('reisen_action_add_scripts',			'reisen_action_theme_styles_add_scripts');
		// Add theme scripts inline
		add_filter('reisen_filter_localize_script',		'reisen_filter_theme_styles_localize_script');

		// Add theme less files into list for compilation
		add_filter('reisen_filter_compile_less',			'reisen_filter_theme_styles_compile_less');

		// Add color schemes
		reisen_add_color_scheme('original', array(

			'title'					=> esc_html__('Original', 'reisen'),
			
			// Whole block border and background
			'bd_color'				=> '#e7e7e7',   //
			'bg_color'				=> '#ffffff',
			
			// Headers, text and links colors
			'text'					=> '#888888',   //
			'text_light'			=> '#acb4b6',
			'text_dark'				=> '#393939',   //
			'text_link'				=> '#bf2d0d',   //
			'text_hover'			=> '#af290c',   //

			// Inverse colors
			'inverse_text'			=> '#ffffff',
			'inverse_light'			=> '#ffffff',
			'inverse_dark'			=> '#ffffff',
			'inverse_link'			=> '#ffffff',
			'inverse_hover'			=> '#ffffff',
		
			// Input fields
			'input_text'			=> '#8a8a8a',
			'input_light'			=> '#acb4b6',
			'input_dark'			=> '#232a34',
			'input_bd_color'		=> '#dddddd',
			'input_bd_hover'		=> '#d7d7d3',   //
			'input_bg_color'		=> '#f6f7f7',   //
			'input_bg_hover'		=> '#f0f0f0',
		
			// Alternative blocks (submenu items, etc.)
			'alter_text'			=> '#8a8a8a',
			'alter_light'			=> '#acb4b6',
			'alter_dark'			=> '#313131',       //
			'alter_link'			=> '#cecfd0',       //
			'alter_hover'			=> '#189799',
			'alter_bd_color'		=> '#e3e3e3',       //
			'alter_bd_hover'		=> '#8b8b8b',       //
			'alter_bg_color'		=> '#f2f3f3',       //
			'alter_bg_hover'		=> '#f0f0f0',       //
			)
		);

		// Add Custom fonts
		reisen_add_custom_font('h1', array(
			'title'			=> esc_html__('Heading 1', 'reisen'),
			'description'	=> '',
			'font-family'	=> 'Montserrat',
			'font-size' 	=> '3.667em',
			'font-weight'	=> '700',
			'font-style'	=> '',
			'line-height'	=> '1.3em',
			'margin-top'	=> '0.5em',
			'margin-bottom'	=> '0.39em'
			)
		);
		reisen_add_custom_font('h2', array(
			'title'			=> esc_html__('Heading 2', 'reisen'),
			'description'	=> '',
			'font-family'	=> 'Montserrat',
			'font-size' 	=> '2.933em',
			'font-weight'	=> '700',
			'font-style'	=> '',
			'line-height'	=> '1.3em',
			'margin-top'	=> '2.06em',
			'margin-bottom'	=> '0.53em'
			)
		);
		reisen_add_custom_font('h3', array(
			'title'			=> esc_html__('Heading 3', 'reisen'),
			'description'	=> '',
			'font-family'	=> 'Montserrat',
			'font-size' 	=> '2.4em',
			'font-weight'	=> '700',
			'font-style'	=> '',
			'line-height'	=> '1.3em',
			'margin-top'	=> '2.5em',
			'margin-bottom'	=> '0.75em'
			)
		);
		reisen_add_custom_font('h4', array(
			'title'			=> esc_html__('Heading 4', 'reisen'),
			'description'	=> '',
			'font-family'	=> 'Montserrat',
			'font-size' 	=> '2em',
			'font-weight'	=> '700',
			'font-style'	=> '',
			'line-height'	=> '1.3em',
			'margin-top'	=> '2.9em',
			'margin-bottom'	=> '0.35em'
			)
		);
		reisen_add_custom_font('h5', array(
			'title'			=> esc_html__('Heading 5', 'reisen'),
			'description'	=> '',
			'font-family'	=> 'Montserrat',
			'font-size' 	=> '1.6em',
			'font-weight'	=> '700',
			'font-style'	=> '',
			'line-height'	=> '1.3em',
			'margin-top'	=> '3.8em',
			'margin-bottom'	=> '0.5em'
			)
		);
		reisen_add_custom_font('h6', array(
			'title'			=> esc_html__('Heading 6', 'reisen'),
			'description'	=> '',
			'font-family'	=> 'Hind',
			'font-size' 	=> '1.333em',
			'font-weight'	=> '400',
			'font-style'	=> '',
			'line-height'	=> '1.3em',
			'margin-top'	=> '4.75em',
			'margin-bottom'	=> '0.3em'
			)
		);
		reisen_add_custom_font('p', array(
			'title'			=> esc_html__('Text', 'reisen'),
			'description'	=> '',
			'font-family'	=> 'Hind',
			'font-size' 	=> '15px',
			'font-weight'	=> '400',
			'font-style'	=> '',
			'line-height'	=> '1.55em',
			'margin-top'	=> '',
			'margin-bottom'	=> '1em'
			)
		);
		reisen_add_custom_font('link', array(
			'title'			=> esc_html__('Links', 'reisen'),
			'description'	=> '',
			'font-family'	=> '',
			'font-size' 	=> '',
			'font-weight'	=> '',
			'font-style'	=> ''
			)
		);
		reisen_add_custom_font('info', array(
			'title'			=> esc_html__('Post info', 'reisen'),
			'description'	=> '',
			'font-family'	=> '',
			'font-size' 	=> '0.933em',
			'font-weight'	=> '',
			'font-style'	=> '',
			'line-height'	=> '1.2857em',
			'margin-top'	=> '',
			'margin-bottom'	=> '1.55em'
			)
		);
		reisen_add_custom_font('menu', array(
			'title'			=> esc_html__('Main menu items', 'reisen'),
			'description'	=> '',
			'font-family'	=> 'Montserrat',
			'font-size' 	=> '0.933em',
			'font-weight'	=> '400',
			'font-style'	=> '',
			'line-height'	=> '1.2857em',
			'margin-top'	=> '1.8em',
			'margin-bottom'	=> '1.8em'
			)
		);
		reisen_add_custom_font('submenu', array(
			'title'			=> esc_html__('Dropdown menu items', 'reisen'),
			'description'	=> '',
			'font-family'	=> 'Montserrat',
			'font-size' 	=> '0.933em',
			'font-weight'	=> '400',
			'font-style'	=> '',
			'line-height'	=> '1.2857em',
			'margin-top'	=> '',
			'margin-bottom'	=> ''
			)
		);
		reisen_add_custom_font('logo', array(
			'title'			=> esc_html__('Logo', 'reisen'),
			'description'	=> '',
			'font-family'	=> 'Montserrat',
			'font-size' 	=> '3em',
			'font-weight'	=> '700',
			'font-style'	=> '',
			'line-height'	=> '0.87em',
			'margin-top'	=> '2.45em',
			'margin-bottom'	=> '1.2em'
			)
		);
		reisen_add_custom_font('button', array(
			'title'			=> esc_html__('Buttons', 'reisen'),
			'description'	=> '',
			'font-family'	=> 'Montserrat',
			'font-size' 	=> '0.8em',
			'font-weight'	=> '400',
			'font-style'	=> '',
			'line-height'	=> '1.2857em'
			)
		);
		reisen_add_custom_font('input', array(
			'title'			=> esc_html__('Input fields', 'reisen'),
			'description'	=> '',
			'font-family'	=> 'Montserrat',
			'font-size' 	=> '0.933em',
			'font-weight'	=> '400',
			'font-style'	=> '',
			'line-height'	=> '1.2857em'
			)
		);

	}
}





//------------------------------------------------------------------------------
// Theme fonts
//------------------------------------------------------------------------------

// Add theme fonts in the used fonts list
if (!function_exists('reisen_filter_theme_styles_used_fonts')) {
	function reisen_filter_theme_styles_used_fonts($theme_fonts) {
		$theme_fonts['Hind'] = 1;
        $theme_fonts['Montserrat'] = 1;
		return $theme_fonts;
	}
}

// Add theme fonts (from Google fonts) in the main fonts list (if not present).
// To use custom font-face you not need add it into list in this function
// How to install custom @font-face fonts into the theme?
// All @font-face fonts are located in "theme_name/css/font-face/" folder in the separate subfolders for the each font. Subfolder name is a font-family name!
// Place full set of the font files (for each font style and weight) and css-file named stylesheet.css in the each subfolder.
// Create your @font-face kit by using Fontsquirrel @font-face Generator (http://www.fontsquirrel.com/fontface/generator)
// and then extract the font kit (with folder in the kit) into the "theme_name/css/font-face" folder to install
if (!function_exists('reisen_filter_theme_styles_list_fonts')) {
	function reisen_filter_theme_styles_list_fonts($list) {
		if (!isset($list['Hind']))	$list['Hind'] = array('family'=>'sans-serif', 'link'=>'Hind:400,700');
        if (!isset($list['Montserrat']))	$list['Montserrat'] = array('family'=>'sans-serif', 'link'=>'Montserrat:400,700');

		return $list;
	}
}



//------------------------------------------------------------------------------
// Theme stylesheets
//------------------------------------------------------------------------------

// Add theme.less into list files for compilation
if (!function_exists('reisen_filter_theme_styles_compile_less')) {
	function reisen_filter_theme_styles_compile_less($files) {
		if (file_exists(reisen_get_file_dir('css/theme.less'))) {
		 	$files[] = reisen_get_file_dir('css/theme.less');
		}
		return $files;	
	}
}

// Add theme stylesheets
if (!function_exists('reisen_action_theme_styles_add_styles')) {
	function reisen_action_theme_styles_add_styles() {
		// Add stylesheet files only if LESS supported
		if ( reisen_get_theme_setting('less_compiler') != 'no' ) {
			wp_enqueue_style( 'reisen-theme-style', reisen_get_file_url('css/theme.css'), array(), null );
			wp_add_inline_style( 'reisen-theme-style', reisen_get_inline_css() );
		}
	}
}

// Add theme inline styles
if (!function_exists('reisen_filter_theme_styles_add_styles_inline')) {
	function reisen_filter_theme_styles_add_styles_inline($custom_style) {
		// Submenu width
		$menu_width = reisen_get_theme_option('menu_width');
		if (!empty($menu_width)) {
			$custom_style .= "
				/* Submenu width */
				.menu_side_nav > li ul,
				.menu_main_nav > li ul {
					width: ".intval($menu_width)."px;
				}
				.menu_side_nav > li > ul ul,
				.menu_main_nav > li > ul ul {
					left:".intval($menu_width+4)."px;
				}
				.menu_side_nav > li > ul ul.submenu_left,
				.menu_main_nav > li > ul ul.submenu_left {
					left:-".intval($menu_width+1)."px;
				}
			";
		}
	
		// Logo height
		$logo_height = reisen_get_custom_option('logo_height');
		if (!empty($logo_height)) {
			$custom_style .= "
				/* Logo header height */
				.sidebar_outer_logo .logo_main,
				.top_panel_wrap .logo_main,
				.top_panel_wrap .logo_fixed {
					height:".intval($logo_height)."px;
				}
			";
		}
	
		// Logo top offset
		$logo_offset = reisen_get_custom_option('logo_offset');
		if (!empty($logo_offset)) {
			$custom_style .= "
				/* Logo header top offset */
				.top_panel_wrap .logo {
					margin-top:".intval($logo_offset)."px;
				}
			";
		}

		// Logo footer height
		$logo_height = reisen_get_theme_option('logo_footer_height');
		if (!empty($logo_height)) {
			$custom_style .= "
				/* Logo footer height */
				.contacts_wrap .logo img {
					height:".intval($logo_height)."px;
				}
			";
		}

		// Custom css from theme options
		$custom_style .= reisen_get_custom_option('custom_css');

		return $custom_style;	
	}
}


//------------------------------------------------------------------------------
// Theme scripts
//------------------------------------------------------------------------------

// Add theme scripts
if (!function_exists('reisen_action_theme_styles_add_scripts')) {
	function reisen_action_theme_styles_add_scripts() {
		if (reisen_get_theme_option('show_theme_customizer') == 'yes' && file_exists(reisen_get_file_dir('js/theme.customizer.js')))
			wp_enqueue_script( 'reisen-theme-styles-customizer-script', reisen_get_file_url('js/theme.customizer.js'), array(), null );
	}
}

// Add theme scripts inline
if (!function_exists('reisen_filter_theme_styles_localize_script')) {
	function reisen_filter_theme_styles_localize_script($vars) {
		if (empty($vars['theme_font']))
			$vars['theme_font'] = reisen_get_custom_font_settings('p', 'font-family');
		$vars['theme_color'] = reisen_get_scheme_color('text_dark');
		$vars['theme_bg_color'] = reisen_get_scheme_color('bg_color');
		return $vars;
	}
}

if (!function_exists('reisen_style_styles_method')) {
    function reisen_style_styles_method() {
        $body_style = reisen_get_custom_option('body_style');
        $style = '';
        if (reisen_get_custom_option('bg_custom') == 'yes' && ($body_style == 'boxed' || reisen_get_custom_option('bg_image_load') == 'always')) {
            if (($img = reisen_get_custom_option('bg_image_custom')) != '')
                $style = 'background: url(' . esc_url($img) . ') ' . str_replace('_', ' ', reisen_get_custom_option('bg_image_custom_position')) . ' no-repeat fixed;';
            else if (($img = reisen_get_custom_option('bg_pattern_custom')) != '')
                $style = 'background: url(' . esc_url($img) . ') 0 0 repeat fixed;';
            if (($img = reisen_get_custom_option('bg_color')) != '')
                $style .= 'background-color: ' . ($img) . ';';
        }

        $style = '.scheme_original .body_style_boxed .body_wrap { ' . $style . ') }';

        $reisen_show_title_bg = reisen_get_custom_option('show_title_bg')=='yes';
        $reisen_title_bg = reisen_get_custom_option('title_bg');

        if ( ($reisen_show_title_bg == 'yes') && ($reisen_title_bg != ''))
            $style .= '.top_panel_title_inner { background-image: url(' . esc_url($reisen_title_bg) . '); }';

        wp_enqueue_style('reisen-style', get_template_directory_uri() . '/css/reisen-style.css');

        wp_add_inline_style('reisen-style', $style );
    }
}
?>