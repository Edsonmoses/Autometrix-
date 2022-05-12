<?php

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'reisen_template_single_portfolio_theme_setup' ) ) {
	add_action( 'reisen_action_before_init_theme', 'reisen_template_single_portfolio_theme_setup', 1 );
	function reisen_template_single_portfolio_theme_setup() {
		reisen_add_template(array(
			'layout' => 'single-portfolio',
			'mode'   => 'single',
			'need_content' => true,
			'need_terms' => true,
			'title'  => esc_html__('Portfolio item', 'reisen'),
			'thumb_title'  => esc_html__('Fullwidth image', 'reisen'),
			'w'		 => 1170,
			'h'		 => null,
			'h_crop' => 659
		));
	}
}

// Template output
if ( !function_exists( 'reisen_template_single_portfolio_output' ) ) {
	function reisen_template_single_portfolio_output($post_options, $post_data) {
		$post_data['post_views']++;
		$avg_author = 0;
		$avg_users  = 0;
		if (!$post_data['post_protected'] && $post_options['reviews'] && reisen_get_custom_option('show_reviews')=='yes') {
			$avg_author = $post_data['post_reviews_author'];
			$avg_users  = $post_data['post_reviews_users'];
		}
		$show_title = reisen_get_custom_option('show_post_title')=='yes' && (reisen_get_custom_option('show_post_title_on_quotes')=='yes' || !in_array($post_data['post_format'], array('aside', 'chat', 'status', 'link', 'quote')));

		reisen_open_wrapper('<article class="' 
				. join(' ', get_post_class('itemscope'
					. ' post_item post_item_single_portfolio'
					. ' post_featured_' . esc_attr($post_options['post_class'])
					. ' post_format_' . esc_attr($post_data['post_format'])))
				. '"'
				. ' itemscope itemtype="//schema.org/'.($avg_author > 0 || $avg_users > 0 ? 'Review' : 'Article')
				. '">');

		reisen_template_set_args('prev-next-block', array(
			'post_options' => $post_options,
			'post_data' => $post_data
		));
		get_template_part(reisen_get_file_slug('templates/_parts/prev-next-block.php'));

		reisen_open_wrapper('<section class="post_content'.(!$post_data['post_protected'] && $post_data['post_edit_enable'] ? ' '.esc_attr('post_content_editor_present') : '').'" itemprop="'.($avg_author > 0 || $avg_users > 0 ? 'reviewBody' : 'articleBody').'">');

		if ($show_title) {
			?>
			<h1 itemprop="<?php echo (float) $avg_author > 0 || (float) $avg_users > 0 ? 'itemReviewed' : 'headline'; ?>" class="post_title entry-title"><?php reisen_show_layout($post_data['post_title']); ?></h1>
			<?php
		}

		if (!$post_data['post_protected'] && reisen_get_custom_option('show_post_info')=='yes') {
			reisen_template_set_args('post-info', array(
				'post_options' => $post_options,
				'post_data' => $post_data
			));
			get_template_part(reisen_get_file_slug('templates/_parts/post-info.php'));
		}

		reisen_template_set_args('reviews-block', array(
			'post_options' => $post_options,
			'post_data' => $post_data,
			'avg_author' => $avg_author,
			'avg_users' => $avg_users
		));

        if (function_exists('reisen_reviews_theme_setup')) {
		    get_template_part(reisen_get_file_slug('templates/_parts/reviews-block.php'));
        }
		// Post content
		if ($post_data['post_protected']) { 
			reisen_show_layout($post_data['post_excerpt']);
			echo get_the_password_form(); 
		} else {
			if (!reisen_storage_empty('reviews_markup') && reisen_strpos($post_data['post_content'], reisen_get_reviews_placeholder())===false && function_exists('reisen_sc_reviews'))
				$post_data['post_content'] = reisen_sc_reviews(array()) . ($post_data['post_content']);
			reisen_show_layout(reisen_gap_wrapper(reisen_reviews_wrapper($post_data['post_content'])));
			wp_link_pages( array( 
				'before' => '<nav class="pagination_single"><span class="pager_pages">' . esc_html__( 'Pages:', 'reisen' ) . '</span>', 
				'after' => '</nav>',
				'link_before' => '<span class="pager_numbers">',
				'link_after' => '</span>'
				)
			); 
			if (reisen_get_custom_option('show_post_tags')=='yes' && !empty($post_data['post_terms'][$post_data['post_taxonomy_tags']]->terms_links)) {
				?>
				<div class="post_info">
					<span class="post_info_item post_info_tags"><?php esc_html_e('in', 'reisen'); ?> <?php echo join(', ', $post_data['post_terms'][$post_data['post_taxonomy_tags']]->terms_links); ?></span>
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
			// Author info
			get_template_part(reisen_get_file_slug('templates/_parts/author-info.php'));
			// Share buttons
			get_template_part(reisen_get_file_slug('templates/_parts/share.php'));
			// Show related posts
			get_template_part(reisen_get_file_slug('templates/_parts/related-posts.php'));
			// Show comments
			if ( comments_open() || get_comments_number() != 0 ) {
				comments_template();
			}
		}

		// Manually pop args from storage
		// after all single footer templates
		reisen_template_get_args('single-footer');
	
		reisen_close_wrapper();
	}
}
?>