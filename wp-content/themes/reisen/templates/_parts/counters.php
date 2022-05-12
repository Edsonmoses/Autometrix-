<?php
// Get template args
extract(reisen_template_get_args('counters'));

$show_all_counters = !empty($post_options['counters']);
$counters_tag = 'a';

// Views
if ($show_all_counters && reisen_strpos($post_options['counters'], 'views')!==false && function_exists('trx_utils_get_post_views')) {
	?>
	<<?php reisen_show_layout($counters_tag); ?> class="post_counters_item post_counters_views icon-eye-1" title="<?php echo esc_attr( sprintf(__('Views - %s', 'reisen'), $post_data['post_views']) ); ?>" href="<?php echo esc_url($post_data['post_link']); ?>"><span class="post_counters_number"><?php reisen_show_layout($post_data['post_views']); ?></span><?php if (reisen_strpos($post_options['counters'], 'captions')!==false) echo ' '.esc_html__('Views', 'reisen'); ?></<?php reisen_show_layout($counters_tag); ?>>
	<?php
}

// Comments
if ($show_all_counters && reisen_strpos($post_options['counters'], 'comments')!==false) {
	?>
	<a class="post_counters_item post_counters_comments icon-commenting" title="<?php echo esc_attr( sprintf(__('Comments - %s', 'reisen'), $post_data['post_comments']) ); ?>" href="<?php echo esc_url($post_data['post_comments_link']); ?>"><span class="post_counters_number"><?php reisen_show_layout($post_data['post_comments']); ?></span><?php if (reisen_strpos($post_options['counters'], 'captions')!==false) echo ' '.esc_html__('Comments', 'reisen'); ?></a>
	<?php 
}
 
// Rating
$rating = $post_data['post_reviews_'.(reisen_get_theme_option('reviews_first')=='author' ? 'author' : 'users')];
if ($rating > 0 && ($show_all_counters || reisen_strpos($post_options['counters'], 'rating')!==false)) { 
	?>
	<<?php reisen_show_layout($counters_tag); ?> class="post_counters_item post_counters_rating icon-star" title="<?php echo esc_attr( sprintf(__('Rating - %s', 'reisen'), $rating) ); ?>" href="<?php echo esc_url($post_data['post_link']); ?>"><span class="post_counters_number"><?php reisen_show_layout($rating); ?></span></<?php reisen_show_layout($counters_tag); ?>>
	<?php
}

// Likes
if ($show_all_counters && reisen_strpos($post_options['counters'], 'likes')!==false && function_exists('trx_utils_get_post_likes')) {
	// Load core messages
	reisen_enqueue_messages();
	$likes = isset($_COOKIE['reisen_likes']) ? reisen_get_value_gpc('reisen_likes') : '';
	$allow = reisen_strpos($likes, ','.($post_data['post_id']).',')===false;
	?>
	<a class="post_counters_item post_counters_likes icon-heart <?php echo !empty($allow) ? 'enabled' : 'disabled'; ?>" title="<?php echo !empty($allow) ? esc_attr__('Like', 'reisen') : esc_attr__('Dislike', 'reisen'); ?>" href="#"
		data-postid="<?php echo esc_attr($post_data['post_id']); ?>"
		data-likes="<?php echo esc_attr($post_data['post_likes']); ?>"
		data-title-like="<?php esc_attr_e('Like', 'reisen'); ?>"
		data-title-dislike="<?php esc_attr_e('Dislike', 'reisen'); ?>"><span class="post_counters_number"><?php reisen_show_layout($post_data['post_likes']); ?></span><?php if (reisen_strpos($post_options['counters'], 'captions')!==false) echo ' '.esc_html__('Likes', 'reisen'); ?></a>
	<?php
}

// Edit page link
if (reisen_strpos($post_options['counters'], 'edit')!==false) {
	edit_post_link( esc_html__( 'Edit', 'reisen' ), '<span class="post_edit edit-link">', '</span>' );
}

// Markup for search engines
if (is_single() && reisen_strpos($post_options['counters'], 'markup')!==false) {
	?>
	<meta itemprop="interactionCount" content="User<?php echo esc_attr(reisen_strpos($post_options['counters'],'comments')!==false ? 'Comments' : 'PageVisits'); ?>:<?php echo esc_attr(reisen_strpos($post_options['counters'], 'comments')!==false ? $post_data['post_comments'] : $post_data['post_views']); ?>" />
	<?php
}
?>