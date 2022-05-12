<?php
/* Woocommerce support functions
------------------------------------------------------------------------------- */

// Theme init
if (!function_exists('reisen_woocommerce_theme_setup')) {
	add_action( 'reisen_action_before_init_theme', 'reisen_woocommerce_theme_setup', 1 );
	function reisen_woocommerce_theme_setup() {

		if (reisen_exists_woocommerce()) {
			add_action('reisen_action_add_styles', 				'reisen_woocommerce_frontend_scripts' );

			// Detect current page type, taxonomy and title (for custom post_types use priority < 10 to fire it handles early, than for standard post types)
			add_filter('reisen_filter_get_blog_type',				'reisen_woocommerce_get_blog_type', 9, 2);
			add_filter('reisen_filter_get_blog_title',			'reisen_woocommerce_get_blog_title', 9, 2);
			add_filter('reisen_filter_get_current_taxonomy',		'reisen_woocommerce_get_current_taxonomy', 9, 2);
			add_filter('reisen_filter_is_taxonomy',				'reisen_woocommerce_is_taxonomy', 9, 2);
			add_filter('reisen_filter_get_stream_page_title',		'reisen_woocommerce_get_stream_page_title', 9, 2);
			add_filter('reisen_filter_get_stream_page_link',		'reisen_woocommerce_get_stream_page_link', 9, 2);
			add_filter('reisen_filter_get_stream_page_id',		'reisen_woocommerce_get_stream_page_id', 9, 2);
			add_filter('reisen_filter_detect_inheritance_key',	'reisen_woocommerce_detect_inheritance_key', 9, 1);
			add_filter('reisen_filter_detect_template_page_id',	'reisen_woocommerce_detect_template_page_id', 9, 2);
			add_filter('reisen_filter_orderby_need',				'reisen_woocommerce_orderby_need', 9, 2);

			add_filter('reisen_filter_show_post_navi', 			'reisen_woocommerce_show_post_navi');
			add_filter('reisen_filter_list_post_types', 			'reisen_woocommerce_list_post_types');
			
			// Detect if WooCommerce support 'Product Grid' feature
			$product_grid = reisen_exists_woocommerce() && function_exists( 'wc_get_theme_support' ) ? wc_get_theme_support( 'product_grid' ) : false;
			add_theme_support( 'wc-product-grid-enable', isset( $product_grid['min_columns'] ) && isset( $product_grid['max_columns'] ) );

            remove_action( 'woocommerce_shop_loop_subcategory_title', 'woocommerce_template_loop_category_title', 10 );
            add_action( 'woocommerce_shop_loop_subcategory_title',  'reisen_woocommerce_shop_loop_subcategory_title', 9, 1);

            add_filter( 'woocommerce_product_add_to_cart_text' , 'reisen_custom_woocommerce_product_add_to_cart_text' );
		}

		if (is_admin()) {
			add_filter( 'reisen_filter_required_plugins',					'reisen_woocommerce_required_plugins' );
		}
	}
}


if ( !function_exists( 'reisen_custom_woocommerce_product_add_to_cart_text' ) ) {

    function reisen_custom_woocommerce_product_add_to_cart_text() {
        global $product;

        $product_type = $product->get_type();

        switch ( $product_type ) {
            case 'external':
                return esc_html__( 'Buy product', 'reisen' );
                break;
            case 'grouped':
                return esc_html__( 'View products', 'reisen' );
                break;
            case 'simple':
                return esc_html__( 'Add to cart', 'reisen' );
                break;
            case 'variable':
                return esc_html__( 'Details', 'reisen' );
                break;
            default:
                return esc_html__( 'Read more', 'reisen' );
        }

    }
}



