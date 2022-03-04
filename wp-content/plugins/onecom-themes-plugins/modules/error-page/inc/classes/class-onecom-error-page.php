<?php

class Onecom_Error_Page {
	private $error_class_path = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'fatal-error-handler.php';
	private $local_class_path = ONECOM_WP_PATH . 'modules' . DIRECTORY_SEPARATOR . 'error-page' . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'fatal-error-handler.php';

	public function __construct() {
		if ( ! defined( 'OC_TEXTDOMAIN' ) ) {
			define( 'OC_TEXTDOMAIN', OC_PLUGIN_DOMAIN );
		}
		add_action( 'admin_menu', [ $this, 'menu_pages' ],1);
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'wp_ajax_onecom-error-pages', [ $this, 'configure_feature' ] );
	}

	public function menu_pages() {
		add_submenu_page(
			OC_TEXTDOMAIN,
			__( 'Advanced Error Page', OC_TEXTDOMAIN ),
			'<span id="onecom_errorpage">'.__( 'Advanced Error Page', OC_TEXTDOMAIN ).'</span>',
			'manage_options',
			'onecom-wp-error-page',
			[ $this, 'error_page_callback' ],
			4
		);
	}

	public function enqueue_scripts( $hook_suffix ) {
		if ( $hook_suffix !== '_page_onecom-wp-error-page' ) {
			return;
		}
		$extenstion = '';
		wp_enqueue_script( 'onecom-error-page', ONECOM_WP_URL . '/modules/error-page/assets/js/error-page' . $extenstion . '.js', [ 'jquery' ], null, true );
		wp_enqueue_style( 'onecom-error-page-css', ONECOM_WP_URL . '/modules/error-page/assets/css/error-page' . $extenstion . '.css', null );

		//create object for localize into script
		$LocalizeObj = array(
			'isPremium' => (int) $this->isPremium(),
		);
		$localizeHandleName = 'onecom-error-page';
		wp_localize_script( $localizeHandleName, 'LocalizeObj', $LocalizeObj );
	}

	public function error_page_callback() {
		$checked = ( file_exists( $this->error_class_path ) && $this->is_onecom_plugin() ) ? 'checked' : ''
		?>
        <div class="wrap" id="onecom-ui">
            <?php
            if (!$this->isPremium() && function_exists('onecom_premium_theme_admin_notice')){
                onecom_premium_theme_admin_notice();
            }
            ?>
			<h1 class="one-title"> <span><?php echo __( "Utility Tools", OC_TEXTDOMAIN ); ?> </h1>
			<div class="page-subtitle">
				<?php echo __( "Helpful tools for building and maintaining your site.", OC_TEXTDOMAIN ); ?>
			</div>

            <div class="wrap_inner inner one_wrap oc_card error-page">
                <div class="card-left">
                    <div class="oc-flex-center oc-icon-box">
                        <img width="48" height="48" src="<?php echo ONECOM_WP_URL . '/modules/error-page/assets/img/error-page-icon.svg';?>" alt="one.com">
                        <h2 class="main-heading"> <?php echo __( "Advanced Error Page", OC_TEXTDOMAIN ); ?> </h2>
                    </div>
                    <p class="oc_desc indent"><?php echo __( "Display useful information if there is a problem on your site. This information will be visible only to the admin users.", "onecom-wp" ); ?></p>
                    <div class="show-on-mobile">
                        <div id="onecom-error-preview"
                             class="onecom-error-preview <?php echo ( $checked != '' ) ? 'onecom-error-extended' : '' ?>"></div>
                    </div>
                    <form class="onecom_ep_form">
                        <div class="fieldset">
                            <label for="onecom_ep_enable">
                        <span class="oc_cb_switch">
                            <input type="checkbox" class="" id="onecom_ep_enable" <?php echo $checked ?> name="show"
                                   value=1/>
                            <span class="oc_cb_slider"></span>
                        </span> <span><?php echo __( "Enable tips on error page:", "onecom-wp" ); ?></span>
                                <span id="oc_pc_switch_spinner" class="oc_cb_spinner spinner error-page"></span>
                            </label>
                        </div>
                        <span class="oc_gap"></span>
                    </form>
                </div>
                <div class="card-right hide-on-mobile">
                    <div id="onecom-error-preview"
                         class="onecom-error-preview <?php echo ( $checked != '' ) ? 'onecom-error-extended' : '' ?>"></div>
                </div>
            </div>
        </div>
	<?php }

	public function configure_feature() {
		$action = strip_tags( $_POST['type'] );
		//check if there is an existing file, owned by one.com. If no, bail out
		if ( ! $this->is_onecom_plugin() ) {
			wp_send_json( [
				'status'  => 'failed',
				'message' => __( 'Failed to save settings. Please reload the page and try again.' )
			] );

			return;
		}
		if ( $action === 'enable' ) {
			$response = $this->enable_feature();
		} else {
			$response = $this->disable_feature();
		}
		wp_send_json( $response );
	}

	public function enable_feature() {

		if ( file_exists( $this->error_class_path ) && ( ! $this->is_onecom_plugin() ) ) {
			return [
				'status'  => 'failed',
				'message' => __( 'An error handler is already present!', OC_TEXTDOMAIN )
			];
		}

		if ( copy( $this->local_class_path, $this->error_class_path ) ) {
			$response = [
				'status'  => 'success',
				'message' => __( 'Error page enabled', OC_TEXTDOMAIN )
			];
		} else {
			$response = [
				'status'  => 'failed',
				'message' => __( 'Error page could not be enabled', OC_TEXTDOMAIN )
			];
		}

		return $response;
	}

	public function disable_feature() {
		if ( ! file_exists( $this->error_class_path ) ) {
			return [
				'status'  => 'failed',
				'message' => __( 'No active error pages found', OC_TEXTDOMAIN )
			];
		}
		if ( unlink( $this->error_class_path ) ) {
			$response = [
				'status'  => 'success',
				'message' => __( 'Error page disabled', OC_TEXTDOMAIN )
			];
		} else {
			$response = [
				'status'  => 'failed',
				'message' => __( 'Error page could not be disabled', OC_TEXTDOMAIN )
			];
		}

		wp_send_json( $response );
	}

	public function is_onecom_plugin() {
		if ( ! file_exists( $this->error_class_path ) ) {
			return true;
		}
		$data = get_plugin_data( $this->error_class_path );
		if ( isset( $data['AuthorName'] ) && ( $data['AuthorName'] === 'one.com' ) ) {
			return true;
		}

		return false;
	}
    public function isPremium()
    {
        $features = oc_set_premi_flag();
        if ((isset($features['data']) && (empty($features['data']))) || (in_array('MWP_ADDON', $features['data']) || in_array('ONE_CLICK_INSTALL', $features['data']))
        ) {
            return true;
        }
        return false;
    }
}
