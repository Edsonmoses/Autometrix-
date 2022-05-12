<?php
/**
 * Theme sprecific functions and definitions
 */

/* Theme setup section
------------------------------------------------------------------- */

// Set the content width based on the theme's design and stylesheet.
if ( ! isset( $content_width ) ) $content_width = 1170; /* pixels */


// Add theme specific actions and filters
// Attention! Function were add theme specific actions and filters handlers must have priority 1
if ( !function_exists( 'reisen_theme_setup' ) ) {
	add_action( 'reisen_action_before_init_theme', 'reisen_theme_setup', 1 );
	function reisen_theme_setup() {

        // Add default posts and comments RSS feed links to head
        add_theme_support( 'automatic-feed-links' );

        // Enable support for Post Thumbnails
        add_theme_support( 'post-thumbnails' );

        // Custom header setup
        add_theme_support( 'custom-header', array('header-text'=>false));

        // Custom backgrounds setup
        add_theme_support( 'custom-background');

        // Supported posts formats
        add_theme_support( 'post-formats', array('gallery', 'video', 'audio', 'link', 'quote', 'image', 'status', 'aside', 'chat') );

        // Autogenerate title tag
        add_theme_support('title-tag');

        // Add user menu
        add_theme_support('nav-menus');

        // WooCommerce Support
        add_theme_support( 'woocommerce' );

        // Next setting from the WooCommerce 3.0+ enable built-in image zoom on the single product page
        add_theme_support( 'wc-product-gallery-zoom' );

        // Next setting from the WooCommerce 3.0+ enable built-in image slider on the single product page
        add_theme_support( 'wc-product-gallery-slider' );

        // Next setting from the WooCommerce 3.0+ enable built-in image lightbox on the single product page
        add_theme_support( 'wc-product-gallery-lightbox' );

		// Register theme menus
		add_filter( 'reisen_filter_add_theme_menus',		'reisen_add_theme_menus' );

		// Register theme sidebars
		add_filter( 'reisen_filter_add_theme_sidebars',	'reisen_add_theme_sidebars' );

		// Set options for importer
		add_filter( 'reisen_filter_importer_options',		'reisen_set_importer_options' );

		// Add theme required plugins
		add_filter( 'reisen_filter_required_plugins',		'reisen_add_required_plugins' );
		
		// Add preloader styles
		add_filter('reisen_filter_add_styles_inline',		'reisen_head_add_page_preloader_styles');

		// Init theme after WP is created
		add_action( 'wp',									'reisen_core_init_theme' );

		// Add theme specified classes into the body
		add_filter( 'body_class', 							'reisen_body_classes' );

		// Add data to the head and to the beginning of the body
		add_action('wp_head',								'reisen_head_add_page_meta', 1);
		add_action('before',								'reisen_body_add_toc');
		add_action('before',								'reisen_body_add_page_preloader');

		// Add data to the footer (priority 1, because priority 2 used for localize scripts)
		add_action('wp_footer',								'reisen_footer_add_views_counter', 1);
		add_action('wp_footer',								'reisen_footer_add_theme_customizer', 1);

        // Gutenberg support
        add_theme_support( 'align-wide' );

		// Set list of the theme required plugins
		reisen_storage_set('required_plugins', array(
			'essgrids',
			'revslider',
			'trx_utils',
            'mailchimp',
			'visual_composer',
			'woocommerce',
            'woof',
            'trx_updater',
            'elegro-payment',
            'contact-form-7'
			)
		);

		// Set list of the theme required custom fonts from folder /css/font-faces
		// Attention! Font's folder must have name equal to the font's name
		reisen_storage_set('required_custom_fonts', array(
			'Amadeus'
			)
		);

        reisen_storage_set('demo_data_url',  esc_url(reisen_get_protocol() . '://reisen.themerex.net/demo/'));
		
	}
}


// Add/Remove theme nav menus
if ( !function_exists( 'reisen_add_theme_menus' ) ) {
	//Handler of add_filter( 'reisen_filter_add_theme_menus', 'reisen_add_theme_menus' );
	function reisen_add_theme_menus($menus) {
		return $menus;
	}
}


