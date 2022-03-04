<?php
declare(strict_types=1);

/**
 * class OneComCheckUpdates
 */
class OnecomCheckUpdates extends OnecomHealthMonitor
{
    public function __construct()
    {
        $this->cms_update = "<a href='https://help.one.com/hc/%s/articles/360001621938-How-do-I-update-a-CMS-like-WordPress-and-Joomla-' target='_blank'>";

    }

    public function php_updates()
    {
        $this->log_entry('Scanning available site updates -- PHP version');
        $php_updates_available = version_compare(PHP_VERSION, '7.3.0', '<');
        $this->log_entry('Finished scaanning available site updates -- PHP version');

        $guide_link = sprintf("<a href='https://help.one.com/hc/%s/articles/360000449117-How-do-I-update-PHP-for-my-WordPress-site-' target='_blank'>", onecom_generic_locale_link('', get_locale(), 1));

        if ($php_updates_available) {
            $title = __('PHP version', $this->text_domain);
            $desc = sprintf(__('You are running an outdated version of PHP. We recommend using the latest version of PHP. %sHow do I update PHP for my WordPress site?%s', $this->text_domain));
            $status = $this->flag_open;
        } else {
            $title = __('You have the latest PHP version enabled!', $this->text_domain);
            $desc = '';
            $status = $this->flag_resolved;
        }
        $this->log_entry('Finished scan for PHP version');
        // @todo: make this functional
//		oc_sh_save_result( 'php_updates', $result[ $this->status_key ] );

        return $this->format_result($status, $title, $desc);
    }

    public function plugin_updates()
    {
        $result = [];
        // plugin updates
        $plugin_updates_available = false;
        $plugin_transients = get_site_transient('update_plugins');
        $this->log_entry('Scanning available site updates -- Plugins');
        if (isset($plugin_transients->response) && count($plugin_transients->response) > 0) {
            $plugin_updates_available = true;
        }
        $this->log_entry('Finished scanning available site updates -- Plugins');

        $guide_link = sprintf($this->cms_update, onecom_generic_locale_link('', get_locale(), 1));
        $list = [];
        if ($plugin_updates_available) {
            $title = __('Plugins', $this->text_domain);
            $desc = sprintf(__('One or more of your plugins are outdated. Outdated plugins make your site vulnerable to security attacks. %sUpdate your Plugins to the newest version%s', $this->text_domain), $guide_link, "</a>");
            $status = $this->flag_open;
            $plugins = get_plugins();
            foreach ($plugin_transients->response as $plugin) {
                $list[] = $this->get_plugin_name($plugin->plugin, $plugins);
            }
        } else {
            $title = __('All your plugins are updated', $this->text_domain);
            $desc = '';
            $status = $this->flag_resolved;
        }

        $this->log_entry('Finished scan for available plugin updates');
        //@todo
        //oc_sh_save_result( 'plugin_updates', $result[ $this->status_key ] );

        $result = $this->format_result($status, $title, $desc);
        $result['list'] = $list;

        return $result;
    }

    public function theme_updates()
    {
        //theme updates
        $this->log_entry('Scanning available site updates -- Themes');
        $theme_update_available = false;
        $theme_transients = get_site_transient('update_themes');
        if (isset($theme_transients->response) && (count($theme_transients->response) > 0)) {
            $theme_update_available = true;
        }
        $this->log_entry('Finished scanning available site updates -- Themes');
        $list = [];
        if ($theme_update_available) {

            foreach ($theme_transients->response as $theme_dir => $theme) {
                $theme = wp_get_theme($theme_dir);
                if (is_a($theme, 'WP_Theme')){
                    $list[] = $theme->get('Name');
                }

            }
            $status = $this->flag_open;
        } else {
            $status = $this->flag_resolved;
        }
        $this->log_entry('Finished scan for available theme updates');
        $result = $this->format_result($status);
        sort($list);
        $result['list'] = $list;
        return $result;
    }

