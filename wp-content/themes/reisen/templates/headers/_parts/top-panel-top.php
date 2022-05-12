<?php 
// Get template args
extract(reisen_template_get_args('top-panel-top'));

if (in_array('contact_info', $top_panel_top_components) && ($contact_info=trim(reisen_get_custom_option('contact_info')))!='') {
	?>
	<div class="top_panel_top_contact_area">
		<?php echo wp_kses_data($contact_info); ?>
	</div>
	<?php
}
?>

<?php
if (in_array('open_hours', $top_panel_top_components) && ($open_hours=trim(reisen_get_custom_option('contact_open_hours')))!='') {
	?>
	<div class="top_panel_top_open_hours icon-clock"><?php echo wp_kses_data($open_hours); ?></div>
	<?php
}
?>

<div class="top_panel_top_user_area">
	<?php
	if (in_array('socials', $top_panel_top_components) && reisen_get_custom_option('show_socials')=='yes' && function_exists('reisen_sc_socials')) {
		?>
		<div class="top_panel_top_socials">
			<?php reisen_show_layout(reisen_sc_socials(array('size'=>'tiny'))); ?>
		</div>
		<?php
	}

	if (in_array('search', $top_panel_top_components) && reisen_get_custom_option('show_search')=='yes' && function_exists('reisen_sc_search')) {
		?>
		<div class="top_panel_top_search"><?php reisen_show_layout(reisen_sc_search(array(
			"style" => reisen_get_theme_option('search_style'),
			'state' => reisen_get_theme_option('search_style')=='default' ? 'fixed' : 'closed'))); ?></div>
		<?php
	}

	$menu_user = reisen_get_nav_menu('menu_user');
	if (empty($menu_user)) {
		?>
		<ul id="<?php echo (!empty($menu_user_id) ? esc_attr($menu_user_id) : 'menu_user'); ?>" class="menu_user_nav">
		<?php
	} else {
		$menu = reisen_substr($menu_user, 0, reisen_strlen($menu_user)-5);
		$pos = reisen_strpos($menu, '<ul');
		if ($pos!==false && reisen_strpos($menu, 'menu_user_nav')===false)
			$menu = reisen_substr($menu, 0, $pos+3) . ' class="menu_user_nav"' . reisen_substr($menu, $pos+3);
		if (!empty($menu_user_id))
			$menu = reisen_set_tag_attrib($menu, '<ul>', 'id', $menu_user_id);
		echo str_replace('class=""', '', $menu);
	}
	

	if (in_array('currency', $top_panel_top_components) && function_exists('reisen_is_woocommerce_page') && reisen_is_woocommerce_page() && reisen_get_custom_option('show_currency')=='yes') {
		?>
		<li class="menu_user_currency">
			<a href="#">$</a>
			<ul>
				<li><a href="#"><b>&#36;</b> <?php esc_html_e('Dollar', 'reisen'); ?></a></li>
				<li><a href="#"><b>&euro;</b> <?php esc_html_e('Euro', 'reisen'); ?></a></li>
				<li><a href="#"><b>&pound;</b> <?php esc_html_e('Pounds', 'reisen'); ?></a></li>
			</ul>
		</li>
		<?php
	}

	if (in_array('language', $top_panel_top_components) && reisen_get_custom_option('show_languages')=='yes' && function_exists('icl_get_languages')) {
		$languages = icl_get_languages('skip_missing=1');
		if (!empty($languages) && is_array($languages)) {
			$lang_list = '';
			$lang_active = '';
			foreach ($languages as $lang) {
				$lang_title = esc_attr($lang['translated_name']);
				if ($lang['active']) {
					$lang_active = $lang_title;
				}
				$lang_list .= "\n"
					.'<li><a rel="alternate" hreflang="' . esc_attr($lang['language_code']) . '" href="' . esc_url(apply_filters('WPML_filter_link', $lang['url'], $lang)) . '">'
						.'<img src="' . esc_url($lang['country_flag_url']) . '" alt="' . esc_attr($lang_title) . '" title="' . esc_attr($lang_title) . '" />'
						. ($lang_title)
					.'</a></li>';
			}
			?>
			<li class="menu_user_language">
				<a href="#"><span><?php reisen_show_layout(wp_kses_data($lang_active)); ?></span></a>
				<ul><?php reisen_show_layout(wp_kses_data($lang_list)); ?></ul>
			</li>
			<?php
		}
	}

	if (in_array('bookmarks', $top_panel_top_components) && reisen_get_custom_option('show_bookmarks')=='yes') {
		// Load core messages
		reisen_enqueue_messages();
		?>
		<li class="menu_user_bookmarks"><a href="#" class="bookmarks_show icon-star" title="<?php esc_attr_e('Show bookmarks', 'reisen'); ?>"><?php esc_html_e('Bookmarks', 'reisen'); ?></a>
		<?php
			$list = reisen_get_value_gpc('reisen_bookmarks', '');
			if (!empty($list)) $list = json_decode($list, true);
			?>
			<ul class="bookmarks_list">
				<li><a href="#" class="bookmarks_add icon-star-empty" title="<?php esc_attr_e('Add the current page into bookmarks', 'reisen'); ?>"><?php esc_html_e('Add bookmark', 'reisen'); ?></a></li>
				<?php 
				if (!empty($list) && is_array($list)) {
					foreach ($list as $bm) {
						echo '<li><a href="'.esc_url($bm['url']).'" class="bookmarks_item">'.($bm['title']).'<span class="bookmarks_delete icon-cancel" title="'.esc_attr__('Delete this bookmark', 'reisen').'"></span></a></li>';
					}
				}
				?>
			</ul>
		</li>
		<?php 
	}

	if (in_array('login', $top_panel_top_components) && reisen_get_custom_option('show_login')=='yes') {
        if ( !is_user_logged_in() ) {
            // Load core messages
            reisen_enqueue_messages();
            // Load Popup engine
            reisen_enqueue_popup();
            // Anyone can register ?
            if ( (int) get_option('users_can_register') > 0) {
                ?><li class="menu_user_register"><?php do_action('trx_utils_action_register'); ?></li><?php
            }
            ?><li class="menu_user_login"><?php do_action('trx_utils_action_login'); ?></li><?php
        } else {
			$current_user = wp_get_current_user();
			?>
			<li class="menu_user_controls">
				<a href="#"><?php
					$user_avatar = '';
					$mult = reisen_get_retina_multiplier();
					if ($current_user->user_email) $user_avatar = get_avatar($current_user->user_email, 16*$mult);
					if ($user_avatar) {
						?><span class="user_avatar"><?php reisen_show_layout($user_avatar); ?></span><?php
					}?><span class="user_name"><?php reisen_show_layout(wp_kses_data($current_user->display_name)); ?></span></a>
				<ul>
					<?php if (current_user_can('publish_posts')) { ?>
					<li><a href="<?php echo esc_url(home_url('/')); ?>/wp-admin/post-new.php?post_type=post" class="icon icon-doc"><?php esc_html_e('New post', 'reisen'); ?></a></li>
					<?php } ?>
					<li><a href="<?php echo esc_url(get_edit_user_link()); ?>" class="icon icon-cog"><?php esc_html_e('Settings', 'reisen'); ?></a></li>
				</ul>
			</li>
			<li class="menu_user_logout"><a href="<?php echo esc_url(wp_logout_url(home_url('/'))); ?>" class="icon icon-logout"><?php esc_html_e('Logout', 'reisen'); ?></a></li>
			<?php 
		}
	}

	if (in_array('cart', $top_panel_top_components) && function_exists('reisen_exists_woocommerce') && reisen_exists_woocommerce() && (reisen_is_woocommerce_page() && reisen_get_custom_option('show_cart')=='shop' || reisen_get_custom_option('show_cart')=='always') && !(is_checkout() || is_cart() || defined('WOOCOMMERCE_CHECKOUT') || defined('WOOCOMMERCE_CART'))) { 
		?>
		<li class="menu_user_cart">
			<?php do_action('trx_utils_show_contact_info_cart'); ?>
		</li>
		<?php
	}
	?>

	</ul>

</div>