// Add theme specific widgetized areas
if ( !function_exists( 'reisen_add_theme_sidebars' ) ) {
	//Handler of add_filter( 'reisen_filter_add_theme_sidebars',	'reisen_add_theme_sidebars' );
	function reisen_add_theme_sidebars($sidebars=array()) {
		if (is_array($sidebars)) {
			$theme_sidebars = array(
				'sidebar_main'		=> esc_html__( 'Main Sidebar', 'reisen' ),
				'sidebar_footer'	=> esc_html__( 'Footer Sidebar', 'reisen' )
			);
			if (function_exists('reisen_exists_woocommerce') && reisen_exists_woocommerce()) {
				$theme_sidebars['sidebar_cart']  = esc_html__( 'WooCommerce Cart Sidebar', 'reisen' );
			}
			$sidebars = array_merge($theme_sidebars, $sidebars);
		}
		return $sidebars;
	}
}


// Add theme required plugins
if ( !function_exists( 'reisen_add_required_plugins' ) ) {
	//Handler of add_filter( 'reisen_filter_required_plugins',		'reisen_add_required_plugins' );
	function reisen_add_required_plugins($plugins) {
		$plugins[] = array(
			'name' 		=> esc_html__('Reisen Utilities', 'reisen'),
			'version'	=> '3.2',					// Minimal required version
			'slug' 		=> 'trx_utils',
			'source'	=> reisen_get_file_dir('plugins/install/trx_utils.zip'),
			'required' 	=> true
		);
        $plugins[] = array(
            'name' 		=> esc_html__('WooCommerce Products Filter', 'reisen'),
            'slug' 		=> 'woocommerce-products-filter',
            'required' 	=> false
        );
        $plugins[] = array(
            'name' 		=> esc_html__('Custom Post Type UI', 'reisen'),
            'slug' 		=> 'custom-post-type-ui',
            'required' 	=> false
        );
		return $plugins;
	}
}


//------------------------------------------------------------------------
// One-click import support
//------------------------------------------------------------------------

// Set theme specific importer options
if ( ! function_exists( 'reisen_importer_set_options' ) ) {
    add_filter( 'trx_utils_filter_importer_options', 'reisen_importer_set_options', 9 );
    function reisen_importer_set_options( $options=array() ) {
        if ( is_array( $options ) ) {
            // Save or not installer's messages to the log-file
            $options['debug'] = false;
            // Prepare demo data
            if ( is_dir( REISEN_THEME_PATH . 'demo/' ) ) {
                $options['demo_url'] = REISEN_THEME_PATH . 'demo/';
            } else {
                $options['demo_url'] = esc_url( reisen_get_protocol().'://demofiles.themerex.net/reisen/' ); // Demo-site domain
            }

            // Required plugins
            $options['required_plugins'] =  array(
                'essential-grid',
                'revslider',
                'mailchimp-for-wp',
                'js_composer',
                'woocommerce',
                'woof',
                'cptui',
                'contact-form-7',
                'elegro-payment'
            );

            $options['theme_slug'] = 'reisen';

            // Set number of thumbnails to regenerate when its imported (if demo data was zipped without cropped images)
            // Set 0 to prevent regenerate thumbnails (if demo data archive is already contain cropped images)
            $options['regenerate_thumbnails'] = 3;
            // Default demo
            $options['files']['default']['title'] = esc_html__( 'Reisen Demo', 'reisen' );
            $options['files']['default']['domain_dev'] = esc_url('http://reisen.themerex.net'); // Developers domain
            $options['files']['default']['domain_demo']= esc_url('http://reisen.themerex.net'); // Demo-site domain

        }
        return $options;
    }
}


// Add data to the head and to the beginning of the body
//------------------------------------------------------------------------

