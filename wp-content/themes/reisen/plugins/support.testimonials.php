<?php
/**
 * Reisen Framework: Testimonial support
 *
 * @package	reisen
 * @since	reisen 1.0
 */

// Theme init
if (!function_exists('reisen_testimonial_theme_setup')) {
	add_action( 'reisen_action_before_init_theme', 'reisen_testimonial_theme_setup', 1 );
	function reisen_testimonial_theme_setup() {
	
		// Add item in the admin menu
		add_filter('trx_utils_filter_override_options',		'reisen_testimonial_add_override_options');

		// Save data from override options
		add_action('save_post',				'reisen_testimonial_save_data');

		// Options fields
		reisen_storage_set('testimonial_override_options', array(
			'id' => 'testimonial-override-options',
			'title' => esc_html__('Testimonial Details', 'reisen'),
			'page' => 'testimonial',
			'context' => 'normal',
			'priority' => 'high',
			'fields' => array(
				"testimonial_author" => array(
					"title" => esc_html__('Testimonial author',  'reisen'),
					"desc" => wp_kses_data( __("Name of the testimonial's author", 'reisen') ),
					"class" => "testimonial_author",
					"std" => "",
					"type" => "text"),
				"testimonial_position" => array(
					"title" => esc_html__("Author's position",  'reisen'),
					"desc" => wp_kses_data( __("Position of the testimonial's author", 'reisen') ),
					"class" => "testimonial_author",
					"std" => "",
					"type" => "text"),
				"testimonial_email" => array(
					"title" => esc_html__("Author's e-mail",  'reisen'),
					"desc" => wp_kses_data( __("E-mail of the testimonial's author - need to take Gravatar (if registered)", 'reisen') ),
					"class" => "testimonial_email",
					"std" => "",
					"type" => "text"),
				"testimonial_link" => array(
					"title" => esc_html__('Testimonial link',  'reisen'),
					"desc" => wp_kses_data( __("URL of the testimonial source or author profile page", 'reisen') ),
					"class" => "testimonial_link",
					"std" => "",
					"type" => "text")
				)
			)
		);
		
		// Add supported data types
		reisen_theme_support_pt('testimonial');
		reisen_theme_support_tx('testimonial_group');
		
	}
}


// Add override options
if (!function_exists('reisen_testimonial_add_override_options')) {
	//Handler of add_filter('trx_utils_filter_override_options', 'reisen_testimonial_add_override_options');
	function reisen_testimonial_add_override_options() {
        $boxes[] = array_merge(reisen_storage_get('testimonial_override_options'), array('callback' => 'reisen_testimonial_show_override_options'));
        return $boxes;
	}
}

// Callback function to show fields in override options
if (!function_exists('reisen_testimonial_show_override_options')) {
	function reisen_testimonial_show_override_options() {
		global $post;

		// Use nonce for verification
		echo '<input type="hidden" name="override_options_testimonial_nonce" value="'.esc_attr(wp_create_nonce(admin_url())).'" />';
		
		$data = get_post_meta($post->ID, reisen_storage_get('options_prefix').'_testimonial_data', true);
	
		$fields = reisen_storage_get_array('testimonial_override_options', 'fields');
		?>
		<table class="testimonial_area">
		<?php
		if (is_array($fields) && count($fields) > 0) {
			foreach ($fields as $id=>$field) { 
				$meta = isset($data[$id]) ? $data[$id] : '';
				?>
				<tr class="testimonial_field <?php echo esc_attr($field['class']); ?>" valign="top">
					<td><label for="<?php echo esc_attr($id); ?>"><?php echo esc_html($field['title']); ?></label></td>
					<td><input type="text" name="<?php echo esc_attr($id); ?>" id="<?php echo esc_attr($id); ?>" value="<?php echo esc_attr($meta); ?>" size="30" />
						<br><small><?php echo esc_html($field['desc']); ?></small></td>
				</tr>
				<?php
			}
		}
		?>
		</table>
		<?php
	}
}


// Save data from override options
if (!function_exists('reisen_testimonial_save_data')) {
	//Handler of add_action('save_post', 'reisen_testimonial_save_data');
	function reisen_testimonial_save_data($post_id) {
		// verify nonce
		if ( !wp_verify_nonce( reisen_get_value_gp('override_options_testimonial_nonce'), admin_url() ) )
			return $post_id;

		// check autosave
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return $post_id;
		}

		// check permissions
		if ($_POST['post_type']!='testimonial' || !current_user_can('edit_post', $post_id)) {
			return $post_id;
		}

		$data = array();

		$fields = reisen_storage_get_array('testimonial_override_options', 'fields');

		// Post type specific data handling
		if (is_array($fields) && count($fields) > 0) {
			foreach ($fields as $id=>$field) { 
				if (isset($_POST[$id])) 
					$data[$id] = reisen_get_value_gp($id);
			}
		}

		update_post_meta($post_id, reisen_storage_get('options_prefix').'_testimonial_data', $data);
	}
}
?>