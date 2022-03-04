<?php

class OnecomFixes extends OnecomHealthMonitor
{
	public function init()
	{
		add_action('wp_ajax_ocsh_fix_check_performance_cache', [$this, 'fix_check_performance_cache']);
		add_action('wp_ajax_ocsh_fix_woocommerce_sessions', [$this, 'fix_woocommerce_sessions']);
		add_action('wp_ajax_ocsh_fix_staging_check', [$this, 'fix_staging']);
		add_action('wp_ajax_ocsh_fix_check_pingbacks', [$this, 'fix_check_pingbacks']);
		add_action('wp_ajax_ocsh_fix_enable_cdn', [$this, 'fix_check_performance_cdn']);
		add_action('wp_ajax_ocsh_delete_file', [$this, 'fix_check_backup_zips']);
		add_action('wp_ajax_ocsh_fix_logout_duration', [$this, 'fix_check_logout_duration']);
		add_action('wp_ajax_ocsh_fix_xmlrpc', [$this, 'fix_xmlrpc']);
		add_action('wp_ajax_ocsh_fix_login_attempts', [$this, 'fix_spam_protection']);
		add_action('wp_ajax_ocsh_fix_login_recaptcha', [$this, 'fix_login_recaptcha']);
		add_action('wp_ajax_ocsh_fix_file_execution', [$this, 'fix_file_execution']);
		add_action('wp_ajax_ocsh_change_username', [$this, 'fix_usernames']);
		add_action('wp_ajax_ocsh_fix_dis_plugin', [$this, 'fix_dis_plugin']);
		add_action('wp_ajax_ocsh_fix_user_enumeration', [$this, 'fix_user_enumeration']);
		add_action('wp_ajax_ocsh_fix_spam_protection', [$this, 'fix_spam_protection']);
	}

	public function fix_woocommerce_sessions()
	{
		$db = new OnecomCheckDB();
		$result = $db->fix_woocommerce_sessions();
		$this->remove_from_ignore();
		wp_send_json($result);
	}

	public function fix_staging()
	{
		$stg = new OnecomCheckStaging();
		$result = $stg->fix_staging();
		$this->remove_from_ignore();
		wp_send_json($result);
	}

	public function fix_check_pingbacks()
	{
		$ping = new OnecomPingback();
		$result = $ping->fix_pingback();
		$this->remove_from_ignore();
		wp_send_json($result);

	}

	public function fix_check_performance_cache()
	{
		$pc = new OnecomCheckPlugins();
		$result = $pc->fix_performance_cache();
		$this->remove_from_ignore();
		wp_send_json($result);
	}

	public function fix_check_performance_cdn()
	{
		$pc = new OnecomCheckPlugins();
		$result = $pc->fix_performance_cdn();
		$this->remove_from_ignore();
		wp_send_json($result);
	}

    public function fix_check_backup_zips()
    {
        $fs = new OnecomCheckFiles();
        $file = strip_tags($_POST['file']);
        $result = $fs->fix_backup_zips($file);
        $this->remove_from_ignore();
        wp_send_json($result);
    }

	public function fix_check_logout_duration()
	{
		$logout = new OnecomCheckLogin();
		$result = $logout->fix_check_logout_time();
		$this->remove_from_ignore();
		wp_send_json($result);
	}

	public function fix_xmlrpc()
	{
		$xmlrpc = new OnecomXmlRpc();
		$this->remove_from_ignore();
		wp_send_json($xmlrpc->fix_check_xmlrpc());
	}

	public function fix_login_recaptcha()
	{
		$login = new OnecomCheckLogin();
		$this->remove_from_ignore();
		wp_send_json($login->fix_login_recaptcha($_POST));
	}

    public function fix_file_execution()
    {
        $file = new OnecomFileSecurity();
        $file->get_htaccess();
        $result = $file->oc_save_ht_cb();
        $this->remove_from_ignore();
        self::send_json($result);
    }

	public function fix_usernames()
	{
		$username = new OnecomCheckUsername();
		$result = $username->fix_usernames();
		$this->remove_from_ignore();
		wp_send_json($result);
	}

	public function fix_dis_plugin()
	{
		$plugin = new OnecomCheckPlugins();
		$result = $plugin->fix_dis_plugin();
		$this->remove_from_ignore();
		wp_send_json($result);
	}

	public function fix_user_enumeration()
	{
		$user = new OnecomCheckUsername();
		$result = $user->fix_user_enumeration();
		$this->remove_from_ignore();
		wp_send_json($result);
	}

	public function fix_spam_protection()
	{
		$plugin = new OnecomCheckSpam();
		$result = $plugin->fix_spam_protection();
		$this->remove_from_ignore();
		wp_send_json($result);
	}

	private function remove_from_ignore(): void
	{

        $check = strip_tags($_POST['action']);
        $check = str_replace(['ocsh_fix_', 'ocsh_fix_check_', 'check_'], '', $check);
        $marked_as_resolved = $this->ignored;
        if (empty($marked_as_resolved)) {
            $marked_as_resolved = [];
        }
        if (($key = array_search($check, $marked_as_resolved)) !== false) {
            unset($marked_as_resolved[$key]);
        }
        $this->push_stats('quick_fix', $check);
        update_option('oc_marked_resolved', $marked_as_resolved);
    }
}
