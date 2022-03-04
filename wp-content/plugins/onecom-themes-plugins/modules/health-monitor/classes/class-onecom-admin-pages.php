<?php

/**
 * Deals with admin pages
 */
class OnecomAdminPages extends OnecomHealthMonitor {

	private $page_name = 'Health Monitor';

	public function init() {
		add_action( 'admin_menu', [ $this, 'report_page' ] );
		add_action( 'network_admin_menu', [ $this, 'report_page' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'page_scripts' ] );
	}

	function report_page() {
		add_submenu_page(
			$this->text_domain,
			__( $this->page_name, $this->text_domain ),
			'<span id="onecom_health_monitor">' . __( $this->page_name, $this->text_domain ) . '</span>',
			'manage_options',
			'onecom-wp-health-monitor',
			[ $this, 'report_page_callback' ],
            -1
		);
	}

	function report_page_callback() {
		include_once $this->module_path . 'templates/oc_sh_health_monitor.php';
	}

	function page_scripts( $hook_suffix ) {
		if ( $hook_suffix === '_page_onecom-wp-health-monitor' || $hook_suffix === 'admin_page_onecom-wp-health-monitor' ) {
			if ( SCRIPT_DEBUG || SCRIPT_DEBUG == 'true' ) {
				$folder      = '';
				$extenstion  = '';
				$script_path = ONECOM_WP_URL . 'modules/health-monitor/assets/';
			} else {
				$folder      = 'min-';
				$extenstion  = '.min';
				$script_path = ONECOM_WP_URL . 'assets/';
			}
            wp_enqueue_script('updates');
			wp_enqueue_style( 'oc_sh_fonts', 'https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap' );
			wp_enqueue_style( 'oc_sh_css', $script_path . $folder . 'css/site-scanner' . $extenstion . '.css' );
			wp_enqueue_script( 'oc_sh_js', $script_path . $folder . 'js/oc_sh_script' . $extenstion . '.js', [
				'jquery',
				'wp-theme-plugin-editor'
			], null, true );
			$cm_settings['codeEditor'] = wp_enqueue_code_editor( array( 'type' => 'shell' ) );
			wp_enqueue_script( 'wp-theme-plugin-editor' );
			wp_enqueue_style( 'wp-codemirror' );
			wp_localize_script( 'oc_sh_js', 'oc_constants', [
				'OC_RESOLVED'         => OC_RESOLVED,
				'OC_OPEN'             => OC_OPEN,
				'ocsh_page_url'       => menu_page_url( 'admin_page_onecom-wp-health-monitor', false ),
				'ocsh_scan_btn'       => __( 'Scan again', $this->text_domain ),
				'nonce'               => wp_create_nonce( HT_NONCE_STRING ),
				'nonce_error'         => __( 'An error occurred. Please reload the page and try again', $this->text_domain ),
				'cm_settings'         => $cm_settings,
				'resetHtaccess'       => base64_encode( '<FilesMatch "\.(php|phtml|php3|php4|php5|pl|py|jsp|asp|html|htm|shtml|sh|cgi|suspected)$">
    deny from all
</FilesMatch>' ),
				'checks'              => $this->checks,
				'error_empty'         => __( "This field cannot be empty", $this->text_domain ),
				'error_empty_sitekey' => __( "Please, enter your site key.", $this->text_domain ),
				'error_length'        => __( "The entered value seems to be incomplete.", $this->text_domain ),
				'ajaxurl'             => $this->onecom_is_premium() ? add_query_arg( [
					'premium' => 1
				], admin_url( 'admin-ajax.php' ) ) : admin_url( 'admin-ajax.php' ),
				'asset_url'           => ONECOM_WP_URL,
				'empty_list_messages' => [
					'todo'    => __( 'Awesome, you completed all recommendations!', $this->text_domain ),
					'done'    => __( 'You haven\'t completed any recommendations. See the <span data-target="todo">To do</span> section.', $this->text_domain ),
					'ignored' => __( 'You haven’t ignored any recommendations.', $this->text_domain )
				],
				'text'                => [
					'unignore'        => __( 'Unignore', $this->text_domain ),
					'ignore'          => __( 'Ignore from future scans', $this->text_domain ),
					'ignore_critical' => __( 'Ignore for 24 hours', $this->text_domain )
				],
				'current_screen'      => get_current_screen()->base,
				'upgrade_modal_text'  => $this->upgrade_modal_text
			] );
		}
	}
}