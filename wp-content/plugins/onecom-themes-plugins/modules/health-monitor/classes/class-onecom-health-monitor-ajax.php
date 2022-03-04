<?php
declare(strict_types=1);

/**
 * Class OnecomAjax
 * Deals with ajax requests
 */
class OnecomHealthMonitorAjax extends OnecomHealthMonitor
{
	private $file_object;

	public function __construct()
	{
		parent::__construct();
		if (!function_exists('oc_sh_check_php_updates')) {
			require_once MODULE_PATH . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'functions.php';
		}
		$this->file_object = new OnecomCheckFiles();
	}

	public function init()
	{
		$fixes = new OnecomFixes();
		add_action('wp_ajax_ocsh_mark_resolved', [$this, 'ocsh_mark_resolved']);
		add_action('wp_ajax_onecom_unignore', [$this, 'unignore']);
		add_action('wp_ajax_ocsh_reset_checks', [$this, 'reset_checks']);
		$this->add_check_callbacks();
		$fixes->init();
		$this->add_undo_callbacks();
	}

	/**
	 * Add AJAX callbacks for checks
	 */
	public function add_check_callbacks(): void
	{
		add_action('wp_ajax_ocsh_check_php_updates', [$this, 'php_updates']);
		add_action('wp_ajax_ocsh_check_plugin_updates', [$this, 'plugin_updates']);
		add_action('wp_ajax_ocsh_check_theme_updates', [$this, 'theme_updates']);
		add_action('wp_ajax_ocsh_check_wp_updates', [$this, 'wp_updates']);
		add_action('wp_ajax_ocsh_check_wp_connection', [$this, 'wp_connection']);
		add_action('wp_ajax_ocsh_check_core_updates', [$this, 'core_updates']);
		add_action('wp_ajax_ocsh_check_ssl', [$this, 'check_ssl']);
		add_action('wp_ajax_ocsh_check_file_execution', [$this, 'file_execution']);
		add_action('wp_ajax_ocsh_check_file_permissions', [$this, 'file_permissions']);
		add_action('wp_ajax_ocsh_check_DB', [$this, 'database']);
		add_action('wp_ajax_ocsh_check_file_edit', [$this, 'file_edit']);
		add_action('wp_ajax_ocsh_check_usernames', [$this, 'usernames']);
		add_action('wp_ajax_ocsh_check_dis_plugin', [$this, 'dis_plugin']);
		add_action('wp_ajax_ocsh_save_result', [$this, 'save_result_cb']);
		add_action('wp_ajax_ocsh_check_uploads_index', [$this, 'uploads_index_cb']);
		add_action('wp_ajax_ocsh_check_woocommerce_sessions', [$this, 'woocommerce_session']);
		add_action('wp_ajax_ocsh_check_options_table_count', [$this, 'options_table_count']);
		add_action('wp_ajax_ocsh_check_staging_time', [$this, 'staging_time']);
		add_action('wp_ajax_ocsh_check_backup_zips', [$this, 'backup_zips']);
		add_action('wp_ajax_ocsh_check_performance_cache', [$this, 'performance_cache']);
		add_action('wp_ajax_ocsh_check_updated_long_ago', [$this, 'updated_long_ago']);
		add_action('wp_ajax_ocsh_check_pingbacks', [$this, 'pingbacks']);
		add_action('wp_ajax_ocsh_check_logout_duration', [$this, 'logout_duration']);
		add_action('wp_ajax_ocsh_check_xmlrpc', [$this, 'xmlrpc']);
		add_action('wp_ajax_ocsh_check_spam_protection', [$this, 'spam_protection']);
		add_action('wp_ajax_ocsh_check_login_attempts', [$this, 'login_attempts']);
		add_action('wp_ajax_ocsh_check_login_recaptcha', [$this, 'login_recaptcha']);
		add_action('wp_ajax_ocsh_check_asset_minification', [$this, 'asset_minification']);
		add_action('wp_ajax_ocsh_check_error_reporting', [$this, 'error_reporting']);
		add_action('wp_ajax_ocsh_check_user_enumeration', [$this, 'user_enumeration']);
		add_action('wp_ajax_ocsh_check_optimize_uploaded_images', [$this, 'optimize_uploaded_images']);
		add_action('wp_ajax_ocsh_check_enable_cdn', [$this, 'enable_cdn']);
		add_action('wp_ajax_ocsh_check_login_protection', [$this, 'login_protection']);
	}

