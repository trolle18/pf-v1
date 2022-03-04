<?php


class OnecomSpSettings extends OnecomSp
{

    public $is_premium;

    public function __construct()
    {

        add_action('admin_menu', array($this, 'oc_sp_admin_page'));
        $base = new OnecomSp();
        $this->is_premium = onecomsp_is_premium();

    }

    public function oc_sp_admin_page()
    {

        $menu_title = __("Spam Protection", $this->text_domain);
        add_menu_page($menu_title, $menu_title, 'manage_options', 'onecom-wp-spam-protection', array($this, 'sp_settings_page'), 'dashicons-shield' );


        add_submenu_page('onecom-wp-spam-protection',
            __('Spam Summary',$this->text_domain),
            '<span id="onecom_spam_protection">' . __('Spam Summary' , $this->text_domain) . '</span>',
            'manage_options',
            'onecom-wp-spam-protection',
            array($this, 'sp_settings_page'));

    }


    public function sp_settings_page()
    {

        $spamlogs = oc_get_sp_options('onecom_sp_spam_logs');
        $total_count = isset($spamlogs['spam_count']) ? $spamlogs['spam_count'] : 0;
        $comments = [];
        $registration = [];
        $failed_login = [];
        $other = [];

        if (isset($spamlogs['records']) && is_array($spamlogs['records'])) {

            foreach ($spamlogs['records'] as $record) {


                if (strpos($record[3], 'wp-comments-post.php') !== false) {

                    $comments[] = $record[3];
                    unset($record[3]);

                } elseif (strpos($record[3], 'action=register') !== false) {
                    $registration[] = $record[3];
                    unset($record[3]);

                } elseif (strcmp('/wp-login.php', $record[3]) == 0) {

                    $failed_login[] = $record[3];
                    unset($record[3]);


                } else {
                    $other[] = $record[3];
                }

            }

        }

        $disabled = (!$this->is_premium) ? 'oc-show-modal' : '';
        $disabled_class = (!$this->is_premium) ? 'disabled-section' : '';
        ?>
        <div class="one-sp-wrap wrap" id="onecom-sp-ui">

            <?php if (!$this->is_premium && function_exists('onecom_premium_theme_admin_notice')){
                onecom_premium_theme_admin_notice();
            }?>


            <div class="one-sp-body">
                <div class="wrap-top-onecom-heading-desc">
                    <h1 class="one-title"><?php echo  __('Health and Security Tools', OC_SP_TEXTDOMAIN); ?></h1>
                    <p class="onecom-main-desc"><?php echo  __('Monitor the essential security and performance checkpoints and fix them if needed.', OC_SP_TEXTDOMAIN); ?></p>
                </div>

                <div class="one-sp-inner-container">
                    <div class="inner_header">
                        <div class="inner_header_left">
                            <div class="oc-flex-center oc-icon-box">
                                <div class="mini-wrp">
                                <img src="<?php echo ONECOM_SP_WP_URL.'/assets/images/spam_protection.svg' ?>" alt="" class="onecom-heading-icon">
                                    <span>Pro</span>
                                </div>

            <?php echo OnecomSp::sp_admin_head(__('Spam Protection', OC_SP_TEXTDOMAIN).'<span>Pro</span>',''); ?>
                            </div>
                            <p><?php echo  __('Protect your website from spambots commenting or registering on it.', OC_SP_TEXTDOMAIN); ?></p>

                        </div>
                        <div class="inner_header_right">
                            <div class="onecom_card">
                                <span class="onecom_card_title"><?php _e('Blocked spam attempts', OC_SP_TEXTDOMAIN) ?></span>
                                <span id="onecom_card_blocked" class="onecom_card_value"><?php echo $total_count ?></span>
                            </div>
                        </div>
                    </div>
                        <div class="h-parent-wrap">
        <div class="h-parent">
            <div class="h-child">
                <div class="onecom_tabs_container <?php echo $disabled_class ?> " >
                    <div class="onecom_tab active" data-tab="blocked"><?php echo __( 'Blocked', OC_SP_TEXTDOMAIN ) ?>
                        </div>
                    <div class="onecom_tab" data-tab="logs"><?php echo __( 'Logs', OC_SP_TEXTDOMAIN ) ?></div>
                    <div class="onecom_tab" data-tab="spam_diagnostics"><?php echo __( 'Spam diagnostics', OC_SP_TEXTDOMAIN ) ?></div>
                    <div class="onecom_tab" data-tab="settings"><?php echo __( 'Settings', OC_SP_TEXTDOMAIN ) ?>
                    </div>
                    <div class="onecom_tab" data-tab="advanced_settings"><?php echo __( 'Advanced settings', OC_SP_TEXTDOMAIN ) ?>
                    </div>
                </div>
            </div>
        </div>
        </div>
        <div class="onecom_tabs_panels ">
            <?php if(!$this->is_premium){ ?>

        <div class="oc-sp-non-mwp">
                <img src="<?php echo ONECOM_SP_WP_URL ?>assets/images/beginner-icon.svg"
                     alt="<?php echo __('Get access to Spam Protection and more for free with Managed Wordpress.', OC_SP_TEXTDOMAIN); ?>">
                <p><?php echo sprintf(__('Get access to Spam Protection and more for free with Managed Wordpress.%s %sGet Started%s', OC_SP_TEXTDOMAIN),'<br>','<a class="oc-show-modal" href="'.oc_upgrade_link('top_banner').'" target="_blank" style="font-weight:600;text-decoration:none;">','</a>'); ?></p>

        </div>



        </div>
                </div>
            </div>
        </div>

            <?php return false;}  ?>

            <div class="onecom_tabs_panel blocked" id="blocked">

                    <div class="oc-summary-body">
                    <div class="filter-summary">
                        <select class="oc-duration-filter">
                            <option  data-duration="24hours"><?php _e('Last 24 hours', OC_SP_TEXTDOMAIN) ?></option>
                            <option  data-duration="7days"><?php _e('Last week', OC_SP_TEXTDOMAIN) ?></option>
                            <option  data-duration="30days"><?php _e('Last month', OC_SP_TEXTDOMAIN) ?></option>
                        </select>
                        <span id="oc_switch_spinner" class="oc_cb_spinner spinner"></span>

                        <ul>
                            <li><span class="sp-success"></span><span
                                        class="oc_comment_count"><?php echo count($comments) . '</span> ' . __('spam comments blocked', OC_SP_TEXTDOMAIN) ?>
                                    <a class="oc-review"  href="<?php echo admin_url('edit-comments.php?comment_status=spam') ?>">Review</a>
                            </li>
                            <li><span class="sp-success"></span><span
                                        class="oc_registration_count"><?php echo count($registration) . '</span> ' . __('spam registrations blocked', OC_SP_TEXTDOMAIN) ?>
                            </li>
                            <li><span class="sp-success"></span><span
                                        class="oc_failed_login_count"><?php echo count($failed_login) . '</span> ' . __('failed logins blocked', OC_SP_TEXTDOMAIN) ?>
                            </li>
                            <li><span class="sp-success"></span><span
                                        class="oc_other_count"><?php echo count($other) . '</span> ' . __('other spams blocked', OC_SP_TEXTDOMAIN) ?>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>

            <div class="onecom_tabs_panel logs oc_hidden" id="logs">
                <?php $this->spam_logs_page(); ?>
            </div>
            <div class="onecom_tabs_panel spam_diagnostics oc_hidden" id="spam_diagnostics">
                <?php $this->diagnostics_page(); ?>
            </div>
            <div class="onecom_tabs_panel settings oc_hidden" id="settings">
                <?php $this->protection_settings_page(); ?>
            </div>
            <div class="onecom_tabs_panel advanced_settings oc_hidden" id="advanced_settings">
                <?php $this->blocked_lists_page(); ?>
            </div>
        </div>
            </div>

        </div>


        <?php
    }


    public function protection_settings_page()
    {
        include_once ONECOM_PLUGIN_PATH . 'inc/templates/oc-sp-protect-options.php';
    }

    public function blocked_lists_page()
    {

        include_once ONECOM_PLUGIN_PATH . 'inc/templates/oc-sp-blocked-lists.php';

    }

    public function spam_logs_page()
    {

        include_once ONECOM_PLUGIN_PATH . 'inc/templates/oc-sp-logs.php';

    }

    public function diagnostics_page()
    {

        include_once ONECOM_PLUGIN_PATH . 'inc/templates/oc-diagnostics.php';

    }
}