if ( !function_exists( 'reisen_woocommerce_settings_theme_setup2' ) ) {
	add_action( 'reisen_action_before_init_theme', 'reisen_woocommerce_settings_theme_setup2', 3 );
	function reisen_woocommerce_settings_theme_setup2() {
		if (reisen_exists_woocommerce()) {
			// Add WooCommerce pages in the Theme inheritance system
			reisen_add_theme_inheritance( array( 'woocommerce' => array(
				'stream_template' => 'blog-woocommerce',		// This params must be empty
				'single_template' => 'single-woocommerce',		// They are specified to enable separate settings for blog and single wooc
				'taxonomy' => array('product_cat'),
				'taxonomy_tags' => array('product_tag'),
				'post_type' => array('product'),
				'override' => 'custom'
				) )
			);

			// Add WooCommerce specific options in the Theme Options

			reisen_storage_set_array_before('options', 'partition_service', array(
				
				"partition_woocommerce" => array(
					"title" => esc_html__('WooCommerce', 'reisen'),
					"icon" => "iconadmin-basket",
					"type" => "partition"),

				"info_wooc_1" => array(
					"title" => esc_html__('WooCommerce products list parameters', 'reisen'),
					"desc" => esc_html__("Select WooCommerce products list's style and crop parameters", 'reisen'),
					"type" => "info"),
		
				"shop_mode" => array(
					"title" => esc_html__('Shop list style',  'reisen'),
					"desc" => esc_html__("WooCommerce products list's style: thumbs or list with description", 'reisen'),
					"std" => "thumbs",
					"divider" => false,
					"options" => array(
						'thumbs' => esc_html__('Thumbs', 'reisen'),
						'list' => esc_html__('List', 'reisen')
					),
					"type" => "checklist"),
		
				"show_mode_buttons" => array(
					"title" => esc_html__('Show style buttons',  'reisen'),
					"desc" => esc_html__("Show buttons to allow visitors change list style", 'reisen'),
					"std" => "yes",
					"options" => reisen_get_options_param('list_yes_no'),
					"type" => "switch"),

				"shop_loop_columns" => array(
					"title" => esc_html__('Shop columns',  'reisen'),
					"desc" => esc_html__("How many columns used to show products on shop page", 'reisen'),
					"std" => "3",
					"step" => 1,
					"min" => 1,
					"max" => 6,
					"type" => "spinner"),

				"show_currency" => array(
					"title" => esc_html__('Show currency selector', 'reisen'),
					"desc" => esc_html__('Show currency selector in the user menu', 'reisen'),
					"std" => "yes",
					"options" => reisen_get_options_param('list_yes_no'),
					"type" => "switch"),
		
				"show_cart" => array(
					"title" => esc_html__('Show cart button', 'reisen'),
					"desc" => esc_html__('Show cart button in the user menu', 'reisen'),
					"std" => "hide",
					"options" => array(
						'hide'   => esc_html__('Hide', 'reisen'),
						'always' => esc_html__('Always', 'reisen'),
						'shop'   => esc_html__('Only on shop pages', 'reisen')
					),
					"type" => "checklist"),

                "shop_pages_in_group" => array(
				  "title" => esc_html__('Shop pagination group',  'reisen'),
				  "desc" => esc_html__("The number of pages in the pagination group", 'reisen'),
				  "std" => "10",
				  "step" => 1,
				  "min" => 1,
				  "max" => 1000,
				  "type" => "spinner"),

				"crop_product_thumb" => array(
					"title" => esc_html__("Crop product's thumbnail",  'reisen'),
					"desc" => esc_html__("Crop product's thumbnails on search results page or scale it", 'reisen'),
					"std" => "no",
					"options" => reisen_get_options_param('list_yes_no'),
					"type" => "switch")
				
				)
			);

		}
	}
}

