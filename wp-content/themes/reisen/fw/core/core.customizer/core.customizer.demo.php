<div class="to_demo_wrap">
	<a href="" class="to_demo_pin iconadmin-pin" title="<?php esc_attr_e('Pin/Unpin demo-block by the right side of the window', 'reisen'); ?>"></a>
	<div class="to_demo_body_wrap">
		<div class="to_demo_body">
			<h1 class="to_demo_header"><?php echo esc_html__('Header with','reisen'); ?> <span class="to_demo_header_link"><?php echo esc_html__('inner link','reisen'); ?></span> <?php echo esc_html__('and it','reisen'); ?> <span class="to_demo_header_hover"><?php echo esc_html__('hovered state','reisen'); ?></span></h1>
			<p class="to_demo_info"><?php echo esc_html__('Posted','reisen'); ?> <span class="to_demo_info_link"><?php echo esc_html__('12 May, 2015','reisen'); ?></span> <?php echo esc_html__('by','reisen'); ?> <span class="to_demo_info_hover"><?php echo esc_html__('Author name hovered','reisen'); ?></span>.</p>
			<p class="to_demo_text"><?php echo esc_html__('This is default post content. Colors of each text element are set based on the color you choose below.','reisen'); ?></p>
			<p class="to_demo_text"><span class="to_demo_text_link"><?php echo esc_html__('link example','reisen'); ?></span> <?php echo esc_html__('and','reisen'); ?> <span class="to_demo_text_hover"><?php echo esc_html__('hovered link','reisen'); ?></span></p>

			<?php 
			$colors = reisen_storage_get('custom_colors');
			if (is_array($colors) && count($colors) > 0) {
				foreach ($colors as $slug=>$scheme) { 
					?>
					<h3 class="to_demo_header"><?php echo esc_html__('Accent colors','reisen'); ?></h3>
					<?php if (isset($scheme['text_hover'])) { ?>
						<div class="to_demo_columns3"><p class="to_demo_text"><span class="to_demo_text_hover"><?php echo esc_html__('text_hover example','reisen'); ?></span> <?php echo esc_html__('and','reisen'); ?> <span class="to_demo_text_hover_hover"><?php echo esc_html__('hovered text_hover','reisen'); ?></span></p></div>
					<?php } ?>
					<?php if (isset($scheme['accent2'])) { ?>
						<div class="to_demo_columns3"><p class="to_demo_text"><span class="to_demo_accent2"><?php echo esc_html__('accent2 example','reisen'); ?></span> <?php echo esc_html__('and','reisen'); ?> <span class="to_demo_accent2_hover"><?php echo esc_html__('hovered accent2','reisen'); ?></span></p></div>
					<?php } ?>
					<?php if (isset($scheme['accent3'])) { ?>
						<div class="to_demo_columns3"><p class="to_demo_text"><span class="to_demo_accent3"><?php echo esc_html__('accent3 example','reisen'); ?></span> <?php echo esc_html__('and','reisen'); ?> <span class="to_demo_accent3_hover"><?php echo esc_html__('hovered accent3','reisen'); ?></span></p></div>
					<?php } ?>
		
					<h3 class="to_demo_header"><?php echo esc_html__('Inverse colors (on accented backgrounds)','reisen'); ?></h3>
					<?php if (isset($scheme['text_hover'])) { ?>
						<div class="to_demo_columns3 to_demo_text_hover_bg to_demo_inverse_block">
							<h4 class="to_demo_text_hover_hover_bg to_demo_inverse_dark"><?php echo esc_html__('Accented block header','reisen'); ?></h4>
							<div>
								<p class="to_demo_inverse_light"><?php echo esc_html__('Posted','reisen'); ?> <span class="to_demo_inverse_link"><?php echo esc_html__('12 May, 2015','reisen'); ?></span> <?php echo esc_html__('by','reisen'); ?> <span class="to_demo_inverse_hover"><?php echo esc_html__('Author name hovered','reisen'); ?></span>.</p>
								<p class="to_demo_inverse_text"><?php echo esc_html__('This is a inversed colors example for the normal text','reisen'); ?></p>
								<p class="to_demo_inverse_text"><span class="to_demo_inverse_link"><?php echo esc_html__('link example','reisen'); ?></span> <?php echo esc_html__('and','reisen'); ?> <span class="to_demo_inverse_hover"><?php echo esc_html__('hovered link','reisen'); ?></span></p>
							</div>
						</div>
					<?php } ?>
					<?php if (isset($scheme['accent2'])) { ?>
						<div class="to_demo_columns3 to_demo_accent2_bg to_demo_inverse_block">
							<h4 class="to_demo_accent2_hover_bg to_demo_inverse_dark"><?php echo esc_html__('Accented block header','reisen'); ?></h4>
							<div">
								<p class="to_demo_inverse_light"><?php echo esc_html__('Posted','reisen'); ?> <span class="to_demo_inverse_link"><?php echo esc_html__('12 May, 2015','reisen'); ?></span> <?php echo esc_html__('by','reisen'); ?> <span class="to_demo_inverse_hover"><?php echo esc_html__('Author name hovered','reisen'); ?></span>.</p>
								<p class="to_demo_inverse_text"><?php echo esc_html__('This is a inversed colors example for the normal text','reisen'); ?></p>
								<p class="to_demo_inverse_text"><span class="to_demo_inverse_link"><?php echo esc_html__('link example','reisen'); ?></span> <?php echo esc_html__('and','reisen'); ?> <span class="to_demo_inverse_hover"><?php echo esc_html__('hovered link','reisen'); ?></span></p>
							</div>
						</div>
					<?php } ?>
					<?php if (isset($scheme['accent3'])) { ?>
						<div class="to_demo_columns3 to_demo_accent3_bg to_demo_inverse_block">
							<h4 class="to_demo_accent3_hover_bg to_demo_inverse_dark"><?php echo esc_html__('Accented block header','reisen'); ?></h4>
							<div>
								<p class="to_demo_inverse_light"><?php echo esc_html__('Posted','reisen'); ?> <span class="to_demo_inverse_link"><?php echo esc_html__('12 May, 2015','reisen'); ?></span> <?php echo esc_html__('by','reisen'); ?> <span class="to_demo_inverse_hover"><?php echo esc_html__('Author name hovered','reisen'); ?></span>.</p>
								<p class="to_demo_inverse_text"><?php echo esc_html__('This is a inversed colors example for the normal text','reisen'); ?></p>
								<p class="to_demo_inverse_text"><span class="to_demo_inverse_link"><?php echo esc_html__('link example','reisen'); ?></span> <?php echo esc_html__('and','reisen'); ?> <span class="to_demo_inverse_hover"><?php echo esc_html__('hovered link','reisen'); ?></span></p>
							</div>
						</div>
					<?php } ?>
					<?php 
					break;
				}
			}
			?>
	
			<h3 class="to_demo_header"><?php echo esc_html__('Alternative colors used to decorate highlight blocks and form fields','reisen'); ?></h3>
			<div class="to_demo_columns2">
				<div class="to_demo_alter_block">
					<h4 class="to_demo_alter_header"><?php echo esc_html__('Highlight block header','reisen'); ?></h4>
					<p class="to_demo_alter_text"><?php echo esc_html__('This is a plain text in the highlight block. This is a plain text in the highlight block.','reisen'); ?></p>
					<p class="to_demo_alter_text"><span class="to_demo_alter_link"><?php echo esc_html__('link example','reisen'); ?></span> <?php echo esc_html__('and','reisen'); ?> <span class="to_demo_alter_hover"><?php echo esc_html__('hovered link','reisen'); ?></span></p>
				</div>
			</div>
			<div class="to_demo_columns2">
				<div class="to_demo_form_fields">
					<h4 class="to_demo_header"><?php echo esc_html__('Form field','reisen'); ?></h4>
					<input type="text" class="to_demo_field" value="<?php echo esc_attr__('Input field example','reisen'); ?>">
					<h4 class="to_demo_header"><?php echo esc_html__('Form field focused','reisen'); ?></h4>
					<input type="text" class="to_demo_field_focused" value="<?php echo esc_attr__('Focused field example','reisen'); ?>">
				</div>
			</div>
		</div>
	</div>
</div>