// Add theme specified classes to the body tag
if ( !function_exists('reisen_body_classes') ) {
	//Handler of add_filter( 'body_class', 'reisen_body_classes' );
	function reisen_body_classes( $classes ) {

		$classes[] = 'reisen_body';
		$classes[] = 'body_style_' . trim(reisen_get_custom_option('body_style'));
		$classes[] = 'body_' . (reisen_get_custom_option('body_filled')=='yes' ? 'filled' : 'transparent');
		$classes[] = 'article_style_' . trim(reisen_get_custom_option('article_style'));
		
		$blog_style = reisen_get_custom_option(is_singular() && !reisen_storage_get('blog_streampage') ? 'single_style' : 'blog_style');
		$classes[] = 'layout_' . trim($blog_style);
		$classes[] = 'template_' . trim(reisen_get_template_name($blog_style));
		
		$body_scheme = reisen_get_custom_option('body_scheme');
		if (empty($body_scheme)  || reisen_is_inherit_option($body_scheme)) $body_scheme = 'original';
		$classes[] = 'scheme_' . $body_scheme;

		$top_panel_position = reisen_get_custom_option('top_panel_position');
		if (!reisen_param_is_off($top_panel_position)) {
			$classes[] = 'top_panel_show';
			$classes[] = 'top_panel_' . trim($top_panel_position);
		} else 
			$classes[] = 'top_panel_hide';
		$classes[] = reisen_get_sidebar_class();

		if (reisen_get_custom_option('show_video_bg')=='yes' && (reisen_get_custom_option('video_bg_youtube_code')!='' || reisen_get_custom_option('video_bg_url')!=''))
			$classes[] = 'video_bg_show';

		if (!reisen_param_is_off(reisen_get_theme_option('page_preloader')))
			$classes[] = 'preloader';

		return $classes;
	}
}


// Add page meta to the head
if (!function_exists('reisen_head_add_page_meta')) {
	//Handler of add_action('wp_head', 'reisen_head_add_page_meta', 1);
	function reisen_head_add_page_meta() {
		?>
		<meta charset="<?php bloginfo( 'charset' ); ?>" />
		<meta name="viewport" content="width=device-width, initial-scale=1<?php if (reisen_get_theme_option('responsive_layouts')=='yes') echo ', maximum-scale=1'; ?>">
		<meta name="format-detection" content="telephone=no">
	
		<link rel="profile" href="//gmpg.org/xfn/11" />
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
		<?php
	}
}

// Add page preloader styles to the head
if (!function_exists('reisen_head_add_page_preloader_styles')) {
	//Handler of add_filter('reisen_filter_add_styles_inline', 'reisen_head_add_page_preloader_styles');
	function reisen_head_add_page_preloader_styles($css) {
		if (($preloader=reisen_get_theme_option('page_preloader'))!='none') {
			$image = reisen_get_theme_option('page_preloader_image');
			$bg_clr = reisen_get_scheme_color('bg_color');
			$link_clr = reisen_get_scheme_color('text_link');
			$css .= '
				#page_preloader {
					background-color: '. esc_attr($bg_clr) . ';'
					. ($preloader=='custom' && $image
						? 'background-image:url('.esc_url($image).');'
						: ''
						)
				    . '
				}
				.preloader_wrap > div {
					background-color: '.esc_attr($link_clr).';
				}';
		}
		return $css;
	}
}

// Add TOC anchors to the beginning of the body
if (!function_exists('reisen_body_add_toc')) {
	//Handler of add_action('before', 'reisen_body_add_toc');
	function reisen_body_add_toc() {
		// Add TOC items 'Home' and "To top"
		if (reisen_get_custom_option('menu_toc_home')=='yes' && function_exists('reisen_sc_anchor'))
            reisen_show_layout(reisen_sc_anchor(array(
				'id' => "toc_home",
				'title' => esc_html__('Home', 'reisen'),
				'description' => esc_html__('{{Return to Home}} - ||navigate to home page of the site', 'reisen'),
				'icon' => "icon-home",
				'separator' => "yes",
				'url' => esc_url(home_url('/'))
				)
			)); 
		if (reisen_get_custom_option('menu_toc_top')=='yes' && function_exists('reisen_sc_anchor'))
            reisen_show_layout(reisen_sc_anchor(array(
				'id' => "toc_top",
				'title' => esc_html__('To Top', 'reisen'),
				'description' => esc_html__('{{Back to top}} - ||scroll to top of the page', 'reisen'),
				'icon' => "icon-double-up",
				'separator' => "yes")
				)); 
	}
}

