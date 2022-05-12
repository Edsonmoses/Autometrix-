<?php
/**
 * Single post
 */
get_header(); 

$single_style = reisen_storage_get('single_style');
if (empty($single_style)) $single_style = reisen_get_custom_option('single_style');

while ( have_posts() ) { the_post();
	reisen_show_post_layout(
		array(
			'layout' => $single_style,
			'sidebar' => !reisen_param_is_off(reisen_get_custom_option('show_sidebar_main')),
			'content' => reisen_get_template_property($single_style, 'need_content'),
			'terms_list' => reisen_get_template_property($single_style, 'need_terms')
		)
	);
}

get_footer();
?>