	public function add_undo_callbacks(): void
	{
		add_action('wp_ajax_ocsh_undo_check_pingbacks', [$this, 'undo_check_pingbacks']);
		add_action('wp_ajax_ocsh_undo_check_performance_cache', [$this, 'undo_check_performance_cache']);
		add_action('wp_ajax_ocsh_undo_enable_cdn', [$this, 'undo_enable_cdn']);
		add_action('wp_ajax_ocsh_undo_logout_duration', [$this, 'undo_check_logout_duration']);
		add_action('wp_ajax_ocsh_undo_xmlrpc', [$this, 'undo_fix_xmlrpc']);
		add_action('wp_ajax_ocsh_undo_login_recaptcha', [$this, 'undo_login_recaptcha']);
		add_action('wp_ajax_ocsh_undo_login_attempts', [$this, 'undo_login_attempts']);
	}

	/**
	 * Response format based on type of request
	 */
	public function send_json(array $result, string $check = '')
	{
		if (!(defined('REST_REQUEST') || defined('DOING_CRON'))) {
			if ($this->is_ignored($check)) {
				$result[$this->status_key] = 3;
			}
			// add "html" key if not present
			if (!isset($result['html'])) {
				$result['html'] = $this->get_html($check, $result);
			}
			wp_send_json($result);
		}

		return $result;
	}

	public function php_updates()
	{
		$php_update = new OnecomCheckUpdates();
		$result = $php_update->php_updates();
		parent::save_result('php_updates', $result['status']);
		self::send_json($result, 'php_updates');
	}

	public function plugin_updates()
	{
		$php_update = new OnecomCheckUpdates();
		$result = $php_update->plugin_updates();
		parent::save_result('plugin_updates', $result['status']);
		self::send_json($result, 'plugin_updates');
	}

	public function theme_updates()
	{
		$php_update = new OnecomCheckUpdates();
		$result = $php_update->theme_updates();
		parent::save_result('theme_updates', $result['status']);
		self::send_json($result, 'theme_updates');
	}

	public function wp_updates()
	{
		$updates = new OnecomCheckUpdates();
		$result = $updates->check_wp_updates();
		parent::save_result('wp_updates', $result['status']);
		self::send_json($result, 'wp_updates');
	}

	public function wp_connection()
	{
		$updates = new OnecomCheckUpdates();
		$result = $updates->check_wp_connection();
		parent::save_result('wp_connection', $result['status']);
		self::send_json($result, 'wp_connection');
	}

	public function core_updates()
	{
		$updates = new OnecomCheckUpdates();
		$result = $updates->check_auto_updates();
		parent::save_result('core_updates', $result['status']);
		self::send_json($result, 'core_updates');
	}

	public function check_ssl()
	{
		$ssl = new OnecomCheckSsl();
		$result = $ssl->oc_sh_check_ssl();
		parent::save_result('ssl', $result['status']);
		self::send_json($result, 'ssl');
	}

	public function file_execution()
	{
		$result = $this->file_object->check_execution();
		$result['fix'] = true;
		$result['revert'] = true;
		parent::save_result('file_execution', $result['status']);
		self::send_json($result, 'file_execution');
	}

	public function file_permissions()
	{
		$result = $this->file_object->check_permission();
		parent::save_result('file_permissions', $result['status']);
		self::send_json($result, 'file_permissions');
	}

	public function database()
	{
		$db = new OnecomCheckDB();
		$result = $db->check_db_security();
		parent::save_result('DB', $result['status']);
		self::send_json($result, 'DB');
	}

	public function file_edit()
	{
		$file = new OnecomCheckFiles();
		$result = $file->check_file_editing();
		parent::save_result('file_edit', $result['status']);
		self::send_json($result, 'file_edit');
	}

	public function usernames()
	{
		$usernames = new OnecomCheckUsername();
		$result = $usernames->check_usernames();
		parent::save_result('usernames', $result['status']);
		self::send_json($result, 'usernames');
	}

