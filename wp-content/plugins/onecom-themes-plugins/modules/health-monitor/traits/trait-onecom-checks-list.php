<?php

trait OnecomChecksList {
	public $premium_checks = [
		'login_protection',
	    'uploads_index',
		'options_table_count',
		'staging_time',
		'backup_zips',
		'performance_cache',
		'enable_cdn',
		'updated_long_ago',
		'pingbacks',
		'xmlrpc',
		'spam_protection',
		'login_attempts',
		'user_enumeration',
		'optimize_uploaded_images',
//		'vulnerability_exists',
//		'logout_duration',
//		'login_recaptcha',
//		'asset_minification',
	];
	public $old_checks = [
		'error_reporting',
		'usernames',
		'php_updates',
		'plugin_updates',
		'theme_updates',
		'wp_updates',
		'wp_connection',
		'core_updates',
		'ssl',
		'file_execution',
		'file_permissions',
		'file_edit',
		'dis_plugin',
		//		'DB',
	];

	/**
	 * Function onecom_get_checks
	 * Get a list of checks to perform
	 * @return array
	 */
	public function onecom_get_checks(): array {
		return array_merge( $this->premium_checks, $this->old_checks );
	}
}