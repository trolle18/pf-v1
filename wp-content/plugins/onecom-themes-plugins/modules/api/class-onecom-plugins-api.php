<?php

// block direct access
if ( ! defined( 'ABSPATH' ) ) {
    die();
}
// load categories mapping
require_once __DIR__.'/trait-api-response.php';
require_once ONECOM_WP_PATH . '/modules/health-monitor/traits/trait-onecom-checks-list.php';
require_once ONECOM_WP_PATH . '/modules/health-monitor/traits/trait-onecom-check-category.php';

/*
 * API class
 * */

class OnecomPluginsApi extends WP_REST_Controller {
    use OCAPIResponseTrait, OnecomCheckCategory, OnecomChecksList;

    public $errorTemplate = array(
        'error'   => true,
        'data'    => null,
        'message' => 'Some error occurred.',
        'code'    => 501
    );

    public $itemTemplate = array(
        'id'          => '',
        'title'       => "Title of the bullet",
        'description' => "Description of the bullet",
        'category'    => "Performance",
        'issue'       => 0,
    );

    public $ocPluginsPage = "admin.php?page=onecom-wp-plugins";
    public $ocvmPage = "admin.php?page=onecom-wp-health-monitor";
    private $login_masking_key = 'onecom_login_masking';

    /**
     * Register the routes for the objects of the controller.
     */
    public function register_routes() {
        $namespace = 'onecom-plugins/v' . ONECOM_PLUGIN_API_VERSION;
        register_rest_route( $namespace, '/get', array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_status' ),
                    'permission_callback' => "__return_true",
                    'args'                => array(),
                ),
                array(
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => array( $this, 'create_item' ),
                    'permission_callback' => '__return_true',
                    'args'                => $this->get_endpoint_args_for_item_schema( true ),
                ),
            )
        );
    }

    /**
     * Get Performance cache status
     * @return int
     */
    public function get_pcache_status(): int {
        return "true" === get_site_option( 'varnish_caching_enable', 'false' ) ? 1 : 0;
    }

    /**
     * Get Error page status
     * @return int
     */
    public function get_error_page_status(): array {
        $error_page       = new Onecom_Error_Page();
        $error_class_path = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'fatal-error-handler.php';
        $status           = ( file_exists( $error_class_path ) && $error_page->is_onecom_plugin() ) ? 1 : 0;

        return array(
            "status"  => $status,
            "wp_page" => "admin.php?page=onecom-wp-error-page",
        );
    }

    /**
     * Get get_restricted_uploads_status
     * @return array
     */
    public function get_restricted_uploads_status(): array {
        $extensions = new OnecomFileSecurity();
        $files      = $extensions->get_htaccess_extensions();

        return array(
            'status'  => (int) ! empty( $files ),
            'files'   => $files,
            'wp_page' => "admin.php?page=onecom-wp-health-monitor",
        );
    }

    /**
     * Get get_uc_status
     * @return array
     */
    public function get_uc_status(): array {
        $template = array(
            "status" => 1,
            "wp_page" => "admin.php?page=onecom-wp-under-construction"
        );


        if (!is_plugin_active("onecom-under-construction/onecom-under-construction.php")) {
            return array(
                'status' => 0,
                'wp_page' => $this->ocPluginsPage
            );
        }

        $settings = (array)get_site_option("onecom_under_construction_info");
        if (
            empty($settings) ||
            "" == $settings ||
            !array_key_exists("uc_status", $settings) ||
            "on" !== $settings["uc_status"]
        ) {
            $template["status"] = 0;
            return $template;
        }

        return $template;
    }

    /**
     * Get get_spam_protection_status
     * @return array
     */
    public function get_spam_protection_status(): array {
        if ( ! is_plugin_active( "onecom-spam-protection/onecom-spam-protection.php" ) ) {
            return array(
                'status'  => 0,
                'wp_page' => $this->ocPluginsPage
            );
        }

        return array(
            "status"  => 1,
            "wp_page" => "admin.php?page=onecom-wp-spam-protection"
        );
    }

    /**
     * @param $path
     *
     * @return false|string
     * removed return type since string|bool is not supported in php 7.4
     */
    public function get_plugin_version($path) {

        if(  function_exists('get_file_data') ) {

            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            $file_path   = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $path;
            $plugin_data = get_file_data( $file_path, array(
                'Version' => 'Version'
            ) );

            if ( ! empty( $plugin_data['Version'] ) ) {
                return  $plugin_data['Version'];
            }

        }

        return false;

    }

    /**
     * Get new CDN page URL after redesign
     * @return string
     */
    public function get_cdn_page(): string {
        if (version_compare( $this->get_plugin_version( "onecom-vcache/vcaching.php" ), '2.0.0', '>=' ) ) {
            return 'admin.php?page=onecom-cdn';
        }
        return 'admin.php?page=onecom-vcache-plugin';
    }

    /**
     * Get Performance cache wp-admin page url
     * @return string
     */
    public function get_pcache_page(): string {
        return 'admin.php?page=onecom-vcache-plugin';
    }

    /**
     * Get Health monitor wp-admin page url
     * @return string
     */
    public function get_health_monitor_page(): string {
        return 'admin.php?page=onecom-wp-health-monitor';
    }

    /**
     * Get CDN cache status
     * @return int
     */
    public function get_cdn_status(): int {
        return "true" === get_site_option( 'oc_cdn_enabled', 'false' ) ? 1 : 0;
    }

    /**
     * Get action name based on action slug
     * @return array
     */
    public function get_health_monitor_action_name( $key, $state = 0 ): array {

        // get all checks and their categories from Health monitor's traits
        $checksArr = $this->onecom_get_checks();
        $ignored_checks = get_site_option('oc_marked_resolved', []);
        if (empty($ignored_checks)){
            $ignored_checks = [];
        }
        $this->init_trait_category( 1 );
        $category = [];
        foreach ( $checksArr as $check ) {
            $cat                = $this->get_check_category( $check, 0 );
            $category[ $cat ][] = $check;
        }
        // if the check is ignored, flag should be 0
        if (in_array(str_replace(['ocsh_check_', 'check_'], '', $key), $ignored_checks)){
            $state = 0;
        }else{
            $state = (int) ( ( 2 == $state ) ? 0 : $state );
        }
        $item                 = $this->itemTemplate;
        $item['id']           = $key;
        $item['category']     = false === array_search( $key, $category["Performance"], true ) ? "Security" : "Performance";
        $item['title']        = $key . "_title_" . $state;
        $item['description']  = $key . "_desc_" . $state;
        $item['needs_action'] = $state;
        $item['issue']        = ( ! empty( $category["Critical"] ) && ( 0 === $state || false === array_search( $key, $category["Critical"], true ) ) ) ? $state : 1;

        return $item;
    }

    /**
     * Force scan health monitor
     * @return void
     */
    public function health_monitor_scan(): void {

        require_once ONECOM_WP_PATH . '/inc/functions.php';
        require_once trailingslashit( plugin_dir_path( __DIR__ ) ) . "health-monitor/inc/functions.php";

        $HMAjax = new OnecomHealthMonitorAjax();
        $HMAjax->uploads_index_cb();
        $HMAjax->options_table_count();
        $HMAjax->staging_time();
        $HMAjax->backup_zips();
        $HMAjax->performance_cache();
        $HMAjax->enable_cdn();
        $HMAjax->updated_long_ago();
        $HMAjax->pingbacks();
        $HMAjax->logout_duration();
        $HMAjax->xmlrpc();
        $HMAjax->spam_protection();
        $HMAjax->login_attempts( true );
        $HMAjax->login_recaptcha();
        $HMAjax->user_enumeration();
        $HMAjax->optimize_uploaded_images();
        $HMAjax->error_reporting();
        $HMAjax->usernames();
        $HMAjax->php_updates();
        $HMAjax->plugin_updates();
        $HMAjax->theme_updates();
        $HMAjax->wp_updates();
        $HMAjax->wp_connection();
        $HMAjax->core_updates();
        $HMAjax->check_ssl();
        $HMAjax->file_execution();
        $HMAjax->file_permissions();
        $HMAjax->file_edit();
        $HMAjax->dis_plugin();
        $HMAjax->optimize_uploaded_images();

        if ( class_exists( 'woocommerce' ) ) {
            $HMAjax->woocommerce_session();
        }
    }

    /**
     * Get health monitor status based on score
     * @return string
     *
     */
    public function get_status_on_score( $score ): string {

        // seat belt
        if ( empty( $score ) ) {
            return false;
        }

        // calculate status based on the score
        if ( 75 < $score ) {
            $status = __( "Healthy", OC_PLUGIN_DOMAIN );
        } elseif ( 50 < $score ) {
            $status = __( "Fair", OC_PLUGIN_DOMAIN );
        } else {
            $status = __( "Unhealthy", OC_PLUGIN_DOMAIN );
        }

        return $status;
    }

    /**
     * Get health monitor last scan result from DB
     * @return array
     */
    public function get_health_monitor_recent_results() {
        $cache = get_site_transient( "ocsh_site_scan_result" );
        if ( empty( $cache ) || ! is_array( $cache ) ) {
            return $this->sampleResponse();
        }
        return $cache;
    }

    public function get_vulnerabilities()
    {
        if(!class_exists('OCVMNotifications')){
            require_once ONECOM_WP_PATH . '/modules/vulnerability-monitor/classes/class-ocvm-notifications.php';
        }

        $ocvmNotices = new OCVMNotifications();
        $ocvmNotices->prepareNotifications();

        if(is_countable($ocvmNotices->notices) && count($ocvmNotices->notices)){
            $vulnItem = $this->itemTemplate;
            $vulnItem['id'] = 'vulnerability_exists';
            $vulnItem['title'] = 'vulnerability_exists_title';
            $vulnItem['description'] = 'vulnerability_exists_desc';
            $vulnItem['category'] = 'Security';
            $vulnItem['issue'] = 1;
            $vulnItem['needs_action'] = 1;
            return $vulnItem;
        }

        return false;
    }

    /* Get Health monitor status */
    public function get_health_monitor_status() {

        $health_scan = self::get_health_monitor_recent_results();

        $site_scan_result = oc_sh_calculate_score( $health_scan );
        $status['score']  = round( $site_scan_result['score'] );
        $status['status'] = self::get_status_on_score( $status['score'] );


        // remove the things which are not needed.
        if ( array_key_exists( 'time', $health_scan ) ) {
            $status['last_scan'] = $health_scan["time"];
            unset( $health_scan['time'] );
        }

        $actions = [];
        foreach ( $health_scan as $slug => $state ) {
            if ( is_null( $slug ) || is_null( $state ) ) {
                continue;
            }
            $temp      = $this->get_health_monitor_action_name( $slug, $state );
            $actions[] = $temp;
        }

        // get vulnerability
        $vulns = self::get_vulnerabilities();
        if(false !== $vulns){
            $actions[] = $vulns;
        }

        $status['actions'] = $actions;

        $status['wp_page'] = self::get_health_monitor_page();

        return $status;
    }

    /**
     * WP-admin shortcuts
     * @return array
     */
    public function wp_shortcuts() {
        $links = [];

        $links['customise']['title']   = 'customize_your_site';
        $links['customise']['wp_path'] = 'customize.php';

        $links['add_post']['title']   = 'add_a_blog_post';
        $links['add_post']['wp_path'] = 'post-new.php';

        $links['edit_frontpage']['title'] = 'edit_your_frontpage';

        // assuming the blog page is set as frontpage.
        $links['edit_frontpage']['wp_path'] = 'edit.php';

        // check if static page set as frontpage
        if ( ! empty( get_site_option( 'page_on_front' ) ) ) {
            $links['edit_frontpage']['wp_path'] = 'post.php?post=' . (int) get_site_option( 'page_on_front' );
        }

        $links['view_site']['title']   = 'view_your_site';
        $links['view_site']['wp_path'] = '/';

        $links['add_page']['title']   = 'add_additional_pages';
        $links['add_page']['wp_path'] = 'post-new.php?post_type=page';

        $links['manage_plugins']['title']   = 'manage_plugins';
        $links['manage_plugins']['wp_path'] = 'plugins.php';

        return $links;
    }

    /**
     * Return auto-update status of vulnerability monitor
     * @return array
     */
    public function vulnerability_monitor_status(): array
    {
        $template = array(
            'status'  => 0,
            'wp_page' => $this->ocvmPage
        );

        if(!class_exists('OCVMSettings')){
            require_once ONECOM_WP_PATH . '/modules/vulnerability-monitor/classes/class-ocvm-settings.php';
        }

        $ocvmSettingsObj = new OCVMSettings();
        $ocvmSettings = $ocvmSettingsObj->get();
        if(!empty($ocvmSettings['settings']) && !empty($ocvmSettings['settings'])){
            $template['status'] = $ocvmSettings['settings']['auto_update'];
        }
        return $template;
    }

    /**
     * Get a collection of items
     *
     * @param WP_REST_Request $request Full data about the request.
     *
     * @return WP_Error|WP_REST_Response
     */
    public function get_status( $request ) {

        // backward compatibility
        if ( ! defined( 'REST_REQUEST' ) ) {
            define( 'REST_REQUEST', true );
        }

        // exit if not authenticated
        $error = self::validate_token( $request );
        if ( true !== $error ) {
            return $error;
        }

        // any error occurred?
        $status['error'] = null;

        // PHP version
        $status['data']['php'] = phpversion();

        // WP core version
        $status['data']['wp'] = get_bloginfo( 'version' );

        // One.com plugin exists?
        $status['data']['onecom_plugin_exists'] = ( is_plugin_active( "onecom-themes-plugins/onecom-themes-plugins.php" ) ? 1 : 0 );

        // Favicon
        $status['data']['site_icon'] = get_site_icon_url( 64, includes_url( 'images/w-logo-blue.png' ) );

        // Get Health monitor, Performance cache, CDN, Error page settings
        if ( $status['data']['onecom_plugin_exists'] ) {
            $status['data']['health_monitor']     = self::get_health_monitor_status();
            $status['data']['error_page']         = self::get_error_page_status();
            $status['data']['restricted_uploads'] = self::get_restricted_uploads_status();

            $status['data']['under_construction'] = self::get_uc_status();

            $status['data']['spam_protection'] = self::get_spam_protection_status();
        }


        if ( is_plugin_active( "onecom-vcache/vcaching.php" ) ) {
            $status['data']['cdn']['status']  = self::get_cdn_status();
            $status['data']['cdn']['wp_page'] = self::get_cdn_page();

            $status['data']['cache']['status']  = self::get_pcache_status();
            $status['data']['cache']['wp_page'] = self::get_pcache_page();
        } else {
            $status['data']['cdn']['status']  = 0;
            $status['data']['cdn']['wp_page'] = $this->ocPluginsPage;

            $status['data']['cache']['status']  = 0;
            $status['data']['cache']['wp_page'] = $this->ocPluginsPage;
        }
        //add information about login masking
        $status['data']['login_protection']['status'] = (int) get_site_option( $this->login_masking_key, 0 );

        // vulnerability monitor auto-update state
        $status['data']['vulnerability_monitor'] = self::vulnerability_monitor_status();

        $status['data']['wp_shortcuts']            = self::wp_shortcuts();

        $status["message"] = __( "Success.", OC_PLUGIN_DOMAIN );

        return new WP_REST_Response( $status, 200 );
    }


    /**
     * Check if a given request has access to get items
     *
     * @param WP_REST_Request $request Full data about the request.
     *
     * @return WP_Error|bool
     */
    public function validate_token( $request ) {

        // collect params from request
        $params = $request->get_params();

        // prepare error response
        $err = $this->errorTemplate;

        // check if token received
        if ( ! isset( $params["onecom-auth"] ) || empty( $params["onecom-auth"] ) ) {
            $err['message'] = 'Token missing.';
            $err['code']    = 401;

            return new WP_REST_Response( $err, $err['code'] );
        }

        // check if required functionality exists
        if ( ! class_exists( "OCLAUTH" ) ) {
            $err['message'] = 'Required functionality to handle this request either missing or the plugin is outdated.';

            return new WP_REST_Response( $err, $err['code'] );
        }

        // check token
        $auth  = new OCLAUTH();
        $check = $auth->checkToken( $params["onecom-auth"] );

        // if token was invalid
        if ( ! empty( $check ) && false === $check["error"] ) {
            return true;
        } else if ( ! empty( $check ) && false !== $check["error"] ) {
            $err['message'] = $check["message"];
            $err['code']    = 400;

            return new WP_REST_Response( $err, $err['code'] );
        } else {
            // unknown error case
            $err['message'] = 'Unknown error occurred';
            $err['code']    = 501;

            return new WP_REST_Response( $err, $err['code'] );
        }

    }

    private function get_transient_timeout( string $transient ): int {
        global $wpdb;
        $transient_timeout = $wpdb->get_col( "SELECT option_value FROM $wpdb->options WHERE option_name LIKE '%_transient_timeout_$transient%'" );

        return intval( empty( $transient_timeout[0] ) ? 0 : $transient_timeout[0] ) - time();
    }

    public function create_item( $request ) {

        $params = $request->get_params();
        if(!array_key_exists('login_protection', $params)){
            return;
        }

        $payload = $request->get_param( 'login_protection' );

        $action_item = intval( $payload );
        switch ( $action_item ) {
            case 1:
            case 2:
                return $this->mask_login( $request, $action_item );
            case 0:
                return $this->unmask_login( $request );
        }
    }

    private function mask_login( $request, int $action_item ) {
        update_site_option( $this->login_masking_key, $action_item );

        //block xmlrpc.php
        $xml = new OnecomXmlRpc();
        $xml->fix_check_xmlrpc();

        // remove login_masking check from ignored list
        $ignored = get_site_option('oc_marked_resolved', []);
        if (($key = array_search('login_protection', $ignored)) !== false) {
            unset($ignored[$key]);
            update_option('oc_marked_resolved', $ignored);
        }

        return $this->get_status( $request );
    }

    private function unmask_login( $request ) {
        update_site_option( $this->login_masking_key, 0 );

        // perform action after login masking disable
        do_action( 'disable_onecom_alp');

        //unblock xmlrpc.php
        $xml = new OnecomXmlRpc();
        $xml->undo_check_xmlrpc();

        return $this->get_status( $request );
    }
}