	public function dis_plugin()
	{
		$plugins = new OnecomCheckPlugins();
		$result = $plugins->check_discouraged_plugins();
		parent::save_result('dis_plugin', $result['status']);
		self::send_json($result, 'dis_plugin');
	}

	public function save_result_cb(): float
	{
		return floatval($_POST['osch_Result']);
	}

	public function uploads_index_cb()
	{
		$fs = new OnecomCheckFiles();
		$result = $fs->check_index();
		$result['html'] = $this->get_html('uploads_index', $result);
		parent::save_result('uploads_index', $result['status']);
		self::send_json($result, 'uploads_index');
	}

	public function woocommerce_session()
	{
		$db = new OnecomCheckDB();
		$result = $db->check_woocommerce_session();
		$result['fix'] = true;
		$result['html'] = $this->get_html('woocommerce_sessions', $result);
		parent::save_result('woocommerce_sessions', $result['status']);
		self::send_json($result, 'woocommerce_sessions');
	}

	public function options_table_count()
	{
		$db = new OnecomCheckDB();
		$result = $db->check_options_table();
		$result['html'] = $this->get_html('options_table_count', $result);
		parent::save_result('options_table_count', $result['status']);
		self::send_json($result, 'options_table_count');
	}

	public function staging_time()
	{
		$stg = new OnecomCheckStaging();
		$result = $stg->check_staging_time();
		$result['fix'] = true;
		$result['fix_text'] = __('Review staging', $this->text_domain);
		$result['fix_url'] = admin_url('admin.php?page=onecom-wp-staging');
		$result['html'] = $this->get_html('check_staging_time', $result);
		parent::save_result('check_staging_time', $result['status']);
		self::send_json($result, 'check_staging_time');
	}

	public function backup_zips()
	{
		$fs = new OnecomCheckFiles();
		$result = $fs->check_backup_zips();
		$result['delete-link'] = true;
		$result['html'] = $this->get_html('check_backup_zip', $result);
		parent::save_result('check_backup_zip', $result['status']);
		self::send_json($result, 'check_backup_zip');
	}

	public function performance_cache()
	{
		$plugins = new OnecomCheckPlugins();
		$result = $plugins->check_performance_cache();
		$result['fix'] = true;
		$result['undo'] = true;
		if (isset($result['activate_plugin']) && $result['activate_plugin']) {
			$result['fix_url'] = admin_url('plugins.php?plugin_status=inactive');
		}
		$result['html'] = $this->get_html('check_performance_cache', $result);
		parent::save_result('performance_cache', $result['status']);
		self::send_json($result, 'performance_cache');
	}

	public function updated_long_ago()
	{
		$plugins = new OnecomCheckPlugins();
		$result = $plugins->check_plugins_last_update();
		$result['html'] = $this->get_html('check_updated_long_ago', $result);
		parent::save_result('check_updated_long_ago', $result['status']);
		self::send_json($result, 'check_updated_long_ago');
	}

	public function pingbacks()
	{
		$pingback = new OnecomPingback();
		$result = $pingback->check_pingbacks();
		$result['fix'] = true;
		$result['undo'] = true;
		$result['fix_text'] = __('Disable pingback', $this->text_domain);
		$result['html'] = $this->get_html('check_pingbacks', $result);
		parent::save_result('check_pingbacks', $result['status']);
		self::send_json($result, 'check_pingbacks');
	}

	public function login_protection()
	{
		$login = new OnecomCheckLogin();
		$result = $login->check_login_protection();
		$result['fix'] = true;
		$result['fix_url'] = OC_CP_LOGIN_URL.'&utm_source=onecom_wp_plugin&utm_medium=login_masking';
		$result['html'] = $this->get_html('login_protection', $result);
		parent::save_result('login_protection', $result['status']);
		self::send_json($result, 'login_protection');
	}

    /**
     * Ignore a check from future scans
     */
    public function ocsh_mark_resolved()
    {
        $check = strip_tags($_POST['check'] );
        $check = str_replace('check_', '', $check);
        $marked_as_resolved = $this->ignored;
        if (empty($marked_as_resolved)) {
            $marked_as_resolved = [];
        }
        if (!in_array($check, $marked_as_resolved)) {
            $marked_as_resolved[] = $check;
        }
        $result = update_option('oc_marked_resolved', $marked_as_resolved);
        $this->push_stats('ignore', $check);
        if ($result) {
            wp_send_json($this->format_result($this->flag_resolved, __('Ignored in future scans', $this->text_domain)));
        } else {
            wp_send_json($this->format_result($this->flag_open, __('Could not ignore from future scans', $this->text_domain)));
        }

	}

