<?php

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'reisen_template_no_search_theme_setup' ) ) {
	add_action( 'reisen_action_before_init_theme', 'reisen_template_no_search_theme_setup', 1 );
	function reisen_template_no_search_theme_setup() {
		reisen_add_template(array(
			'layout' => 'no-search',
			'mode'   => 'internal',
			'title'  => esc_html__('No search results found', 'reisen')
		));
	}
}

// Template output
if ( !function_exists( 'reisen_template_no_search_output' ) ) {
	function reisen_template_no_search_output($post_options, $post_data) {
		?>
		<article class="post_item">
			<div class="post_content">
				<p><?php esc_html_e( 'Sorry, but nothing matched your search criteria. Please try again with some different keywords.', 'reisen' ); ?></p>
				<p><?php echo wp_kses_data( sprintf(__('Go back, or return to <a href="%s">%s</a> home page to choose a new page.', 'reisen'), esc_url(home_url('/')), get_bloginfo()) ); ?>
				<br><?php esc_html_e('Please report any broken links to our team.', 'reisen'); ?></p>
				<?php if(function_exists('reisen_sc_search')) reisen_show_layout(reisen_sc_search(array('state'=>"fixed"))); ?>
			</div>	<!-- /.post_content -->
		</article>	<!-- /.post_item -->
		<?php
	}
}
?>