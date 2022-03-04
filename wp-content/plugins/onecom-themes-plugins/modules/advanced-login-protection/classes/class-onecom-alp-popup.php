<?php

defined("WPINC") or die(); // No Direct Access

/**
 * Class Onecom_ALP_Popup
 * Show ALP Popup after 2 months if I'll do later chosen by user
 */
class Onecom_ALP_Popup
{

    public function init()
    {
        // Retun if non-admin or ALP is already enabled
        $login_masking = (int)get_site_option('onecom_login_masking', 0);
        $user = wp_get_current_user();
        if (
            !in_array('administrator', $user->roles)
            || $login_masking > 0
        ) {
            return;
        }

        // Show ALP popup for each new login session
        $alp_popup_info = get_user_meta(get_current_user_id(), 'onecom_alp_popup_info', true);

        // replace old popup values with new one.
        if ( isset( $alp_popup_info['never_show'] )
             && $alp_popup_info['never_show'] == '1' ) {
            update_user_meta( get_current_user_id(), 'onecom_alp_popup_info', array( 'cancel_action_time' => current_time( 'timestamp' ) ) );
        }
        if (
            empty( $alp_popup_info )
            //if never show exists//
            || ( isset( $alp_popup_info['never_show'] )
                 && $alp_popup_info['never_show'] != '1' )
            // if the time when user cancelled popup is more than 2 months
            || (
                isset( $alp_popup_info['cancel_action_time'] )
                && (  $alp_popup_info['cancel_action_time']  < strtotime( '-60 days' ) )
            )
        )
        {
            add_action('admin_footer', [$this, 'show_popup']);
            add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
            add_action('wp_ajax_update_popup_info', [$this, 'update_popup_info']);
        }

    }

    // Load scripts on all admin screen
    public function enqueue_scripts()
    {
        wp_enqueue_style('oc_alp_style', ONECOM_WP_URL . 'modules/advanced-login-protection/assets/css/alp.css');
        wp_enqueue_script('oc_alp_script', ONECOM_WP_URL . 'modules/advanced-login-protection/assets/js/alp.js', ['jquery'], null, true);
    }

    /**
     * Function to output html of Login masking popup.
     * @param void
     * @return void
     */
    public function show_popup()
    {
	    echo '<div id="oc_login_masking_overlay">
            <div id="oc_login_masking_overlay_wrap">

                <div class="oc-bg-white_login_masking">
                <span class="oc_login_masking_close"><img src="' . ONECOM_WP_URL . '/assets/images/close.svg"></span>
                    <div id="oc_um_head_login_masking">
                        <h5>' . __( 'Improve your website security', OC_PLUGIN_DOMAIN ) . '</h5>
                    </div>
                    <div id="oc_um_body_login_masking">' . sprintf( __( 'Enabling Advanced Login Protection in one.com Control Panel will improve website security and minimize the risk of getting hacked. %sLearn more%s.', OC_PLUGIN_DOMAIN ), '<a href="https://help.one.com/hc/en-us/articles/4410417270417-What-is-Advanced-Login-Protection-for-WordPress" target="_blank">', '</a>' ) . '</div>
                    <div id="oc_um_footer_login_masking">
                        <a href="' . OC_CP_LOGIN_URL . '&utm_source=onecom_wp_plugin&utm_medium=login_masking" target="_blank" class="oc_um_btn oc_up_btn">' . __( 'Go to control panel', OC_PLUGIN_DOMAIN ) . '</a><a href="javascript:;" class="oc_um_btn oc_cancel_btn">' . __( "I’ll do this later", OC_PLUGIN_DOMAIN ) . '</a>
                    </div>
                </div>
            </div>
        </div>';
    }

    // Update popup info in db
    function update_popup_info()
    {
        $alp_popup_info = array();
        $alp_popup_info['cancel_action_time'] = (int)$_POST['cancel_action_time'];
        $status = update_user_meta(get_current_user_id(), 'onecom_alp_popup_info', $alp_popup_info);
        if ($status) {
            wp_send_json([
                'status'  => 'success',
                'message' => __('Saved. ALP popup shown for current login session.')
            ]);
        } else {
            wp_send_json([
                'status'  => 'failed',
                'message' => __('Failed to save settings. Please reload the page and try again.')
            ]);
        }
    }
}
