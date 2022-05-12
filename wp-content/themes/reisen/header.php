<?php
/**
 * The Header for our theme.
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="<?php
		// Add class 'scheme_xxx' into <html> because it used as context for the body classes!
		$reisen_body_scheme = reisen_get_custom_option('body_scheme');
		if (empty($reisen_body_scheme) || reisen_is_inherit_option($reisen_body_scheme)) $reisen_body_scheme = 'original';
		echo 'scheme_' . esc_attr($reisen_body_scheme); 
		?>">

<head>
	<?php wp_head(); ?>
</head>

<body <?php body_class();?>>
      <?php wp_body_open(); ?>

	<?php do_action( 'before' ); ?>

	<?php if ( !reisen_param_is_off(reisen_get_custom_option('show_sidebar_outer')) ) { ?>
	<div class="outer_wrap">
	<?php } ?>

	<?php get_template_part(reisen_get_file_slug('sidebar_outer.php')); ?>

	<?php
		$reisen_body_style  = reisen_get_custom_option('body_style');
		$reisen_class = '';
		if (reisen_get_custom_option('bg_custom')=='yes' && ($reisen_body_style=='boxed' || reisen_get_custom_option('bg_image_load')=='always')) {
			if (($reisen_img = reisen_get_custom_option('bg_image')) > 0)
				$reisen_class = 'bg_image_'.($reisen_img);
			else if (($reisen_img = reisen_get_custom_option('bg_pattern')) > 0)
				$reisen_class = 'bg_pattern_'.($reisen_img);
		}
	?>

	<div class="body_wrap<?php echo !empty($reisen_class) ? ' '.esc_attr($reisen_class) : ''; ?>">

		<?php
		$reisen_video_bg_show = reisen_get_custom_option('show_video_bg')=='yes';
		$reisen_youtube = reisen_get_custom_option('video_bg_youtube_code');
		$reisen_video   = reisen_get_custom_option('video_bg_url');
		$reisen_overlay = reisen_get_custom_option('video_bg_overlay')=='yes';
		if ($reisen_video_bg_show && (!empty($reisen_youtube) || !empty($reisen_video))) {
			if (!empty($reisen_youtube)) {
				?>
				<div class="video_bg<?php echo !empty($reisen_overlay) ? ' video_bg_overlay' : ''; ?>" data-youtube-code="<?php echo esc_attr($reisen_youtube); ?>"></div>
				<?php
			} else if (!empty($reisen_video)) {
				$reisen_info = pathinfo($reisen_video);
				$reisen_ext = !empty($reisen_info['extension']) ? $reisen_info['extension'] : 'src';
				?>
				<div class="video_bg<?php echo !empty($reisen_overlay) ? ' video_bg_overlay' : ''; ?>"><video class="video_bg_tag" width="1280" height="720" data-width="1280" data-height="720" data-ratio="16:9" preload="metadata" autoplay loop src="<?php echo esc_url($reisen_video); ?>"><source src="<?php echo esc_url($reisen_video); ?>" type="video/<?php echo esc_attr($reisen_ext); ?>"></source></video></div>
				<?php
			}
		}
		?>

		<div class="page_wrap">

			<?php
			$reisen_top_panel_style = reisen_get_custom_option('top_panel_style');
			$reisen_top_panel_position = reisen_get_custom_option('top_panel_position');
			$reisen_top_panel_scheme = reisen_get_custom_option('top_panel_scheme');
			// Top panel 'Above' or 'Over'
			if (in_array($reisen_top_panel_position, array('above', 'over'))) {
				reisen_show_post_layout(array(
					'layout' => $reisen_top_panel_style,
					'position' => $reisen_top_panel_position,
					'scheme' => $reisen_top_panel_scheme
					), false);
				// Mobile Menu
				get_template_part(reisen_get_file_slug('templates/headers/_parts/header-mobile.php'));
			}

			// Slider
			get_template_part(reisen_get_file_slug('templates/headers/_parts/slider.php'));
			
			// Top panel 'Below'
			if ($reisen_top_panel_position == 'below') {
				reisen_show_post_layout(array(
					'layout' => $reisen_top_panel_style,
					'position' => $reisen_top_panel_position,
					'scheme' => $reisen_top_panel_scheme
					), false);
				// Mobile Menu
				get_template_part(reisen_get_file_slug('templates/headers/_parts/header-mobile.php'));
			}

			// Top of page section: page title and breadcrumbs
			$reisen_show_title = reisen_get_custom_option('show_page_title')=='yes' && !is_home() && !is_front_page();
			$reisen_show_navi = false;
			$reisen_show_breadcrumbs = reisen_get_custom_option('show_breadcrumbs')=='yes' && !is_home() && !is_front_page();

			if ($reisen_show_title || $reisen_show_breadcrumbs) {
				?>
				<div class="top_panel_title top_panel_style_<?php echo esc_attr(str_replace('header_', '', $reisen_top_panel_style)); ?> <?php echo (!empty($reisen_show_title) ? ' title_present'.  ($reisen_show_navi ? ' navi_present' : '') : '') . (!empty($reisen_show_breadcrumbs) ? ' breadcrumbs_present' : ''); ?> scheme_<?php echo esc_attr($reisen_top_panel_scheme); ?>">
					<div class="top_panel_title_inner top_panel_inner_style_<?php echo esc_attr(str_replace('header_', '', $reisen_top_panel_style)); ?> <?php echo (!empty($reisen_show_title) ? ' title_present_inner' : '') . (!empty($reisen_show_breadcrumbs) ? ' breadcrumbs_present_inner' : ''); ?>">
						<div class="content_wrap">
							<?php
							if ($reisen_show_title) {
									?><h1 class="page_title"><?php echo wp_kses_post(reisen_get_blog_title()); ?></h1><?php
							}
							if ($reisen_show_breadcrumbs) {
								?><div class="breadcrumbs"><?php if (!is_404()) reisen_show_breadcrumbs(); ?></div><?php
							}
							?>
						</div>
					</div>
				</div>
				<?php
			}
			?>

			<div class="page_content_wrap page_paddings_<?php echo esc_attr(reisen_get_custom_option('body_paddings')); ?>">

				<?php
				// Content and sidebar wrapper
				if ($reisen_body_style!='fullscreen') reisen_open_wrapper('<div class="content_wrap">');
				
				// Main content wrapper
				reisen_open_wrapper('<div class="content">');
				?>