// WooCommerce hooks
if (!function_exists('reisen_woocommerce_theme_setup3')) {
	add_action( 'reisen_action_after_init_theme', 'reisen_woocommerce_theme_setup3' );
	function reisen_woocommerce_theme_setup3() {

		if (reisen_exists_woocommerce()) {

			add_action(    'woocommerce_before_subcategory_title',		'reisen_woocommerce_open_thumb_wrapper', 9 );
			add_action(    'woocommerce_before_shop_loop_item_title',	'reisen_woocommerce_open_thumb_wrapper', 9 );

			add_action(    'woocommerce_before_subcategory_title',		'reisen_woocommerce_open_item_wrapper', 20 );
			add_action(    'woocommerce_before_shop_loop_item_title',	'reisen_woocommerce_open_item_wrapper', 20 );

			add_action(    'woocommerce_after_subcategory',				'reisen_woocommerce_close_item_wrapper', 20 );
			add_action(    'woocommerce_after_shop_loop_item',			'reisen_woocommerce_close_item_wrapper', 20 );

			add_action(    'woocommerce_after_shop_loop_item_title',	'reisen_woocommerce_after_shop_loop_item_title', 7);

			add_action(    'woocommerce_after_subcategory_title',		'reisen_woocommerce_after_subcategory_title', 10 );

			// Remove link around product item
			remove_action('woocommerce_before_shop_loop_item',			'woocommerce_template_loop_product_link_open', 10);
			remove_action('woocommerce_after_shop_loop_item',			'woocommerce_template_loop_product_link_close', 5);
			// Remove link around product category
			remove_action('woocommerce_before_subcategory',				'woocommerce_template_loop_category_link_open', 10);
			remove_action('woocommerce_after_subcategory',				'woocommerce_template_loop_category_link_close', 10);

		}

		if (reisen_is_woocommerce_page()) {
			
			remove_action( 'woocommerce_sidebar', 						'woocommerce_get_sidebar', 10 );					// Remove WOOC sidebar
			
			remove_action( 'woocommerce_before_main_content',			'woocommerce_output_content_wrapper', 10);
			add_action(    'woocommerce_before_main_content',			'reisen_woocommerce_wrapper_start', 10);
			
			remove_action( 'woocommerce_after_main_content',			'woocommerce_output_content_wrapper_end', 10);		
			add_action(    'woocommerce_after_main_content',			'reisen_woocommerce_wrapper_end', 10);

			add_action(    'woocommerce_show_page_title',				'reisen_woocommerce_show_page_title', 10);

			remove_action( 'woocommerce_single_product_summary',		'woocommerce_template_single_title', 5);		
			add_action(    'woocommerce_single_product_summary',		'reisen_woocommerce_show_product_title', 5 );

			add_action(    'woocommerce_before_shop_loop', 				'reisen_woocommerce_before_shop_loop', 10 );

			remove_action( 'woocommerce_after_shop_loop',				'woocommerce_pagination', 10 );
			add_action(    'woocommerce_after_shop_loop',				'reisen_woocommerce_pagination', 10 );

			add_action(    'woocommerce_product_meta_end',				'reisen_woocommerce_show_product_id', 10);

			add_filter(    'woocommerce_output_related_products_args',	'reisen_woocommerce_output_related_products_args' );
			add_filter(    'woocommerce_related_products_args',			'reisen_woocommerce_related_products_args' );

			add_filter(    'woocommerce_product_thumbnails_columns',	'reisen_woocommerce_product_thumbnails_columns' );

			add_filter(    'get_product_search_form',					'reisen_woocommerce_get_product_search_form' );
			// Set columns number for the products loop
			if ( ! get_theme_support( 'wc-product-grid-enable' ) ) {
			
				add_filter(    'post_class',								'reisen_woocommerce_loop_shop_columns_class' );
				add_action(    'the_title',									'reisen_woocommerce_the_title');
			}
			
			reisen_enqueue_popup();
		}
	}
}



// Check if WooCommerce installed and activated
if ( !function_exists( 'reisen_exists_woocommerce' ) ) {
	function reisen_exists_woocommerce() {
		return class_exists('Woocommerce');
	}
}

// Return true, if current page is any woocommerce page
if ( !function_exists( 'reisen_is_woocommerce_page' ) ) {
	function reisen_is_woocommerce_page() {
		$rez = false;
		if (reisen_exists_woocommerce()) {
			if (!reisen_storage_empty('pre_query')) {
				$id = reisen_storage_get_obj_property('pre_query', 'queried_object_id', 0);
				$rez = reisen_storage_call_obj_method('pre_query', 'get', 'post_type')=='product' 
						|| $id==wc_get_page_id('shop')
						|| $id==wc_get_page_id('cart')
						|| $id==wc_get_page_id('checkout')
						|| $id==wc_get_page_id('myaccount')
						|| reisen_storage_call_obj_method('pre_query', 'is_tax', 'product_cat')
						|| reisen_storage_call_obj_method('pre_query', 'is_tax', 'product_tag')
						|| reisen_storage_call_obj_method('pre_query', 'is_tax', get_object_taxonomies('product'));
						
			} else
				$rez = is_shop() || is_product() || is_product_category() || is_product_tag() || is_product_taxonomy() || is_cart() || is_checkout() || is_account_page();
		}
		return $rez;
	}
}

