<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      0.1.0
 * @package    Under_Construction
 * @subpackage OCUC_Activator
 */

// Exit if file accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

final class OCUC_Activator
{

	/**
	 * On activation, set default under-construction settings and data if not exists
	 * Note: onecom plugins and WP plugins both returns $pagenow as plugins_page
	 */
	public function uc_activate_stats()
	{

		// trigger plugin activation log
		global $pagenow;
		if ($pagenow === 'plugins.php') {
			$referrer = 'plugins_page';
		} else {
			$referrer = 'install_wizard';
		}

		// @phpunit-todo - uncomment before deploy - comment before phpunit
		(class_exists('OCPushStats') ? \OCPushStats::push_stats_event_themes_and_plugins('activate', 'plugin', ONECOM_UC_PLUGIN_SLUG, $referrer) : '');

		// if no option data exists for uc, set default (on first time activation)
		$startDate = current_time('timestamp');
		$default_time =	date('Y-m-d H:i', strtotime('+7 day', $startDate));
		if (get_option('onecom_under_construction_info') === false) {
			$uc_data = array(
				'uc_status' => 'off',
				'uc_http_mode' => '200',
				'uc_theme' => 'theme-1',
				'uc_timer_switch' => 'on',
				'uc_timer_action' => 'no-action',
				'uc_timer' => $default_time,
				'uc_subscribe_form' => 'off',
				'uc_whitelisted_roles' =>  array('administrator' => 'administrator'),
				'uc_headline' => 'Something is happening. Check in later!',
				'uc_description' => '',
				'uc_seo_title' => '',
				'uc_primary_color' => '',
				'uc_page_bg_color' => '',
				'uc_seo_description' => '',
				'uc_footer_scripts' => '',
				'uc_page_bg_image' => ONECOM_UC_DIR_URL .'assets/images/design-1-bg.jpeg',
				/* 'uc_page_bg_color' => '#e5e5e5', */
				'uc_copyright' => "Copyright &copy; ".date("Y").'. All rights reserved'
			);
			update_option('onecom_under_construction_info', $uc_data);
		}
	}
}
