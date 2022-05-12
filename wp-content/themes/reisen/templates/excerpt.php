<?php

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'reisen_template_excerpt_theme_setup' ) ) {
	add_action( 'reisen_action_before_init_theme', 'reisen_template_excerpt_theme_setup', 1 );
	function reisen_template_excerpt_theme_setup() {
		reisen_add_template(array(
			'layout' => 'excerpt',
			'mode'   => 'blog',
			'title'  => esc_html__('Excerpt', 'reisen'),
			'thumb_title'  => esc_html__('Large image (crop)', 'reisen'),
			'w'		 => '',
			'h'		 => ''
		));
	}
}

// Template output
if ( !function_exists( 'reisen_template_excerpt_output' ) ) {
	function reisen_template_excerpt_output($post_options, $post_data) {
		$show_title = true;
		$tag = reisen_in_shortcode_blogger(true) ? 'div' : 'article';
		?>
		<<?php reisen_show_layout($tag); ?> <?php post_class('post_item post_item_excerpt post_featured_' . esc_attr($post_options['post_class']) . ' post_format_'.esc_attr($post_data['post_format']) . ($post_options['number']%2==0 ? ' even' : ' odd') . ($post_options['number']==0 ? ' first' : '') . ($post_options['number']==$post_options['posts_on_page']? ' last' : '') . ($post_options['add_view_more'] ? ' viewmore' : '')); ?>>
			<?php
			if ($post_data['post_flags']['sticky']) {
				?><span class="sticky_label"></span><?php
			}

			if ($show_title && !empty($post_data['post_title'])) {
				?><h3 class="post_title"><a href="<?php echo esc_url($post_data['post_link']); ?>"><?php reisen_show_layout(wp_kses_data($post_data['post_title'])); ?></a></h3><?php
			}
			
			if (!$post_data['post_protected'] && (!empty($post_options['dedicated']) || $post_data['post_thumb'] || $post_data['post_gallery'] || $post_data['post_video'] || $post_data['post_audio'])) {
				?>
				<div class="post_featured">
				<?php
				if (!empty($post_options['dedicated'])) {
					reisen_show_layout($post_options['dedicated']);
				} else if ($post_data['post_thumb'] || $post_data['post_gallery'] || $post_data['post_video'] || $post_data['post_audio']) {
					reisen_template_set_args('post-featured', array(
						'post_options' => $post_options,
						'post_data' => $post_data
					));
					get_template_part(reisen_get_file_slug('templates/_parts/post-featured.php'));
				}
				?>
				</div>
			<?php
			}
			?>
	
			<div class="post_content clearfix">
				<?php

				if (!$post_data['post_protected'] && $post_options['info']) {
					reisen_template_set_args('post-info', array(
						'post_options' => $post_options,
						'post_data' => $post_data
					));
					get_template_part(reisen_get_file_slug('templates/_parts/post-info.php')); 
				}
				?>
		
				<div class="post_descr">
				<?php
					if ($post_data['post_protected']) {
						reisen_show_layout($post_data['post_excerpt']);
					} else {
						if ($post_data['post_excerpt']) {
							echo in_array($post_data['post_format'], array('quote', 'link', 'chat', 'aside', 'status')) ? $post_data['post_excerpt'] : '<p>'.trim(reisen_strshort($post_data['post_excerpt'], isset($post_options['descr']) ? $post_options['descr'] : reisen_get_custom_option('post_excerpt_maxlength'))).'</p>';
						}
					}
					if (empty($post_options['readmore'])) $post_options['readmore'] = esc_html__('Read more', 'reisen');
					if (!reisen_param_is_off($post_options['readmore']) && !in_array($post_data['post_format'], array('quote', 'link', 'chat', 'aside', 'status'))) {
                        if (function_exists('reisen_sc_button')) reisen_show_layout(reisen_sc_button(array('link'=>$post_data['post_link'], 'style'=>'filled_dark', 'size'=>'large'), $post_options['readmore']));
					}
				?>
				</div>

			</div>	<!-- /.post_content -->

		</<?php reisen_show_layout($tag); ?>>	<!-- /.post_item -->

	<?php
	}
}
?>