	/**
	 * Remove a check from ignore list
	 */
	public function unignore(): void
	{
		$check = sanitize_text_field( $_POST['check'] );
		$check = str_replace('check_', '', $check);
		$marked_as_resolved = $this->ignored;
		if (empty($marked_as_resolved)) {
			$marked_as_resolved = [];
		}

		if (($key = array_search($check, $marked_as_resolved)) !== false) {
			unset($marked_as_resolved[$key]);
		}
		$this->push_stats('unignore', $check);
		$result = update_option('oc_marked_resolved', $marked_as_resolved);
		if ($result) {
			wp_send_json($this->format_result($this->flag_resolved, __('Unignored from future scans', $this->text_domain)));
		} else {
			wp_send_json($this->format_result($this->flag_open, __('Could not remove from ignored list', $this->text_domain)));
		}

	}

	/**
	 * Reset the list of ignored checks
	 * @todo    not used, removed
	 */
	public function reset_checks()
	{
		$result = delete_option($this->resolved_option);
		if ($result) {
			wp_send_json(
				$this->format_result($this->flag_resolved, __("Success", $this->text_domain))
			);
		} else {
			wp_send_json(
				$this->format_result($this->flag_open, __("Failed", $this->text_domain)));
		}
	}

	public function undo_check_pingbacks()
	{
		$pingbacks = new OnecomPingback();
		$result = $pingbacks->undo();
		$this->push_stats('revert', 'pingbacks');
		wp_send_json($result);
	}

	public function undo_check_performance_cache()
	{
		$pc = new OnecomCheckPlugins();
		$result = $pc->undo_check_performance_cache();
		$this->push_stats('revert', 'performance_cache');
		wp_send_json($result);
	}

	public function logout_duration()
	{
		$pc = new OnecomCheckLogin();
		$result = $pc->check_logout_time();
		$result['fix'] = true;
		$result['undo'] = true;
		$result['fix_text'] = sprintf(__('Change logout time to %s hours', $this->text_domain), "4");
		$result['undo'] = true;
		$result['html'] = $this->get_html('logout_duration', $result);
		parent::save_result('logout_duration', $result['status']);
		self::send_json($result, 'logout_duration');
	}

	public function undo_check_logout_duration()
	{
		$logout = new OnecomCheckLogin();
		$this->push_stats('revert', 'logout_duration');
		wp_send_json($logout->undo_check_logout_time());
	}

	public function xmlrpc()
	{
		$xmlrpc = new OnecomXmlRpc();
		$result = $xmlrpc->check_xmlrpc();
		$result['fix'] = true;
		$result['undo'] = true;
		$result['html'] = $this->get_html('xmlrpc', $result);
		parent::save_result('xmlrpc', $result['status']);
		self::send_json($result, 'xmlrpc');
	}

	public function undo_fix_xmlrpc()
	{
		$xmlrpc = new OnecomXmlRpc();
		$this->push_stats('revert', 'xmlrpc');
		wp_send_json($xmlrpc->undo_check_xmlrpc());
	}

	public function spam_protection()
	{
		$spam = new OnecomCheckSpam();
		$result = $spam->check_spam_protection();
		if ($result[$this->status_key] === $this->flag_open) {
			$theme_result = $spam->is_onecom_theme();
			$result['fix'] = true;
			if ($theme_result['onecom_theme'] && $theme_result['url'] != '') {
				$result['fix_url'] = $theme_result['url'];
				$result['fix_text'] = __('Enable spam protection', $this->text_domain);
			} else {
//				$result['fix_url']  = admin_url( 'admin.php?page=onecom-wp-recommended-plugins' );
			}
		}

		$result['html'] = $this->get_html('spam_protection', $result);
		parent::save_result('spam_protection', $result['status']);
		self::send_json($result, 'spam_protection');
	}

