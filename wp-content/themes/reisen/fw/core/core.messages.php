<?php
/**
 * Reisen Framework: messages subsystem
 *
 * @package	reisen
 * @since	reisen 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Theme init
if (!function_exists('reisen_messages_theme_setup')) {
	add_action( 'reisen_action_before_init_theme', 'reisen_messages_theme_setup' );
	function reisen_messages_theme_setup() {
		// Core messages strings
		add_filter('reisen_filter_localize_script', 'reisen_messages_localize_script');
	}
}


/* Session messages
------------------------------------------------------------------------------------- */

if (!function_exists('reisen_get_error_msg')) {
	function reisen_get_error_msg() {
		return reisen_storage_get('error_msg');
	}
}

if (!function_exists('reisen_set_error_msg')) {
	function reisen_set_error_msg($msg) {
		$msg2 = reisen_get_error_msg();
		reisen_storage_set('error_msg', trim($msg2) . ($msg2=='' ? '' : '<br />') . trim($msg));
	}
}

if (!function_exists('reisen_get_success_msg')) {
	function reisen_get_success_msg() {
		return reisen_storage_get('success_msg');
	}
}

if (!function_exists('reisen_set_success_msg')) {
	function reisen_set_success_msg($msg) {
		$msg2 = reisen_get_success_msg();
		reisen_storage_set('success_msg', trim($msg2) . ($msg2=='' ? '' : '<br />') . trim($msg));
	}
}

if (!function_exists('reisen_get_notice_msg')) {
	function reisen_get_notice_msg() {
		return reisen_storage_get('notice_msg');
	}
}

if (!function_exists('reisen_set_notice_msg')) {
	function reisen_set_notice_msg($msg) {
		$msg2 = reisen_get_notice_msg();
		reisen_storage_set('notice_msg', trim($msg2) . ($msg2=='' ? '' : '<br />') . trim($msg));
	}
}


/* System messages (save when page reload)
------------------------------------------------------------------------------------- */
if (!function_exists('reisen_set_system_message')) {
	function reisen_set_system_message($msg, $status='info', $hdr='') {
		update_option(reisen_storage_get('options_prefix') . '_message', array('message' => $msg, 'status' => $status, 'header' => $hdr));
	}
}

if (!function_exists('reisen_get_system_message')) {
	function reisen_get_system_message($del=false) {
		$msg = get_option(reisen_storage_get('options_prefix') . '_message', false);
		if (!$msg)
			$msg = array('message' => '', 'status' => '', 'header' => '');
		else if ($del)
			reisen_del_system_message();
		return $msg;
	}
}

if (!function_exists('reisen_del_system_message')) {
	function reisen_del_system_message() {
		delete_option(reisen_storage_get('options_prefix') . '_message');
	}
}


/* Messages strings
------------------------------------------------------------------------------------- */

if (!function_exists('reisen_messages_localize_script')) {
	//Handler of add_filter('reisen_filter_localize_script', 'reisen_messages_localize_script');
	function reisen_messages_localize_script($vars) {
		$vars['strings'] = array(
			'ajax_error'		=> esc_html__('Invalid server answer', 'reisen'),
			'bookmark_add'		=> esc_html__('Add the bookmark', 'reisen'),
            'bookmark_added'	=> esc_html__('Current page has been successfully added to the bookmarks. You can see it in the right panel on the tab \'Bookmarks\'', 'reisen'),
            'bookmark_del'		=> esc_html__('Delete this bookmark', 'reisen'),
            'bookmark_title'	=> esc_html__('Enter bookmark title', 'reisen'),
            'bookmark_exists'	=> esc_html__('Current page already exists in the bookmarks list', 'reisen'),
			'search_error'		=> esc_html__('Error occurs in AJAX search! Please, type your query and press search icon for the traditional search way.', 'reisen'),
			'email_confirm'		=> esc_html__('On the e-mail address "%s" we sent a confirmation email. Please, open it and click on the link.', 'reisen'),
			'reviews_vote'		=> esc_html__('Thanks for your vote! New average rating is:', 'reisen'),
			'reviews_error'		=> esc_html__('Error saving your vote! Please, try again later.', 'reisen'),
			'error_like'		=> esc_html__('Error saving your like! Please, try again later.', 'reisen'),
			'error_global'		=> esc_html__('Global error text', 'reisen'),
			'name_empty'		=> esc_html__('The name can\'t be empty', 'reisen'),
			'name_long'			=> esc_html__('Too long name', 'reisen'),
			'email_empty'		=> esc_html__('Too short (or empty) email address', 'reisen'),
			'email_long'		=> esc_html__('Too long email address', 'reisen'),
			'email_not_valid'	=> esc_html__('Invalid email address', 'reisen'),
			'subject_empty'		=> esc_html__('The subject can\'t be empty', 'reisen'),
			'subject_long'		=> esc_html__('Too long subject', 'reisen'),
			'text_empty'		=> esc_html__('The message text can\'t be empty', 'reisen'),
			'text_long'			=> esc_html__('Too long message text', 'reisen'),
			'send_complete'		=> esc_html__("Send message complete!", 'reisen'),
			'send_error'		=> esc_html__('Transmit failed!', 'reisen'),
			'geocode_error'			=> esc_html__('Geocode was not successful for the following reason:', 'reisen'),
			'googlemap_not_avail'	=> esc_html__('Google map API not available!', 'reisen'),
			'editor_save_success'	=> esc_html__("Post content saved!", 'reisen'),
			'editor_save_error'		=> esc_html__("Error saving post data!", 'reisen'),
			'editor_delete_post'	=> esc_html__("You really want to delete the current post?", 'reisen'),
			'editor_delete_post_header'	=> esc_html__("Delete post", 'reisen'),
			'editor_delete_success'	=> esc_html__("Post deleted!", 'reisen'),
			'editor_delete_error'	=> esc_html__("Error deleting post!", 'reisen'),
			'editor_caption_cancel'	=> esc_html__('Cancel', 'reisen'),
			'editor_caption_close'	=> esc_html__('Close', 'reisen')
			);
		return $vars;
	}
}
?>