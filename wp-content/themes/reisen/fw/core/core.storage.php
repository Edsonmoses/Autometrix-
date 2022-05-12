<?php
/**
 * Reisen Framework: theme variables storage
 *
 * @package	reisen
 * @since	reisen 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Get theme variable
if (!function_exists('reisen_storage_get')) {
	function reisen_storage_get($var_name, $default='') {
		global $REISEN_STORAGE;
		return isset($REISEN_STORAGE[$var_name]) ? $REISEN_STORAGE[$var_name] : $default;
	}
}

// Set theme variable
if (!function_exists('reisen_storage_set')) {
	function reisen_storage_set($var_name, $value) {
		global $REISEN_STORAGE;
		$REISEN_STORAGE[$var_name] = $value;
	}
}

// Check if theme variable is empty
if (!function_exists('reisen_storage_empty')) {
	function reisen_storage_empty($var_name, $key='', $key2='') {
		global $REISEN_STORAGE;
		if (!empty($key) && !empty($key2))
			return empty($REISEN_STORAGE[$var_name][$key][$key2]);
		else if (!empty($key))
			return empty($REISEN_STORAGE[$var_name][$key]);
		else
			return empty($REISEN_STORAGE[$var_name]);
	}
}

// Check if theme variable is set
if (!function_exists('reisen_storage_isset')) {
	function reisen_storage_isset($var_name, $key='', $key2='') {
		global $REISEN_STORAGE;
		if (!empty($key) && !empty($key2))
			return isset($REISEN_STORAGE[$var_name][$key][$key2]);
		else if (!empty($key))
			return isset($REISEN_STORAGE[$var_name][$key]);
		else
			return isset($REISEN_STORAGE[$var_name]);
	}
}

// Inc/Dec theme variable with specified value
if (!function_exists('reisen_storage_inc')) {
	function reisen_storage_inc($var_name, $value=1) {
		global $REISEN_STORAGE;
		if (empty($REISEN_STORAGE[$var_name])) $REISEN_STORAGE[$var_name] = 0;
		$REISEN_STORAGE[$var_name] += $value;
	}
}

// Concatenate theme variable with specified value
if (!function_exists('reisen_storage_concat')) {
	function reisen_storage_concat($var_name, $value) {
		global $REISEN_STORAGE;
		if (empty($REISEN_STORAGE[$var_name])) $REISEN_STORAGE[$var_name] = '';
		$REISEN_STORAGE[$var_name] .= $value;
	}
}

// Get array (one or two dim) element
if (!function_exists('reisen_storage_get_array')) {
	function reisen_storage_get_array($var_name, $key, $key2='', $default='') {
		global $REISEN_STORAGE;
		if (empty($key2))
			return !empty($var_name) && !empty($key) && isset($REISEN_STORAGE[$var_name][$key]) ? $REISEN_STORAGE[$var_name][$key] : $default;
		else
			return !empty($var_name) && !empty($key) && isset($REISEN_STORAGE[$var_name][$key][$key2]) ? $REISEN_STORAGE[$var_name][$key][$key2] : $default;
	}
}

// Set array element
if (!function_exists('reisen_storage_set_array')) {
	function reisen_storage_set_array($var_name, $key, $value) {
		global $REISEN_STORAGE;
		if (!isset($REISEN_STORAGE[$var_name])) $REISEN_STORAGE[$var_name] = array();
		if ($key==='')
			$REISEN_STORAGE[$var_name][] = $value;
		else
			$REISEN_STORAGE[$var_name][$key] = $value;
	}
}

// Set two-dim array element
if (!function_exists('reisen_storage_set_array2')) {
	function reisen_storage_set_array2($var_name, $key, $key2, $value) {
		global $REISEN_STORAGE;
		if (!isset($REISEN_STORAGE[$var_name])) $REISEN_STORAGE[$var_name] = array();
		if (!isset($REISEN_STORAGE[$var_name][$key])) $REISEN_STORAGE[$var_name][$key] = array();
		if ($key2==='')
			$REISEN_STORAGE[$var_name][$key][] = $value;
		else
			$REISEN_STORAGE[$var_name][$key][$key2] = $value;
	}
}

// Add array element after the key
if (!function_exists('reisen_storage_set_array_after')) {
	function reisen_storage_set_array_after($var_name, $after, $key, $value='') {
		global $REISEN_STORAGE;
		if (!isset($REISEN_STORAGE[$var_name])) $REISEN_STORAGE[$var_name] = array();
		if (is_array($key))
			reisen_array_insert_after($REISEN_STORAGE[$var_name], $after, $key);
		else
			reisen_array_insert_after($REISEN_STORAGE[$var_name], $after, array($key=>$value));
	}
}

// Add array element before the key
if (!function_exists('reisen_storage_set_array_before')) {
	function reisen_storage_set_array_before($var_name, $before, $key, $value='') {
		global $REISEN_STORAGE;
		if (!isset($REISEN_STORAGE[$var_name])) $REISEN_STORAGE[$var_name] = array();
		if (is_array($key))
			reisen_array_insert_before($REISEN_STORAGE[$var_name], $before, $key);
		else
			reisen_array_insert_before($REISEN_STORAGE[$var_name], $before, array($key=>$value));
	}
}

// Push element into array
if (!function_exists('reisen_storage_push_array')) {
	function reisen_storage_push_array($var_name, $key, $value) {
		global $REISEN_STORAGE;
		if (!isset($REISEN_STORAGE[$var_name])) $REISEN_STORAGE[$var_name] = array();
		if ($key==='')
			array_push($REISEN_STORAGE[$var_name], $value);
		else {
			if (!isset($REISEN_STORAGE[$var_name][$key])) $REISEN_STORAGE[$var_name][$key] = array();
			array_push($REISEN_STORAGE[$var_name][$key], $value);
		}
	}
}

// Pop element from array
if (!function_exists('reisen_storage_pop_array')) {
	function reisen_storage_pop_array($var_name, $key='', $defa='') {
		global $REISEN_STORAGE;
		$rez = $defa;
		if ($key==='') {
			if (isset($REISEN_STORAGE[$var_name]) && is_array($REISEN_STORAGE[$var_name]) && count($REISEN_STORAGE[$var_name]) > 0) 
				$rez = array_pop($REISEN_STORAGE[$var_name]);
		} else {
			if (isset($REISEN_STORAGE[$var_name][$key]) && is_array($REISEN_STORAGE[$var_name][$key]) && count($REISEN_STORAGE[$var_name][$key]) > 0) 
				$rez = array_pop($REISEN_STORAGE[$var_name][$key]);
		}
		return $rez;
	}
}

// Inc/Dec array element with specified value
if (!function_exists('reisen_storage_inc_array')) {
	function reisen_storage_inc_array($var_name, $key, $value=1) {
		global $REISEN_STORAGE;
		if (!isset($REISEN_STORAGE[$var_name])) $REISEN_STORAGE[$var_name] = array();
		if (empty($REISEN_STORAGE[$var_name][$key])) $REISEN_STORAGE[$var_name][$key] = 0;
		$REISEN_STORAGE[$var_name][$key] += $value;
	}
}

// Concatenate array element with specified value
if (!function_exists('reisen_storage_concat_array')) {
	function reisen_storage_concat_array($var_name, $key, $value) {
		global $REISEN_STORAGE;
		if (!isset($REISEN_STORAGE[$var_name])) $REISEN_STORAGE[$var_name] = array();
		if (empty($REISEN_STORAGE[$var_name][$key])) $REISEN_STORAGE[$var_name][$key] = '';
		$REISEN_STORAGE[$var_name][$key] .= $value;
	}
}

// Call object's method
if (!function_exists('reisen_storage_call_obj_method')) {
	function reisen_storage_call_obj_method($var_name, $method, $param=null) {
		global $REISEN_STORAGE;
		if ($param===null)
			return !empty($var_name) && !empty($method) && isset($REISEN_STORAGE[$var_name]) ? $REISEN_STORAGE[$var_name]->$method(): '';
		else
			return !empty($var_name) && !empty($method) && isset($REISEN_STORAGE[$var_name]) ? $REISEN_STORAGE[$var_name]->$method($param): '';
	}
}

// Get object's property
if (!function_exists('reisen_storage_get_obj_property')) {
	function reisen_storage_get_obj_property($var_name, $prop, $default='') {
		global $REISEN_STORAGE;
		return !empty($var_name) && !empty($prop) && isset($REISEN_STORAGE[$var_name]->$prop) ? $REISEN_STORAGE[$var_name]->$prop : $default;
	}
}
?>