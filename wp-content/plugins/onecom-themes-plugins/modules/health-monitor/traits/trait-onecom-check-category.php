<?php

trait OnecomCheckCategory
{
    public $category = [];

    public function init_trait_category($skip_translations = 0)
    {
        $this->category = [
            'security' => (0 === $skip_translations) ? __('Security', OC_PLUGIN_DOMAIN) : 'Security',
            'critical' => (0 === $skip_translations) ? __('Critical', OC_PLUGIN_DOMAIN) : 'Critical',
            'performance' => (0 === $skip_translations) ? __('Performance', OC_PLUGIN_DOMAIN) : 'Performance',
        ];
    }

    /**
     * Get the category of a check
     *
     * @param string $check
     *
     * @return string
     */
    public function get_check_category($check = '', $html = 1): string
    {
        if (empty($check)) {
            return '';
        }
        $checks = [
            'uploads_index' => $this->category['critical'],
            'options_table_count' => $this->category['critical'],
            'staging_time' => $this->category['critical'],
            'backup_zip' => $this->category['critical'],
            'wp_connection' => $this->category['critical'],
            'dis_plugin' => $this->category['critical'],
            'woocommerce_sessions' => $this->category['critical'],
            'core_updates' => $this->category['critical'],
            'performance_cache' => $this->category['performance'],
            'updated_long_ago' => $this->category['security'],
            'pingbacks' => $this->category['security'],
            'logout_duration' => $this->category['security'],
            'xmlrpc' => $this->category['security'],
            'spam_protection' => $this->category['security'],
            'login_attempts' => $this->category['security'],
            'login_recaptcha' => $this->category['security'],
            'asset_minification' => $this->category['performance'],
            'php_updates' => $this->category['security'],
            'plugin_updates' => $this->category['security'],
            'theme_updates' => $this->category['security'],
            'wp_updates' => $this->category['critical'],
            'ssl' => $this->category['security'],
            'file_execution' => $this->category['security'],
            'file_permissions' => $this->category['security'],
            'DB' => $this->category['security'],
            'file_edit' => $this->category['security'],
            'usernames' => $this->category['security'],
            'error_reporting' => $this->category['security'],
            'enable_cdn' => $this->category['performance'],
//		'vulnerable_components' => $this->category['performance'],
            'user_enumeration' => $this->category['security'],
            'optimize_uploaded_images' => $this->category['performance'],
            'login_protection' => $this->category['security']
        ];
        $check = str_replace('check_', '', $check);
        if ((!array_key_exists($check, $checks)) || empty ($checks[$check])) {
            return '';
        }

        return (1 === $html) ? ('<span class="onecom_tag '.$checks[$check].'">' . $checks[$check] . '</span>') : $checks[$check];

    }
}