// Filter to detect current page inheritance key
if ( !function_exists( 'reisen_woocommerce_detect_inheritance_key' ) ) {
	//Handler of add_filter('reisen_filter_detect_inheritance_key',	'reisen_woocommerce_detect_inheritance_key', 9, 1);
	function reisen_woocommerce_detect_inheritance_key($key) {
		if (!empty($key)) return $key;
		return reisen_is_woocommerce_page() ? 'woocommerce' : '';
	}
}

// Filter to detect current template page id
if ( !function_exists( 'reisen_woocommerce_detect_template_page_id' ) ) {
	//Handler of add_filter('reisen_filter_detect_template_page_id',	'reisen_woocommerce_detect_template_page_id', 9, 2);
	function reisen_woocommerce_detect_template_page_id($id, $key) {
		if (!empty($id)) return $id;
		if ($key == 'woocommerce_cart')				$id = get_option('woocommerce_cart_page_id');
		else if ($key == 'woocommerce_checkout')	$id = get_option('woocommerce_checkout_page_id');
		else if ($key == 'woocommerce_account')		$id = get_option('woocommerce_account_page_id');
		else if ($key == 'woocommerce')				$id = get_option('woocommerce_shop_page_id');
		return $id;
	}
}

// Filter to detect current page type (slug)
if ( !function_exists( 'reisen_woocommerce_get_blog_type' ) ) {
	//Handler of add_filter('reisen_filter_get_blog_type',	'reisen_woocommerce_get_blog_type', 9, 2);
	function reisen_woocommerce_get_blog_type($page, $query=null) {
		if (!empty($page)) return $page;
		
		if (is_shop()) 					$page = 'woocommerce_shop';
		else if ($query && $query->get('post_type')=='product' || is_product())		$page = 'woocommerce_product';
		else if ($query && $query->get('product_tag')!='' || is_product_tag())		$page = 'woocommerce_tag';
		else if ($query && $query->get('product_cat')!='' || is_product_category())	$page = 'woocommerce_category';
		else if (is_cart())				$page = 'woocommerce_cart';
		else if (is_checkout())			$page = 'woocommerce_checkout';
		else if (is_account_page())		$page = 'woocommerce_account';
		else if (is_woocommerce())		$page = 'woocommerce';
		return $page;
	}
}

// Filter to detect current page title
if ( !function_exists( 'reisen_woocommerce_get_blog_title' ) ) {
	//Handler of add_filter('reisen_filter_get_blog_title',	'reisen_woocommerce_get_blog_title', 9, 2);
	function reisen_woocommerce_get_blog_title($title, $page) {
		if (!empty($title)) return $title;
		
		if ( reisen_strpos($page, 'woocommerce')!==false ) {
			if ( $page == 'woocommerce_category' ) {
				$term = get_term_by( 'slug', get_query_var( 'product_cat' ), 'product_cat', OBJECT);
				$title = $term->name;
			} else if ( $page == 'woocommerce_tag' ) {
				$term = get_term_by( 'slug', get_query_var( 'product_tag' ), 'product_tag', OBJECT);
				$title = esc_html__('Tag:', 'reisen') . ' ' . esc_html($term->name);
			} else if ( $page == 'woocommerce_cart' ) {
				$title = esc_html__( 'Your cart', 'reisen' );
			} else if ( $page == 'woocommerce_checkout' ) {
				$title = esc_html__( 'Checkout', 'reisen' );
			} else if ( $page == 'woocommerce_account' ) {
				$title = esc_html__( 'Account', 'reisen' );
			} else if ( $page == 'woocommerce_product' ) {
				$title = reisen_get_post_title();
			} else if (($page_id=get_option('woocommerce_shop_page_id')) > 0) {
				$title = reisen_get_post_title($page_id);
			} else {
				$title = esc_html__( 'Shop', 'reisen' );
			}
		}
		
		return $title;
	}
}

