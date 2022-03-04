<?php

declare(strict_types=1);
defined("WPINC") or die(); // No Direct Access

/**
 * Class Onecom_Marketgoo
 * Performs actions related to marketgoo:
 * * marketgoo plugin download url filter
 * * marketgoo token activation if addon subscribed
 */
class Onecom_Marketgoo
{
    const MG_META_API = "https://marketgoo.one.com/wordpress/metadata/one-marketgoo.json";
    const MG_ADDON_API = MIDDLEWARE_URL . "/features/addon/marketgoo";

    private $mg_meta_info;
    private $mg_addon_info;
    private $api_host;
    private $wp_path;
    private $subdomain;

    // Class Constructor
	public function __construct() {}

    public function init(){
        // Override plugin download url via custom filter
        add_filter('onecom_plugin_download_url', [$this, 'download_url_filter'], 10, 2);

        // Enable marketgoo with token upon activation
        add_action('activate_one-marketgoo/one-marketgoo.php', [$this, 'auto_activate_marketgoo']);
    }

    /**
     * Seemless marketgoo enable on plugin activation if addon exists
     */
    public function auto_activate_marketgoo(): void
    {
        // Get marketgoo addon data upon activation step
        $this->mg_addon_info = $this->marketgoo_addon_info(true);

        // check if valid api response & root domain
        if (
            is_array($this->mg_addon_info) &&
            is_null($this->mg_addon_info['error']) &&
            $this->is_root_domain() &&
            $this->mg_addon_info['data']['tier'] === 'MARKETGOO_WORDPRESS' &&
            !empty($this->mg_addon_info['data']['wordpressActivationToken'])
        ) {
            update_site_option('one-marketgoo_token', $this->mg_addon_info['data']['wordpressActivationToken']);
        } else {
            delete_site_option('one-marketgoo_token');
        }
    }

    /**
     *  Custom Filter to Override marketgoo download url
     *  * Use marketgoo api download url if exists, else keep same
     */
    public function download_url_filter($download_link, $plugin_slug): string
    {
        // fetch marketgoo meta for this plugin only
        if ($plugin_slug === 'one-marketgoo') {
            $this->mg_meta_info = $this->marketgoo_meta_info();

            // if required meta found, return latest download url
            if (
                is_array($this->mg_meta_info) &&
                key_exists('download_url', $this->mg_meta_info) &&
                !empty($this->mg_meta_info['download_url'])
            ) {
                return $this->mg_meta_info['download_url'];
            }
        }

        // by default return existing url from plugins.json
        return $download_link;
    }

    function marketgoo_addon_info($force = false, $domain = ''): array
    {
        // check transient
        $marketgoo_addon_info = get_site_transient('onecom_marketgoo_addon_info');
        if (!empty($marketgoo_addon_info) && false === $force) {
            return $marketgoo_addon_info;
        }
        if (!$domain) {
            $domain = isset($_SERVER['ONECOM_DOMAIN_NAME']) ? $_SERVER['ONECOM_DOMAIN_NAME'] : false;
        }
        if (!$domain) {
            return [
                'data' => null,
                'error' => 'Empty domain',
                'success' => false,
            ];
        }
        $totp = oc_generate_totp();
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => self::MG_ADDON_API,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Cache-Control: no-cache",
                "X-Onecom-Client-Domain: " . $domain,
                "X-TOTP: " . $totp,
                "cache-control: no-cache",
            ),
        ));
        $response = curl_exec($curl);
        $response = json_decode($response, true);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return [
                'data' => null,
                'error' => __("Some error occurred, please reload the page and try again.", "validator"),
                'success' => false,
            ];
        } else {
            // save transient for next calls, & return latest response
            set_site_transient('onecom_marketgoo_addon_info', $response, 12 * HOUR_IN_SECONDS);
            return $response;
        }
    }

    /**
     * marketgoo API to fetch download url etc
     */
    public function marketgoo_meta_info(): array
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => self::MG_META_API,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Cache-Control: no-cache",
                "cache-control: no-cache",
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        /**
         * Return error in case of api error, false, or empty response
         * Note: API returns false sometimes
         */
        if (!$err && !empty($response) && $response) {
            $response = json_decode($response, true);
            return $response;
        } else {
            return [
                'data' => null,
                'error' => 'marketgoo api error',
                'success' => false,
            ];
        }
    }

    /**
     *  get api host to allow http_request for external plugin download
     */
    public function get_api_host()
    {
        // extract api host
        $this->api_host = parse_url(self::MG_META_API);
        if (is_array($this->api_host) && !empty($this->api_host['host'])) {
            return preg_replace('/^www\./', '', $this->api_host['host']);
        }

        return false;
    }

    /**
     * check if root domain, to show/activate plugin
     */
    public function is_root_domain(): bool
    {
        // return if possibly non-one.com domain
        if (
            !isset($_SERVER['ONECOM_DOMAIN_NAME'])
            && empty($_SERVER['ONECOM_DOMAIN_NAME'])
            && isset($_SERVER['HTTP_HOST'])
            && !empty($_SERVER['HTTP_HOST'])
        ) {
            return false;
        }

        /**
         *  Returns root domain true if
         *  * if current domain/subdomain match with root domain
         *  * AND (if any) mannual wp installation also matches root domain
         *  * HTTP_HOST returns subdomains (and www.domain.com if root)
         */
        $this->subdomain = preg_replace('/(?:https?:\/\/)?(?:www\.)?(.*)\/?$/i', '$1', OC_HTTP_HOST);
        $this->wp_path = preg_replace('/(?:https?:\/\/)?(?:www\.)?(.*)\/?$/i', '$1', rtrim(get_home_url(), "/"));
        if ($_SERVER['ONECOM_DOMAIN_NAME'] === $this->subdomain
            && $_SERVER['ONECOM_DOMAIN_NAME'] === $this->wp_path
        ) {
            return true;
        }

        return false;
    }
}
