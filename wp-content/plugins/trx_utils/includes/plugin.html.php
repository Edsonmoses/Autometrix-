<?php
/**
 * HTML manipulations
 *
 * @package WordPress
 * @subpackage ThemeREX Utilities
 * @since v3.0
 */

// Don't load directly
if ( ! defined( 'TRX_UTILS_VERSION' ) ) {
	die( '-1' );
}


/* URL utilities
-------------------------------------------------------------------------------- */

// Return internal page link - if is customize mode - full url else only hash part
if (!function_exists('trx_utils_get_hash_link')) {
	function trx_utils_get_hash_link($hash) {
		if (strpos($hash, 'http')!==0) {
			if ($hash[0]!='#') $hash = '#'.$hash;
			if (is_customize_preview()) $hash = trx_utils_get_protocol().'://' . ($_SERVER["HTTP_HOST"]) . ($_SERVER["REQUEST_URI"]) . $hash;
		}
		return $hash;
	}
}

// Return current site protocol
if (!function_exists('trx_utils_get_protocol')) {
	function trx_utils_get_protocol() {
		return is_ssl() ? 'https' : 'http';
	}
}

// Add parameters to URL
if (!function_exists('trx_utils_add_to_url')) {
	function trx_utils_add_to_url($url, $prm) {
		if (is_array($prm) && count($prm) > 0) {
			$separator = strpos($url, '?')===false ? '?' : '&';
			foreach ($prm as $k=>$v) {
				$url .= $separator . urlencode($k) . '=' . urlencode($v);
				$separator = '&';
			}
		}
		return $url;
	}
}

// Set e-mail content type
// Call add_filter( 'wp_mail_content_type', 'trx_utils_set_html_content_type' ) before send mail
// and  remove_filter( 'wp_mail_content_type', 'trx_utils_set_html_content_type' ) after send mail
if (!function_exists('trx_utils_set_html_content_type')) {
	function trx_utils_set_html_content_type() {
		return 'text/html';
	}
}



/* GET, POST and SESSION utilities
-------------------------------------------------------------------------------- */

// Return GET or POST value
if (!function_exists('trx_utils_get_value_gp')) {
	function trx_utils_get_value_gp($name, $defa='') {
		$rez = $defa;
		$magic = function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc() == 1;
		if (isset($_GET[$name])) {
			$rez = $magic ? stripslashes(trim($_GET[$name])) : trim($_GET[$name]);
		} else if (isset($_POST[$name])) {
			$rez = $magic ? stripslashes(trim($_POST[$name])) : trim($_POST[$name]);
		}
		return $rez;
	}
}

// Return GET or POST or COOKIE value
if (!function_exists('trx_utils_get_value_gpc')) {
	function trx_utils_get_value_gpc($name, $defa='') {
		$rez = $defa;
		$magic = function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc() == 1;
		if (isset($_GET[$name])) {
			$rez = $magic ? stripslashes(trim($_GET[$name])) : trim($_GET[$name]);
		} else if (isset($_POST[$name])) {
			$rez = $magic ? stripslashes(trim($_POST[$name])) : trim($_POST[$name]);
		} else if (isset($_COOKIE[$name])) {
			$rez = $magic ? stripslashes(trim($_COOKIE[$name])) : trim($_COOKIE[$name]);
		}
		return $rez;
	}
}


// Get GET, POST, SESSION value and save it (if need)
if (!function_exists('trx_utils_get_value_gps')) {
	function trx_utils_get_value_gps($name, $defa='') {
		$rez = $defa;
		if (isset($_GET[$name])) {
			$rez = stripslashes(trim($_GET[$name]));
		} else if (isset($_POST[$name])) {
			$rez = stripslashes(trim($_POST[$name]));
		} else if (isset($_SESSION[$name])) {
			$rez = stripslashes(trim($_SESSION[$name]));
		}
		return $rez;
	}
}

// Save value into session
if (!function_exists('trx_utils_set_session_value')) {
	function trx_utils_set_session_value($name, $value) {
		if (!session_id()) session_start();
		$_SESSION[$name] = $value;
	}
}

// Delete value from session
if (!function_exists('trx_utils_del_session_value')) {
	function trx_utils_del_session_value($name) {
		if (!session_id()) session_start();
		unset($_SESSION[$name]);
	}
}


