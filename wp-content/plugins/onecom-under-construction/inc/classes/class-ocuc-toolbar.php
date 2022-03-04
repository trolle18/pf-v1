<?php

/**
 * Add shortcut to admin and frontend toolbar
 *
 * This class defines under-construction shortcut link in toolbar
 *
 * @since      0.2.0
 * @package    Under_Construction
 * @subpackage OCUC_Toolbar
 */

// Exit if file accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class OCUC_Toolbar
{

	public function __construct()
	{
		add_action('admin_bar_menu', array($this, 'add_toolbar_link'), 100);
	}

	public function add_toolbar_link()
	{
		global $wp_admin_bar;

		if (!is_super_admin() || !is_admin_bar_showing()) {
			return;
		}

		$uc_option = get_option('onecom_under_construction_info');
		$uc_settings_url = admin_url('admin.php?page=onecom-wp-under-construction');

		// Set toolbar shortcut text based on current uc status
		if (isset($uc_option['uc_status']) && $uc_option['uc_status'] === 'on') {
			$uc_status = __('Maintenance Mode is ON', ONECOM_UC_TEXT_DOMAIN);
		} else {
			$uc_status = __('Maintenance Mode is OFF', ONECOM_UC_TEXT_DOMAIN);
		}
		$wp_admin_bar->add_menu(
			array(
				'id'    => 'ocuc_options',
				'title' => $uc_status,
				'href'  => $uc_settings_url,
				'meta'  => array(
					'title' => $uc_status,
				),
			)
		);
	}
}

$toolbar = new OCUC_Toolbar();