// Filter to detect stream page title
if ( !function_exists( 'reisen_woocommerce_get_stream_page_title' ) ) {
	//Handler of add_filter('reisen_filter_get_stream_page_title',	'reisen_woocommerce_get_stream_page_title', 9, 2);
	function reisen_woocommerce_get_stream_page_title($title, $page) {
		if (!empty($title)) return $title;
		if (reisen_strpos($page, 'woocommerce')!==false) {
			if (($page_id = reisen_woocommerce_get_stream_page_id(0, $page)) > 0)
				$title = reisen_get_post_title($page_id);
			else
				$title = esc_html__('Shop', 'reisen');				
		}
		return $title;
	}
}

// Filter to detect stream page ID
if ( !function_exists( 'reisen_woocommerce_get_stream_page_id' ) ) {
	//Handler of add_filter('reisen_filter_get_stream_page_id',	'reisen_woocommerce_get_stream_page_id', 9, 2);
	function reisen_woocommerce_get_stream_page_id($id, $page) {
		if (!empty($id)) return $id;
		if (reisen_strpos($page, 'woocommerce')!==false) {
			$id = get_option('woocommerce_shop_page_id');
		}
		return $id;
	}
}

// Filter to detect stream page link
if ( !function_exists( 'reisen_woocommerce_get_stream_page_link' ) ) {
	//Handler of add_filter('reisen_filter_get_stream_page_link',	'reisen_woocommerce_get_stream_page_link', 9, 2);
	function reisen_woocommerce_get_stream_page_link($url, $page) {
		if (!empty($url)) return $url;
		if (reisen_strpos($page, 'woocommerce')!==false) {
			$id = reisen_woocommerce_get_stream_page_id(0, $page);
			if ($id) $url = get_permalink($id);
		}
		return $url;
	}
}

// Filter to detect current taxonomy
if ( !function_exists( 'reisen_woocommerce_get_current_taxonomy' ) ) {
	//Handler of add_filter('reisen_filter_get_current_taxonomy',	'reisen_woocommerce_get_current_taxonomy', 9, 2);
	function reisen_woocommerce_get_current_taxonomy($tax, $page) {
		if (!empty($tax)) return $tax;
		if ( reisen_strpos($page, 'woocommerce')!==false ) {
			$tax = 'product_cat';
		}
		return $tax;
	}
}

// Return taxonomy name (slug) if current page is this taxonomy page
if ( !function_exists( 'reisen_woocommerce_is_taxonomy' ) ) {
	//Handler of add_filter('reisen_filter_is_taxonomy',	'reisen_woocommerce_is_taxonomy', 9, 2);
	function reisen_woocommerce_is_taxonomy($tax, $query=null) {
		if (!empty($tax))
			return $tax;
		else 
			return $query!==null && $query->get('product_cat')!='' || is_product_category() ? 'product_cat' : '';
	}
}

// Return false if current plugin not need theme orderby setting
if ( !function_exists( 'reisen_woocommerce_orderby_need' ) ) {
	//Handler of add_filter('reisen_filter_orderby_need',	'reisen_woocommerce_orderby_need', 9, 1);
	function reisen_woocommerce_orderby_need($need) {
		if ($need == false || reisen_storage_empty('pre_query'))
			return $need;
		else {
			return reisen_storage_call_obj_method('pre_query', 'get', 'post_type')!='product' 
					&& reisen_storage_call_obj_method('pre_query', 'get', 'product_cat')==''
					&& reisen_storage_call_obj_method('pre_query', 'get', 'product_tag')=='';
		}
	}
}

// Add custom post type into list
if ( !function_exists( 'reisen_woocommerce_list_post_types' ) ) {
	//Handler of add_filter('reisen_filter_list_post_types', 	'reisen_woocommerce_list_post_types', 10, 1);
	function reisen_woocommerce_list_post_types($list) {
		$list['product'] = esc_html__('Products', 'reisen');
		return $list;
	}
}


	
// Enqueue WooCommerce custom styles
if ( !function_exists( 'reisen_woocommerce_frontend_scripts' ) ) {
	//Handler of add_action( 'reisen_action_add_styles', 'reisen_woocommerce_frontend_scripts' );
	function reisen_woocommerce_frontend_scripts() {
			if (file_exists(reisen_get_file_dir('css/plugin.woocommerce.css')))
				wp_enqueue_style( 'reisen-plugin-woocommerce-style',  reisen_get_file_url('css/plugin.woocommerce.css'), array(), null );
	}
}