// Show content with the html layout (if not empty)
if ( !function_exists('trx_utils_show_layout') ) {
	function trx_utils_show_layout($str, $before='', $after='') {
		if (!empty($str)) {
			printf("%s%s%s", $before, $str, $after);
		}
	}
}

//Return Post Views Count
if (!function_exists('trx_utils_get_post_views')) {
    add_filter('trx_utils_filter_get_post_views', 'trx_utils_get_post_views', 10, 2);
    function trx_utils_get_post_views($default=0, $id=0){
        global $wp_query;
        if (!$id) $id = $wp_query->current_post>=0 ? get_the_ID() : $wp_query->post->ID;
        $count_key = reisen_storage_get('options_prefix').'_post_views_count';
        $count = get_post_meta($id, $count_key, true);
        if ($count===''){
            delete_post_meta($id, $count_key);
            add_post_meta($id, $count_key, '0');
            $count = 0;
        }
        return $count;
    }
}

//Set Post Views Count
if (!function_exists('trx_utils_set_post_views')) {
    add_action('trx_utils_filter_set_post_views', 'trx_utils_set_post_views', 10, 2);
    function trx_utils_set_post_views($id=0, $counter=-1) {
        global $wp_query;
        if (!$id) $id = $wp_query->current_post>=0 ? get_the_ID() : $wp_query->post->ID;
        $count_key = reisen_storage_get('options_prefix').'_post_views_count';
        $count = get_post_meta($id, $count_key, true);
        if ($count===''){
            delete_post_meta($id, $count_key);
            add_post_meta($id, $count_key, 1);
        } else {
            $count = $counter >= 0 ? $counter : $count+1;
            update_post_meta($id, $count_key, $count);
        }
    }
}

//Return Post Likes Count
if (!function_exists('trx_utils_get_post_likes')) {
    add_filter('trx_utils_filter_get_post_likes', 'trx_utils_get_post_likes', 10, 2);
    function trx_utils_get_post_likes($default=0, $id=0){
        global $wp_query;
        if (!$id) $id = $wp_query->current_post>=0 ? get_the_ID() : $wp_query->post->ID;
        $count_key = reisen_storage_get('options_prefix').'_post_likes_count';
        $count = get_post_meta($id, $count_key, true);
        if ($count===''){
            delete_post_meta($id, $count_key);
            add_post_meta($id, $count_key, '0');
            $count = 0;
        }
        return $count;
    }
}

//Set Post Likes Count
if (!function_exists('trx_utils_set_post_likes')) {
    add_action('trx_utils_filter_set_post_likes', 'trx_utils_set_post_likes', 10, 2);
    function trx_utils_set_post_likes($id=0, $count=0) {
        global $wp_query;
        if (!$id) $id = $wp_query->current_post>=0 ? get_the_ID() : $wp_query->post->ID;
        $count_key = reisen_storage_get('options_prefix').'_post_likes_count';
        update_post_meta($id, $count_key, $count);
    }
}

// AJAX: Set post likes/views count
// Handler of add_action('wp_ajax_post_counter', 			'trx_utils_callback_post_counter');
// Handler of add_action('wp_ajax_nopriv_post_counter',	'trx_utils_callback_post_counter');
if ( !function_exists( 'trx_utils_callback_post_counter' ) ) {
    function trx_utils_callback_post_counter() {

        if ( !wp_verify_nonce( trx_utils_get_value_gp('nonce'), admin_url('admin-ajax.php') ) )
            wp_die();

        $response = array('error'=>'');

        $id = (int) trx_utils_get_value_gp('post_id');
        if (isset($_REQUEST['likes'])) {
            $counter = max(0, (int) trx_utils_get_value_gp('likes'));
            trx_utils_set_post_likes($id, $counter);
        } else if (isset($_REQUEST['views'])) {
            $counter = max(0, (int) trx_utils_get_value_gp('views'));
            trx_utils_set_post_views($id, $counter);
        }
        echo json_encode($response);
        wp_die();
    }
}

// Get theme variable
if (!function_exists('trx_utils_storage_get')) {
    function trx_utils_storage_get($var_name, $default='') {
        global $REISEN_STORAGE;
        return isset($REISEN_STORAGE[$var_name]) ? $REISEN_STORAGE[$var_name] : $default;
    }
}
?>