// Add page preloader to the beginning of the body
if (!function_exists('reisen_body_add_page_preloader')) {
	//Handler of add_action('before', 'reisen_body_add_page_preloader');
	function reisen_body_add_page_preloader() {
		if ( ($preloader=reisen_get_theme_option('page_preloader')) != 'none' && ( $preloader != 'custom' || ($image=reisen_get_theme_option('page_preloader_image')) != '')) {
			?><div id="page_preloader"><?php
				if ($preloader == 'circle') {
					?><div class="preloader_wrap preloader_<?php echo esc_attr($preloader); ?>"><div class="preloader_circ1"></div><div class="preloader_circ2"></div><div class="preloader_circ3"></div><div class="preloader_circ4"></div></div><?php
				} else if ($preloader == 'square') {
					?><div class="preloader_wrap preloader_<?php echo esc_attr($preloader); ?>"><div class="preloader_square1"></div><div class="preloader_square2"></div></div><?php
				}
			?></div><?php
		}
	}
}

// Return text for the Privacy Policy checkbox
if ( ! function_exists('reisen_get_privacy_text' ) ) {
    function reisen_get_privacy_text() {
        $page = get_option( 'wp_page_for_privacy_policy' );
        $privacy_text = reisen_get_theme_option( 'privacy_text' );
        return apply_filters( 'reisen_filter_privacy_text', wp_kses_post(
                $privacy_text
                . ( ! empty( $page ) && ! empty( $privacy_text )
                    // Translators: Add url to the Privacy Policy page
                    ? ' ' . sprintf( __( 'For further details on handling user data, see our %s', 'reisen' ),
                        '<a href="' . esc_url( get_permalink( $page ) ) . '" target="_blank">'
                        . __( 'Privacy Policy', 'reisen' )
                        . '</a>' )
                    : ''
                )
            )
        );
    }
}

// Return text for the "I agree ..." checkbox
if ( ! function_exists( 'reisen_trx_addons_privacy_text' ) ) {
    add_filter( 'trx_addons_filter_privacy_text', 'reisen_trx_addons_privacy_text' );
    function reisen_trx_addons_privacy_text( $text='' ) {
        return reisen_get_privacy_text();
    }
}


// Add data to the footer
//------------------------------------------------------------------------

// Add post/page views counter
if (!function_exists('reisen_footer_add_views_counter')) {
	//Handler of add_action('wp_footer', 'reisen_footer_add_views_counter');
	function reisen_footer_add_views_counter() {
		// Post/Page views counter
		get_template_part(reisen_get_file_slug('templates/_parts/views-counter.php'));
	}
}

// Add theme customizer
if (!function_exists('reisen_footer_add_theme_customizer')) {
	//Handler of add_action('wp_footer', 'reisen_footer_add_theme_customizer');
	function reisen_footer_add_theme_customizer() {
		// Front customizer
		if (reisen_get_custom_option('show_theme_customizer')=='yes') {
			require_once REISEN_FW_PATH . 'core/core.customizer/front.customizer.php';
		}
	}
}

/**
 * Fire the wp_body_open action.
 *
 * Added for backwards compatibility to support pre 5.2.0 WordPress versions.
 */
if ( ! function_exists( 'wp_body_open' ) ) {
    function wp_body_open() {
        /**
         * Triggered after the opening <body> tag.
         */
        do_action('wp_body_open');
    }
}

// Add theme required plugins
if ( !function_exists( 'reisen_add_trx_utils' ) ) {
    add_filter( 'trx_utils_active', 'reisen_add_trx_utils' );
    function reisen_add_trx_utils($enable=true) {
        return true;
    }
}

function reisen_move_comment_field_to_bottom( $fields ) {
    $comment_field = $fields['comment'];
    unset( $fields['comment'] );
    $fields['comment'] = $comment_field;
    return $fields;
}

add_filter( 'comment_form_fields', 'reisen_move_comment_field_to_bottom' );


// Include framework core files
//-------------------------------------------------------------------
require_once trailingslashit( get_template_directory() ) . 'fw/loader.php';
?>