// Before main content
if ( !function_exists( 'reisen_woocommerce_wrapper_start' ) ) {
	//Handler of add_action('woocommerce_before_main_content', 'reisen_woocommerce_wrapper_start', 10);
	function reisen_woocommerce_wrapper_start() {
		if (is_product() || is_cart() || is_checkout() || is_account_page()) {
			?>
			<article class="post_item post_item_single post_item_product">
			<?php
		} else {
			?>
			<div class="list_products shop_mode_<?php echo !reisen_storage_empty('shop_mode') ? reisen_storage_get('shop_mode') : 'thumbs'; ?>">
			<?php
		}
	}
}

// After main content
if ( !function_exists( 'reisen_woocommerce_wrapper_end' ) ) {
	//Handler of add_action('woocommerce_after_main_content', 'reisen_woocommerce_wrapper_end', 10);
	function reisen_woocommerce_wrapper_end() {
		if (is_product() || is_cart() || is_checkout() || is_account_page()) {
			?>
			</article>	<!-- .post_item -->
			<?php
		} else {
			?>
			</div>	<!-- .list_products -->
			<?php
		}
	}
}

// Check to show page title
if ( !function_exists( 'reisen_woocommerce_show_page_title' ) ) {
	//Handler of add_action('woocommerce_show_page_title', 'reisen_woocommerce_show_page_title', 10);
	function reisen_woocommerce_show_page_title($defa=true) {
		return reisen_get_custom_option('show_page_title')=='no';
	}
}

// Check to show product title
if ( !function_exists( 'reisen_woocommerce_show_product_title' ) ) {
	//Handler of add_action( 'woocommerce_single_product_summary', 'reisen_woocommerce_show_product_title', 5 );
	function reisen_woocommerce_show_product_title() {
		if (reisen_get_custom_option('show_post_title')=='yes' || reisen_get_custom_option('show_page_title')=='no') {
			wc_get_template( 'single-product/title.php' );
		}
	}
}

// Add list mode buttons
if ( !function_exists( 'reisen_woocommerce_before_shop_loop' ) ) {
	//Handler of add_action( 'woocommerce_before_shop_loop', 'reisen_woocommerce_before_shop_loop', 10 );
	function reisen_woocommerce_before_shop_loop() {
		if (reisen_get_custom_option('show_mode_buttons')=='yes') {
			echo '<div class="mode_buttons"><form action="' . esc_url(reisen_get_current_url()) . '" method="post">'
				. '<input type="hidden" name="reisen_shop_mode" value="'.esc_attr(reisen_storage_get('shop_mode')).'" />'
				. '<a href="#" class="woocommerce_thumbs icon-th" title="'.esc_attr__('Show products as thumbs', 'reisen').'"></a>'
				. '<a href="#" class="woocommerce_list icon-th-list" title="'.esc_attr__('Show products as list', 'reisen').'"></a>'
				. '</form></div>';
		}
	}
}


// Open thumbs wrapper for categories and products
if ( !function_exists( 'reisen_woocommerce_open_thumb_wrapper' ) ) {
	//Handler of add_action( 'woocommerce_before_subcategory_title', 'reisen_woocommerce_open_thumb_wrapper', 9 );
	//Handler of add_action( 'woocommerce_before_shop_loop_item_title', 'reisen_woocommerce_open_thumb_wrapper', 9 );
	function reisen_woocommerce_open_thumb_wrapper($cat='') {
		reisen_storage_set('in_product_item', true);
		?>
		<div class="post_item_wrap">
			<div class="post_featured">
				<div class="post_thumb">
					<a class="hover_icon hover_icon_link" href="<?php echo esc_url(is_object($cat) ? get_term_link($cat->slug, 'product_cat') : get_permalink()); ?>">
		<?php
	}
}

// Open item wrapper for categories and products
if ( !function_exists( 'reisen_woocommerce_open_item_wrapper' ) ) {
	//Handler of add_action( 'woocommerce_before_subcategory_title', 'reisen_woocommerce_open_item_wrapper', 20 );
	//Handler of add_action( 'woocommerce_before_shop_loop_item_title', 'reisen_woocommerce_open_item_wrapper', 20 );
	function reisen_woocommerce_open_item_wrapper($cat='') {
		?>
				</a>
			</div>
		</div>
		<div class="post_content">
		<?php
	}
}

