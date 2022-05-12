<?php

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'reisen_template_single_team_theme_setup' ) ) {
	add_action( 'reisen_action_before_init_theme', 'reisen_template_single_team_theme_setup', 1 );
	function reisen_template_single_team_theme_setup() {
		reisen_add_template(array(
			'layout' => 'single-team',
			'mode'   => 'single_team',
			'need_content' => true,
			'need_terms' => true,
			'title'  => esc_html__('Single Team member', 'reisen'),
			'thumb_title'  => esc_html__('Large image (crop)', 'reisen'),
			'w'		 => 770,
			'h'		 => 434
		));
	}
}

// Template output
if ( !function_exists( 'reisen_template_single_team_output' ) ) {
	function reisen_template_single_team_output($post_options, $post_data) {
		$post_data['post_views']++;
		$show_title = reisen_get_custom_option('show_post_title')=='yes';
		$title_tag = reisen_get_custom_option('show_page_title')=='yes' ? 'h3' : 'h1';
        $post_meta = get_post_meta($post_data['post_id'], 'reisen_team_data', true);

		reisen_open_wrapper('<article class="' 
				. join(' ', get_post_class('itemscope'
					. ' post_item post_item_single_team'
					. ' post_featured_' . esc_attr($post_options['post_class'])
					. ' post_format_' . esc_attr($post_data['post_format'])))
				. '"'
				. ' itemscope itemtype="//schema.org/Article'
				. '">');

		if ($show_title && $post_options['location'] == 'center' && reisen_get_custom_option('show_page_title')=='no') {
			?>
			<<?php echo esc_attr($title_tag); ?> itemprop="headline" class="post_title entry-title"><span class="post_icon <?php echo esc_attr($post_data['post_icon']); ?>"></span><?php reisen_show_layout($post_data['post_title']); ?></<?php echo esc_attr($title_tag); ?>>
			<?php 
		}

		if (!$post_data['post_protected'] && (
			!empty($post_options['dedicated']) ||
			(reisen_get_custom_option('show_featured_image')=='yes' && $post_data['post_thumb'])
		)) {
			?>
			<section class="post_featured single_team_post_featured">
				<?php
				if (!empty($post_options['dedicated'])) {
					reisen_show_layout($post_options['dedicated']);
				} else {
					reisen_enqueue_popup();
					?>
					<div class="post_thumb" data-image="<?php echo esc_url($post_data['post_attachment']); ?>" data-title="<?php echo esc_attr($post_data['post_title']); ?>">
						<a class="hover_icon hover_icon_view" href="<?php echo esc_url($post_data['post_attachment']); ?>" title="<?php echo esc_attr($post_data['post_title']); ?>"><?php reisen_show_layout($post_data['post_thumb']); ?></a>
					</div>
					<?php 
				}
	
				$soc_list = $post_meta['team_member_socials'];
				if (is_array($soc_list) && count($soc_list)>0) {
					$soc_str = '';
					foreach ($soc_list as $sn=>$sl) {
						if (!empty($sl))
							$soc_str .= (!empty($soc_str) ? '|' : '') . ($sn) . '=' . ($sl);
					}
					if (!empty($soc_str)) {
						?><div class="socials_single_team"><?php if(function_exists('reisen_sc_socials')) reisen_show_layout(reisen_sc_socials(array('size'=>"tiny", 'shape'=>'round', 'socials'=>$soc_str))); ?></div><?php
					}
				}
				?>
			</section>

			<section class="single_team_post_description">
				<h2 class="team_title"><?php reisen_show_layout(wp_kses_data($post_data['post_title'])); ?></h2>
				<h6 class="team_position"><?php reisen_show_layout(wp_kses_data($post_meta['team_member_position'])); ?></h6>
				<div class="team_meta"><?php
					if (!empty($post_meta['team_member_email'])) {
						?><p><?php esc_attr_e('E-mail: ', 'reisen'); ?><?php reisen_show_layout(wp_kses_data($post_meta['team_member_email'])); ?></p><?php
					}
					if (!empty($post_meta['team_member_bday'])) {
						?><p><?php esc_attr_e('Date Of Birth: ', 'reisen'); ?><?php reisen_show_layout(wp_kses_data($post_meta['team_member_bday'])); ?></p><?php
					}
				?></div><?php

				if (!empty($post_meta['team_member_brief_info'])) {
					?>
					<div class="team_brief_info">
						<h5 class="team_brief_info_title"><?php esc_attr_e('Brief info', 'reisen'); ?></h5>
						<div class="team_brief_info_text"><?php echo wpautop($post_meta['team_member_brief_info']); ?></div>
					</div>
					<?php
				}
				?>
			</section>
			<?php
		}
		

		reisen_open_wrapper('<section class="post_content'.(!$post_data['post_protected'] && $post_data['post_edit_enable'] ? ' '.esc_attr('post_content_editor_present') : '').'" itemprop="articleBody">');
		
		if ($show_title && $post_options['location'] != 'center' && reisen_get_custom_option('show_page_title')=='no') {
			?>
			<<?php echo esc_attr($title_tag); ?> itemprop="name" class="post_title entry-title"><span class="post_icon <?php echo esc_attr($post_data['post_icon']); ?>"></span><?php reisen_show_layout(wp_kses_data($post_data['post_title'])); ?></<?php echo esc_attr($title_tag); ?>>
			<?php 
		}
			
		// Post content
		if ($post_data['post_protected']) { 
			reisen_show_layout($post_data['post_excerpt']);
			echo get_the_password_form(); 
		} else {
			reisen_show_layout(reisen_gap_wrapper(reisen_reviews_wrapper($post_data['post_content'])));
			wp_link_pages( array( 
				'before' => '<nav class="pagination_single"><span class="pager_pages">' . esc_html__( 'Pages:', 'reisen' ) . '</span>', 
				'after' => '</nav>',
				'link_before' => '<span class="pager_numbers">',
				'link_after' => '</span>'
				)
			); 
			if ( reisen_get_custom_option('show_post_tags') == 'yes' && !empty($post_data['post_terms'][$post_data['post_taxonomy_tags']]->terms_links)) {
				?>
				<div class="post_info post_info_bottom">
					<span class="post_info_item post_info_tags"><?php esc_html_e('Tags:', 'reisen'); ?> <?php echo join(', ', $post_data['post_terms'][$post_data['post_taxonomy_tags']]->terms_links); ?></span>
				</div>
				<?php 
			}
		} 

		// Prepare args for all rest template parts
		// This parts not pop args from storage!
		reisen_template_set_args('single-footer', array(
			'post_options' => $post_options,
			'post_data' => $post_data
		));
			
		if (!$post_data['post_protected'] && $post_data['post_edit_enable']) {
			get_template_part(reisen_get_file_slug('templates/_parts/editor-area.php'));
		}

		reisen_close_wrapper();
			
		if (!$post_data['post_protected']) {
			get_template_part(reisen_get_file_slug('templates/_parts/share.php'));
		}

		reisen_close_wrapper();

		if (!$post_data['post_protected']) {
			// Show replated posts
			get_template_part(reisen_get_file_slug('templates/_parts/related-posts.php'));
			// Show comments
			if ( comments_open() || get_comments_number() != 0 ) {
				comments_template();
			}
		}

		// Manually pop args from storage
		// after all single footer templates
		reisen_template_get_args('single-footer');
	}
}
?>