	public function login_attempts($is_login_check = false)
	{
//		$login          = new OnecomCheckLogin();
//		$result         = $login->check_failed_login();
		$login = new OnecomCheckSpam();
		$result = $login->check_spam_protection($is_login_check);
		$result['fix'] = true;
		$result['undo'] = true;
		$result['html'] = $this->get_html('login_attempts', $result);

		if (isset($_POST['action']) && (($_POST['action'] === 'ocsh_check_login_attempts') || $is_login_check)) {
			parent::save_result('login_attempts', $result['status']);
			self::send_json($result, 'login_attempts');
		} else {
			parent::save_result('spam_protection', $result['status']);
			self::send_json($result, 'spam_protection');
		}


	}

	public function undo_login_attempts()
	{
		$login = new OnecomCheckSpam();
		$this->push_stats('revert', 'login_attempts');
		wp_send_json($login->undo_spam_protection());
	}

	/**
	 * @todo remove this unused function reset_failed_login
	 */
	public function reset_failed_login()
	{
		$login = new OnecomCheckLogin();
		wp_send_json($login->reset_failed_login_data());
	}

	public function login_recaptcha()
	{
		$login = new OnecomCheckLogin();
		$result = $login->login_recaptcha();
		$result['fix'] = true;
		$result['undo'] = true;
		$result['fix_text'] = __('Enable recaptcha', $this->text_domain);
		$result['input_fields'] = [
			[
				'name' => 'oc_hm_site_key',
				'type' => 'text',
				'label' => __('Site key', $this->text_domain)
			],
			[
				'name' => 'oc_hm_site_secret',
				'type' => 'text',
				'label' => __('Site secret', $this->text_domain)
			]
		];
		$result['info_text'] = sprintf(__('You can obtain these values <a href="%s">here</a>', $this->text_domain), 'https://www.google.com/recaptcha/admin/create');
		$result['html'] = $this->get_html('login_recaptcha', $result);
		parent::save_result('login_recaptcha', $result['status']);
		self::send_json($result, 'login_recaptcha');
	}

	public function undo_login_recaptcha()
	{
		$login = new OnecomCheckLogin();
		$this->push_stats('revert', 'login_recaptcha');
		wp_send_json($login->undo_login_recaptcha());
	}

	public function asset_minification()
	{
		$minification = new OnecomCheckAssetMinification();
		$result = $minification->check_minification();
		$result['html'] = $this->get_html('asset_minification', $result);
		parent::save_result('asset_minification', $result['status']);
		self::send_json($result, 'asset_minification');
	}

	public function error_reporting()
	{
		$err = new OnecomDebugMode();
		$result = $err->check_error_reporting();
		$result['html'] = $this->get_html('error_reporting', $result);
		parent::save_result('error_reporting', $result['status']);
		self::send_json($result, 'error_reporting');
	}

	public function user_enumeration()
	{
		$usr = new OnecomCheckUsername();
		$result = $usr->check_user_enumeration();
		$result['fix'] = true;
		$result['html'] = $this->get_html('user_enumeration', $result);
		parent::save_result('user_enumeration', $result['status']);
		self::send_json($result, 'user_enumeration');
	}

	public function optimize_uploaded_images()
	{
		$plugin = new OnecomCheckPlugins();
		$result = $plugin->is_imagify_setup();
		$result['fix'] = true;
		$result['html'] = $this->get_html('optimize_uploaded_images', $result);
		parent::save_result('optimize_uploaded_images', $result['status']);
		self::send_json($result, 'optimize_uploaded_images');
	}

	public function enable_cdn()
	{
		$plugins = new OnecomCheckPlugins();
		$result = $plugins->check_cdn();
		$result['fix'] = true;
		$result['undo'] = true;
		if (isset($result['activate_plugin']) && $result['activate_plugin']) {
			$result['fix_text'] = __('Activate Performance cache', $this->text_domain);
			$result['fix_url'] = admin_url('plugins.php?plugin_status=inactive');
		}
		$result['html'] = $this->get_html('enable_cdn', $result);
		parent::save_result('enable_cdn', $result['status']);
		self::send_json($result, 'enable_cdn');
	}

	public function undo_enable_cdn()
	{
		$pc = new OnecomCheckPlugins();
		$result = $pc->undo_check_performance_cdn();
		$this->push_stats('revert', 'enable_cdn');
		wp_send_json($result);
	}
}