// Close item wrapper for categories and products
if ( !function_exists( 'reisen_woocommerce_close_item_wrapper' ) ) {
	//Handler of add_action( 'woocommerce_after_subcategory', 'reisen_woocommerce_close_item_wrapper', 20 );
	//Handler of add_action( 'woocommerce_after_shop_loop_item', 'reisen_woocommerce_close_item_wrapper', 20 );
	function reisen_woocommerce_close_item_wrapper($cat='') {
		?>
			</div>
		</div>
		<?php
		reisen_storage_set('in_product_item', false);
	}
}

// Add excerpt in output for the product in the list mode
if ( !function_exists( 'reisen_woocommerce_after_shop_loop_item_title' ) ) {
	//Handler of add_action( 'woocommerce_after_shop_loop_item_title', 'reisen_woocommerce_after_shop_loop_item_title', 7);
	function reisen_woocommerce_after_shop_loop_item_title() {
		if (reisen_storage_get('shop_mode') == 'list') {
		    $excerpt = apply_filters('the_excerpt', get_the_excerpt());
			echo '<div class="description">'.trim($excerpt).'</div>';
		}
	}
}

// Add excerpt in output for the product in the list mode
if ( !function_exists( 'reisen_woocommerce_after_subcategory_title' ) ) {
	//Handler of add_action( 'woocommerce_after_subcategory_title', 'reisen_woocommerce_after_subcategory_title', 10 );
	function reisen_woocommerce_after_subcategory_title($category) {
		if (reisen_storage_get('shop_mode') == 'list')
			echo '<div class="description">' . trim($category->description) . '</div>';
	}
}

// Add Product ID for single product
if ( !function_exists( 'reisen_woocommerce_show_product_id' ) ) {
	//Handler of add_action( 'woocommerce_product_meta_end', 'reisen_woocommerce_show_product_id', 10);
	function reisen_woocommerce_show_product_id() {
		global $post, $product;
		echo '<span class="product_id">'.esc_html__('Product ID: ', 'reisen') . '<span>' . ($post->ID) . '</span></span>';
	}
}

// Redefine number of related products
if ( !function_exists( 'reisen_woocommerce_output_related_products_args' ) ) {
	//Handler of add_filter( 'woocommerce_output_related_products_args', 'reisen_woocommerce_output_related_products_args' );
	function reisen_woocommerce_output_related_products_args($args) {
		$ppp = $ccc = 0;
		if (reisen_param_is_on(reisen_get_custom_option('show_post_related'))) {
			$ccc_add = in_array(reisen_get_custom_option('body_style'), array('fullwide', 'fullscreen')) ? 1 : 0;
			$ccc =  reisen_get_custom_option('post_related_columns');
			$ccc = $ccc > 0 ? $ccc : (reisen_param_is_off(reisen_get_custom_option('show_sidebar_main')) ? 3+$ccc_add : 2+$ccc_add);
			$ppp = reisen_get_custom_option('post_related_count');
			$ppp = $ppp > 0 ? $ppp : $ccc;
		}
		$args['posts_per_page'] = $ppp;
		$args['columns'] = $ccc;
		return $args;
	}
}

// Redefine post_type if number of related products == 0
if ( !function_exists( 'reisen_woocommerce_related_products_args' ) ) {
	//Handler of add_filter( 'woocommerce_related_products_args', 'reisen_woocommerce_related_products_args' );
	function reisen_woocommerce_related_products_args($args) {
		if ($args['posts_per_page'] == 0)
			$args['post_type'] .= '_';
		return $args;
	}
}

// Number columns for product thumbnails
if ( !function_exists( 'reisen_woocommerce_product_thumbnails_columns' ) ) {
	//Handler of add_filter( 'woocommerce_product_thumbnails_columns', 'reisen_woocommerce_product_thumbnails_columns' );
	function reisen_woocommerce_product_thumbnails_columns($cols) {
		return 4;
	}
}