    /**
     * Check if wordpress core is updated
     * @return array
     */
    public function check_wp_updates(): array
    {
        //core updates
        $this->log_entry('Scanning available site updates -- Core');
        $core_update_available = false;
        $core_transients = get_site_transient('update_core');
        if ($core_transients->updates && is_array($core_transients->updates)) {
            foreach ($core_transients->updates as $updates) {
                if (isset($updates->response) && $updates->response === 'upgrade') {
                    $core_update_available = true;
                }
            }
        }

        $guide_link = sprintf($this->cms_update, onecom_generic_locale_link('', get_locale(), 1));

        if ($core_update_available) {
            $status = $this->flag_open;
            $title = __('WordPress version', $this->text_domain);
            $desc = sprintf(__('Update WordPress to the latest version, especially minor updates are important because they usually include security fixes. Check this guide for more instructions:  %sHow do I update a CMS like WordPress?%s', $this->text_domain), $guide_link, "</a>");
        } else {
            $title = __('Your WordPress version is updated', $this->text_domain);
            $desc = '';
            $status = $this->flag_resolved;
        }
        $this->log_entry('Finished scaanning available site updates -- Core');

//@todo		oc_sh_save_result( 'core_updates', $result[ $this->status_key ] );

        return $this->format_result($status, $title, $desc);
    }

    public function check_wp_connection()
    {
        $this->log_entry('Checking connections to WordPress.org');
        $wp_org_connection = $this->check_connection();
        if ($wp_org_connection[$this->status_key] == $this->flag_open) {
            $this->log_entry('Could not connect to WordPress.org');

            return $wp_org_connection;
        } else {
            $status = $this->flag_resolved;
            $title = __('Connection to Wordpress.org was successful', $this->text_domain);
            $desc = '';
        }
        $result = $this->format_result($status, $title, $desc);

//@todo		oc_sh_save_result( 'wp_connection', $result[ $this->status_key ] );

        return $result;
    }

    public function check_connection()
    {
        include ABSPATH . WPINC . '/version.php';

        $guide_link = sprintf("<a href='https://help.one.com/hc/%s' target='_blank'>", onecom_generic_locale_link('', get_locale(), 1));
        $title = __('Connection to Wordpress.org', $this->text_domain);
        try {
            if (function_exists('get_core_checksums')) {
                $checksums = get_core_checksums($wp_version, 'en_US');
            } else {
                // if the function does not exist at this point. simply mark this test as success. Chances of actual failure are negligible on production.
                $checksums = true;
            }

        } catch (Exception $e) {
            $status = $this->flag_open;
            $desc = sprintf(__('Your site could not connect to wordpress.org, which means background updates may not be working properly. %Contact support%s', $this->text_domain), $guide_link, "</a>");

            return $this->format_result($status, $title, $desc);
        }
        if (!$checksums) {
            $status = $this->flag_open;
            $desc = sprintf(__('Your site could not connect to wordpress.org, which means background updates may not be working properly. %Contact support%s', $this->text_domain), $guide_link, "</a>");
        } else {
            $status = $this->flag_resolved;
            $title = __('Background updates are working properly.', $this->text_domain);
            $desc = '';
        }

        return $this->format_result($status, $title, $desc);

    }

    public function check_auto_updates()
    {

        $this->log_entry('Checking if CORE updates can be carried out');
        $core_updates_disabled = false;
        if (defined('WP_AUTO_UPDATE_CORE') && WP_AUTO_UPDATE_CORE === false) {
            $core_updates_disabled = true;
        }
        $this->log_entry('Checking connections to wordpress.org');
        $wp_org_connection = $this->check_connection();
        if ($wp_org_connection[$this->status_key] == $this->flag_open) {
            $this->log_entry('Could not connect to wordpress.org');
        }

        if ($core_updates_disabled) {
            $result = $this->format_result($this->flag_open);
        } else {
            $result = $this->format_result($this->flag_resolved);
        }

//@todo		oc_sh_save_result( 'auto_updates', $result[ $this->status_key ] );

        return $result;
    }

    public function get_plugin_name(string $file = '', array $plugins = []): string
    {
        $name = $file;
        foreach ($plugins as $key => $plugin) {
            if (strpos($key, $file) === 0) {
                $name = $plugin['Name'];
            }
        }

        return $name;
    }
}