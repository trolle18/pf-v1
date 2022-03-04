<?php

/**
 * Hook into frontend to load under construction theme
 *
 * @since      0.1.0
 * @package    Under_Construction
 * @subpackage OCUC_Themes
 */

// Exit if file accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class OCUC_Themes
{
	// Main hook to call under construction feature
	public function init_theme()
	{
		// actions based on settings
		$this->uc_action();

		// under construction page rendering on frontend
		add_action('template_redirect', array($this, 'under_construction'));
		// Get UC Options data
		$uc_data = new OCUC_Render_Views();
		$uc_option = $uc_data->get_uc_option();

		// Revert WooCommerce lost-password if uc is on
		if (is_array($uc_option) && $uc_option['uc_status'] === 'on') {
			add_filter('lostpassword_url', array($this, 'uc_reset_pass_url'), 999, 0);
		}
	}

	// Revert WooCommerce /my-account/lost-password to default
	function uc_reset_pass_url()
	{
		$args = array('action' => 'lostpassword');
		$lostpassword_url = add_query_arg($args, network_site_url('wp-login.php', 'login'));
		return $lostpassword_url;
	}

	// Perform certain actions based on timer settings
	function uc_action()
	{
		// Get UC Options data
		$uc_data = new OCUC_Render_Views();
		$uc_option = $uc_data->get_uc_option();

		/** 
		 * Disable UC status if countdown action is set to 'disable' for past date
		 * Checks: valid date, switch is on, past timer and disable countdown set
		 */
		$uc_timer_action = isset($uc_option['uc_timer_action']) ? $uc_option['uc_timer_action'] : '';
		$uc_timer = isset($uc_option['uc_timer']) ? $uc_option['uc_timer'] : '';
		$uc_timer_switch = isset($uc_option['uc_timer_switch']) ? $uc_option['uc_timer_switch'] : '';
		if (
			strtotime($uc_timer) !== false &&
			$uc_timer_switch === 'on' &&
			strtotime($uc_timer) < current_time('timestamp') &&
			$uc_timer_action === 'disable' &&
			$uc_option['uc_status'] === 'on'
		) {
			$uc_option['uc_status'] = 'off';
			update_option('onecom_under_construction_info', $uc_option);
		}

		/** 
		 * Disable UC timer in admin if action is set to 'hide' timer for past date
		 * Checks: valid date, switch is on, past timer and disable countdown set
		 */
		$uc_timer_action = isset($uc_option['uc_timer_action']) ? $uc_option['uc_timer_action'] : '';
		if (
			strtotime($uc_timer) !== false &&
			$uc_timer_switch === 'on' &&
			strtotime($uc_timer) < current_time('timestamp') &&
			$uc_timer_action === 'hide'
		) {
			$uc_option['uc_timer_switch'] = 'off';
			update_option('onecom_under_construction_info', $uc_option);
		}
	}

	/* Check if migrator request for WP directory detection (using wp_migrate_test_file_<epoch>) */
	function ocm_client()
	{
		$headers = getallheaders();
		return (is_array($headers) && array_key_exists('User-Agent', $headers) && 		$headers['User-Agent'] === 'ocm-client');
	}

	// Render under construction feature and exit (die) to prevent wp theme loading
	function under_construction()
	{
		// Get UC Options data
		$uc_data = new OCUC_Render_Views();
		$uc_option = $uc_data->get_uc_option();

		// Check if current user is logged-in and whitelisted
		if (is_user_logged_in()) {
			$whitelisted_users = isset($uc_option['uc_whitelisted_roles']) && !empty($uc_option['uc_whitelisted_roles']) ? $uc_option['uc_whitelisted_roles'] : array();
			// get current user role/roles (yes, multiple is possible :O )
			$user = wp_get_current_user();
			$user_roles = array_values($user->roles);
			$whitelisted_users = array_values($whitelisted_users);
			// match if current role exists in whitelisted users, returns empty if no match
			$current_whitelisted = array_intersect($user_roles, $whitelisted_users);
		} else {
			// empty means no whitelisted role
			$current_whitelisted =  array();
		}

		/** If current user have any whitelisted role, return
		 * Note: It is possible to assign multiple roles via plugins
		 */
		if (count($current_whitelisted) > 0) {
			return null;
		}

		/**
		 * Whitelist pages
		 * If current post/page is found in exclude list, return
		 * Note: Home (with Posts Page settings) also returns 'post' type
		 */
		if (
			isset($uc_option['uc_exclude_pages'])
			&& !empty($uc_option['uc_exclude_pages'])
			&& is_array($uc_option['uc_exclude_pages'])
		) {

			// merge all cpt ids to simply match current id with in_array()
			$excluded_ids = array();
			foreach ($uc_option['uc_exclude_pages'] as $post_types) {
				$excluded_ids = array_merge($excluded_ids, $post_types);
			}
			$current_id = get_queried_object_id();

			// If current post/page id found in excluded, return
			if (
				!empty($excluded_ids)
				&& !is_home()
				&& (in_array($current_id, $excluded_ids)
					|| in_array("all-" . get_post_type(), $excluded_ids)
				)
			) {
				return null;
			}
		}

		/**
		 * If under construction status enabled
		 * * show under construction page
		 */
		if ($uc_option['uc_status'] === 'on') {
			// Send 503 headers if maintenance mode
			$uc_http_mode = $uc_option['uc_http_mode'];

			// Always send custom header for MM 
			header("x-onecom-maintenance-mode: on");

			// Prevent http 503 for migrator agent
			if ($uc_http_mode == '503' && !$this->ocm_client()) {
				header('HTTP/1.1 503 Service Unavailable');
			}

			// render selected theme design (default: theme-1)
			$theme_folder = isset($uc_option['uc_theme']) && strlen($uc_option['uc_theme']) ? $uc_option['uc_theme'] : 'theme-1';
			include_once ONECOM_UC_PLUGIN_URL . 'themes/' . $theme_folder . '/index.php';

			// @phpunit-todo - comment before phpunit & uncomment before deploy
			die();
		}
	}
}