// Add column class into product item in shop streampage
if ( !function_exists( 'reisen_woocommerce_loop_shop_columns_class' ) ) {
	//Handler of add_filter( 'post_class', 'reisen_woocommerce_loop_shop_columns_class' );
	function reisen_woocommerce_loop_shop_columns_class($class) {
		global $woocommerce_loop;
		if (is_product()) {
			if (!empty($woocommerce_loop['columns']))
			$class[] = ' column-1_'.esc_attr($woocommerce_loop['columns']);
		} else if (!is_product() && !is_cart() && !is_checkout() && !is_account_page()) {
            $cols = function_exists('wc_get_default_products_per_row') ? wc_get_default_products_per_row() : 2;
            $class[] = ' column-1_' . $cols;
		}
		return $class;
	}
}

// Search form
if ( !function_exists( 'reisen_woocommerce_get_product_search_form' ) ) {
	//Handler of add_filter( 'get_product_search_form', 'reisen_woocommerce_get_product_search_form' );
	function reisen_woocommerce_get_product_search_form($form) {
		return '
		<form role="search" method="get" class="search_form" action="' . esc_url(home_url('/')) . '">
			<input type="text" class="search_field" placeholder="' . esc_attr__('Search for products &hellip;', 'reisen') . '" value="' . esc_attr(get_search_query()) . '" name="s" title="' . esc_attr__('Search for products:', 'reisen') . '" /><button class="search_button icon-search" type="submit"></button>
			<input type="hidden" name="post_type" value="product" />
		</form>
		';
	}
}

// Wrap product title into link
if ( !function_exists( 'reisen_woocommerce_the_title' ) ) {
	//Handler of add_filter( 'the_title', 'reisen_woocommerce_the_title' );
	function reisen_woocommerce_the_title($title) {
		if (reisen_storage_get('in_product_item') && get_post_type()=='product') {
			$title = '<a href="'.esc_url(get_permalink()).'">'.($title).'</a>';
		}
		return $title;
	}
}

// Show pagination links
if ( !function_exists( 'reisen_woocommerce_pagination' ) ) {
	//Handler of add_filter( 'woocommerce_after_shop_loop', 'reisen_woocommerce_pagination', 10 );
	function reisen_woocommerce_pagination() {
		$style = reisen_get_custom_option('blog_pagination');
		reisen_show_pagination(array(
			'class' => 'pagination_wrap pagination_' . esc_attr($style),
			'style' => $style,
			'button_class' => '',
			'first_text'=> '',
			'last_text' => '',
			'prev_text' => '',
			'next_text' => '',
			'pages_in_group' => $style=='pages' ? reisen_get_custom_option('shop_pages_in_group') : 20
			)
		);
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'reisen_woocommerce_required_plugins' ) ) {
	//Handler of add_filter('reisen_filter_required_plugins',	'reisen_woocommerce_required_plugins');
	function reisen_woocommerce_required_plugins($list=array()) {
		if (in_array('woocommerce', reisen_storage_get('required_plugins')))
			$list[] = array(
					'name' 		=> 'WooCommerce',
					'slug' 		=> 'woocommerce',
					'required' 	=> false
				);

		return $list;
	}
}

// Show products navigation
if ( !function_exists( 'reisen_woocommerce_show_post_navi' ) ) {
	//Handler of add_filter('reisen_filter_show_post_navi', 'reisen_woocommerce_show_post_navi');
	function reisen_woocommerce_show_post_navi($show=false) {
		return $show || (reisen_get_custom_option('show_page_title')=='yes' && is_single() && reisen_is_woocommerce_page());
	}
}

// Wrap category title into link
if ( !function_exists( 'reisen_woocommerce_shop_loop_subcategory_title' ) ) {
    //Handler of the add_filter( 'woocommerce_shop_loop_subcategory_title', 'reisen_woocommerce_shop_loop_subcategory_title' );
    function reisen_woocommerce_shop_loop_subcategory_title($cat) {

        $cat->name = sprintf('<a href="%s">%s</a>', esc_url(get_term_link($cat->slug, 'product_cat')), $cat->name);
        ?>
        <h2 class="woocommerce-loop-category__title">
        <?php
        reisen_show_layout($cat->name);

        if ( $cat->count > 0 ) {
            echo apply_filters( 'woocommerce_subcategory_count_html', ' <mark class="count">(' . esc_html( $cat->count ) . ')</mark>', $cat ); // WPCS: XSS ok.
        }
        ?>
        </h2><?php
    }
}
?>