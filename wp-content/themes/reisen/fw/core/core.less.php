<?php
/**
 * Reisen Framework: less manipulations
 *
 * @package	reisen
 * @since	reisen 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


// Theme init
if (!function_exists('reisen_less_theme_setup2')) {
	add_action( 'reisen_action_after_init_theme', 'reisen_less_theme_setup2' );
	function reisen_less_theme_setup2() {
		if (reisen_get_theme_setting('less_compiler')!='no' && !is_admin() && reisen_get_theme_option('debug_mode')=='yes') {

			// Regular run - if not admin - recompile only changed files
			$check_time = true;
			if (!is_customize_preview() && (int) get_option(reisen_storage_get('options_prefix') . '_compile_less') > 0) {
				update_option(reisen_storage_get('options_prefix') . '_compile_less', 0);
				$check_time = false;
			}
			
			reisen_storage_set('less_check_time', $check_time);
			do_action('reisen_action_compile_less');
			reisen_storage_set('less_check_time', false);

		}
	}
}



/* LESS
-------------------------------------------------------------------------------- */

// Recompile all LESS files
if (!function_exists('reisen_compile_less')) {	
	function reisen_compile_less($list = array(), $recompile=true) {

		if (!function_exists('trx_utils_less_compiler')) return false;

		$success = true;

		if (!is_array($list) || count($list)==0) return $success;

		// Less compiler
		$less_compiler = reisen_get_theme_setting('less_compiler');
		if ($less_compiler == 'no') return $success;
	
		// Prepare theme specific LESS-vars (colors, backgrounds, logo height, etc.)
		$less_split = reisen_get_theme_setting('less_split');
		$vars = apply_filters('reisen_filter_prepare_less', '');
		if ($less_compiler=='external' || !$less_split) {
			// Save LESS-vars into theme.vars.less
			reisen_fpc(reisen_get_file_dir('css/theme.vars.less'), $vars);
			if ($less_compiler=='external') return $success;
			$vars = '';
		}
		
		// Generate map for the LESS-files
		$less_map = reisen_get_theme_setting('less_map');
		if (reisen_get_theme_option('debug_mode')=='no' || $less_compiler=='lessc') $less_map = 'no';
		
		// Get separator to split LESS-files
		$less_sep = $less_map!='no' ? '' : reisen_get_theme_setting('less_separator');

		// Prepare separate array with less utils (not compile it alone - only with main files)
		$utils = $less_map!='no' ? array() : '';
		$utils_time = 0;
		if (is_array($list) && count($list) > 0) {
			foreach($list as $k=>$file) {
				$fname = basename($file);
				if ($fname[0]=='_') {
					if ($less_map!='no')
						$utils[] = $file;
					else
						$utils .= reisen_fgc($file);
					$list[$k] = '';
					$tmp = filemtime($file);
					if ($utils_time < $tmp) $utils_time = $tmp;
				}
			}
		}
		
		// Compile all .less files
		if (is_array($list) && count($list) > 0) {
			$success = trx_utils_less_compiler($list, array(
				'compiler' => $less_compiler,
				'map' => $less_map,
				'parse_files' => !$less_split,
				'utils' => $utils,
				'utils_time' => $utils_time,
				'vars' => $vars,
				'import' => array(reisen_get_folder_dir('css')),
				'separator' => $less_sep,
				'check_time' => reisen_storage_get('less_check_time')==true,
				'compressed' => reisen_get_theme_option('debug_mode')=='no'
				)
			);
		}
		
		return $success;
	}
}
?>