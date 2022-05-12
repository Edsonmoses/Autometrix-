<?php

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'reisen_template_header_4_theme_setup' ) ) {
	add_action( 'reisen_action_before_init_theme', 'reisen_template_header_4_theme_setup', 1 );
	function reisen_template_header_4_theme_setup() {
		reisen_add_template(array(
			'layout' => 'header_4',
			'mode'   => 'header',
			'title'  => esc_html__('Header 4', 'reisen'),
			'icon'   => reisen_get_file_url('templates/headers/images/4.jpg')
			));
	}
}

// Template output
if ( !function_exists( 'reisen_template_header_4_output' ) ) {
	function reisen_template_header_4_output($post_options, $post_data) {

		// WP custom header
		$header_css = '';
		if ($post_options['position'] != 'over') {
			$header_image = get_header_image();
			$header_css = $header_image!='' 
				? ' style="background-image: url('.esc_url($header_image).')"' 
				: '';
		}
		?>
		

		<div class="top_panel_fixed_wrap"></div>

		<header class="top_panel_wrap top_panel_style_4 scheme_<?php echo esc_attr($post_options['scheme']); ?>">
			<div class="top_panel_wrap_inner top_panel_inner_style_4 top_panel_position_<?php echo esc_attr(reisen_get_custom_option('top_panel_position')); ?>">
			
			<?php if (reisen_get_custom_option('show_top_panel_top')=='yes') { ?>
				<div class="top_panel_top">
					<div class="content_wrap clearfix">
						<?php
						reisen_template_set_args('top-panel-top', array(
							'top_panel_top_components' => array('contact_info', 'login', 'cart', 'currency', 'bookmarks')
						));
						get_template_part(reisen_get_file_slug('templates/headers/_parts/top-panel-top.php'));
						?>
					</div>
				</div>
			<?php } ?>

			<div class="top_panel_middle" <?php reisen_show_layout($header_css); ?>>
				<div class="content_wrap">
					<div class="contact_logo">
						<?php reisen_show_logo(true, true); ?>
					</div>
					<div class="menu_main_wrap">
						<nav class="menu_main_nav_area menu_hover_<?php echo esc_attr(reisen_get_theme_option('menu_hover')); ?>">
							<?php
							$menu_main = reisen_get_nav_menu('menu_main');
							if (empty($menu_main)) $menu_main = reisen_get_nav_menu();
							reisen_show_layout($menu_main);
							?>
						</nav>
						<?php if (reisen_get_custom_option('show_search')=='yes' && function_exists('reisen_sc_search')) reisen_show_layout(reisen_sc_search(array('class'=>"top_panel_icon",'state'=>"closed", "style"=>reisen_get_theme_option('search_style'))));

                        if (function_exists('reisen_exists_woocommerce') && reisen_exists_woocommerce() && (reisen_is_woocommerce_page() && reisen_get_custom_option('show_cart')=='shop' || reisen_get_custom_option('show_cart')=='always') && !(is_checkout() || is_cart() || defined('WOOCOMMERCE_CHECKOUT') || defined('WOOCOMMERCE_CART'))) {
                            ?>
                            <div class="menu_main_cart top_panel_icon">
                                <?php do_action('trx_utils_show_contact_info_cart'); ?>
                            </div>
                        <?php
                        }
                        ?>

					</div>
				</div>
			</div>

			</div>
		</header>

		<?php
		reisen_storage_set('header_mobile', array(
				 'open_hours' => false,
				 'login' => false,
				 'socials' => false,
				 'bookmarks' => false,
				 'contact_address' => false,
				 'contact_phone_email' => false,
				 'woo_cart' => false,
				 'search' => false
			)
		);
	}
}
?>