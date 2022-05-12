<?php
/**
 * The template for displaying the footer.
 */

				reisen_close_wrapper();	// <!-- </.content> -->

				// Show main sidebar
				get_sidebar();

				if (reisen_get_custom_option('body_style')!='fullscreen') reisen_close_wrapper();	// <!-- </.content_wrap> -->
				?>
			
			</div>		<!-- </.page_content_wrap> -->
			
			<?php
            // Google map
            if ( reisen_get_custom_option('show_googlemap')=='yes' && function_exists('reisen_sc_googlemap')) {
                $map_address = reisen_get_custom_option('googlemap_address');
                $map_latlng  = reisen_get_custom_option('googlemap_latlng');
                $map_zoom    = reisen_get_custom_option('googlemap_zoom');
                $map_style   = reisen_get_custom_option('googlemap_style');
                $map_height  = reisen_get_custom_option('googlemap_height');
                if (!empty($map_address) || !empty($map_latlng)) {
                    $args = array();
                    if (!empty($map_style))		$args['style'] = esc_attr($map_style);
                    if (!empty($map_zoom))		$args['zoom'] = esc_attr($map_zoom);
                    if (!empty($map_height))	$args['height'] = esc_attr($map_height);
                    reisen_show_layout(reisen_sc_googlemap($args));
                }
            }

			// Footer sidebar
			$footer_show  = reisen_get_custom_option('show_sidebar_footer');
			$sidebar_name = reisen_get_custom_option('sidebar_footer');
			if (!reisen_param_is_off($footer_show) && is_active_sidebar($sidebar_name)) { 
				reisen_storage_set('current_sidebar', 'footer');
				?>
				<footer class="footer_wrap widget_area scheme_<?php echo esc_attr(reisen_get_custom_option('sidebar_footer_scheme')); ?>">
					<div class="footer_wrap_inner widget_area_inner">
						<div class="content_wrap">
							<div class="columns_wrap"><?php
							ob_start();
							do_action( 'before_sidebar' );
                                if ( is_active_sidebar( $sidebar_name ) ) {
                                    dynamic_sidebar( $sidebar_name );
                                }
							do_action( 'after_sidebar' );
							$out = ob_get_contents();
							ob_end_clean();
							reisen_show_layout(trim(preg_replace("/<\/aside>[\r\n\s]*<aside/", "</aside><aside", $out)));
							?></div>	<!-- /.columns_wrap -->
						</div>	<!-- /.content_wrap -->
					</div>	<!-- /.footer_wrap_inner -->
				</footer>	<!-- /.footer_wrap -->
				<?php
			}


			// Copyright area
			$copyright_style = reisen_get_custom_option('show_copyright_in_footer');
			if (!reisen_param_is_off($copyright_style)) {
				?> 
				<div class="copyright_wrap copyright_style_<?php echo esc_attr($copyright_style); ?>  scheme_<?php echo esc_attr(reisen_get_custom_option('copyright_scheme')); ?>">
					<div class="copyright_wrap_inner">
						<div class="content_wrap">
							<?php
							if ($copyright_style == 'menu') {
								if (($menu = reisen_get_nav_menu('menu_footer'))!='') {
									reisen_show_layout($menu);
								}
							} else if ($copyright_style == 'socials' && function_exists('reisen_sc_socials')) {
								reisen_show_layout(reisen_sc_socials(array('size'=>"tiny")));
							}
							?>
							<div class="copyright_text"><?php
                                $reisen_copyright = reisen_get_custom_option('footer_copyright');
                                $reisen_copyright = str_replace(array('{{Y}}', '{Y}'), date('Y'), $reisen_copyright);
                                echo wp_kses_post($reisen_copyright); ?></div>
						</div>
					</div>
				</div>
				<?php
			}
			?>
			
		</div>	<!-- /.page_wrap -->

	</div>		<!-- /.body_wrap -->
	
	<?php if ( !reisen_param_is_off(reisen_get_custom_option('show_sidebar_outer')) ) { ?>
	</div>	<!-- /.outer_wrap -->
	<?php } ?>

	<?php wp_footer(); ?>

</body>
</html>