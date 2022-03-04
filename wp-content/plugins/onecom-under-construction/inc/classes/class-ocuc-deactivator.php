<?php

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      0.1.0
 * @package    Under_Construction
 * @subpackage OCUC_Deactivator
 */

// Exit if file accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

final class OCUC_Deactivator
{

	/**
	 * deactivation/uninstall hooks
	 */
	public function uc_deactivate_stats()
	{
		// trigger plugin deactivation log
		(class_exists('OCPushStats')?\OCPushStats::push_stats_event_themes_and_plugins('deactivate','plugin',ONECOM_UC_PLUGIN_SLUG,'plugins_page'):'');
	}
}
