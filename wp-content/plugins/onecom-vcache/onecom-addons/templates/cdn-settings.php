<?php
$oc_vache = new OCVCaching();

$dev_mode_checked = '';
$exclude_cdn_checked = '';
$dev_mode_duration = $oc_vache->oc_json_get_option('onecom_vcache_info', 'dev_mode_duration');
$oc_dev_mode_status = $oc_vache->oc_json_get_option('onecom_vcache_info', 'oc_dev_mode_enabled');
$oc_exclude_cdn_data = $oc_vache->oc_json_get_option('onecom_vcache_info', 'oc_exclude_cdn_data');
$oc_exclude_cdn_status = $oc_vache->oc_json_get_option('onecom_vcache_info', 'oc_exclude_cdn_enabled');
$premium_inline_msg = apply_filters('onecom_premium_inline_badge', '', __("Premium feature", OCVCaching::textDomain), 'mwp');

if ($oc_vache->oc_premium() === true) {
    $wrap_premium_class = 'oc-premium';
} else {
    $wrap_premium_class = 'oc-non-premium';
}

if ($oc_dev_mode_status == "true") {
    $dev_mode_checked = 'checked';
} else {
    $dev_mode_checked = '';
}

if ($oc_exclude_cdn_status == "true") {
    $exclude_cdn_checked = 'checked';
} else {
    $exclude_cdn_checked = '';
}

$cdn_enabled = get_site_option('oc_cdn_enabled');

$cdn_icon = $oc_vache->OCVCURI . '/assets/images/cdn-icon.svg';

?>
<!-- Main Wrapper -->
<div class="wrap <?php echo $wrap_premium_class; ?>" id="onecom-wrap">

    <!-- Important placeholder for one.com notifications -->
    <div class="onecom-notifier"></div>

    <!-- Upsell banner for unmanaged package -->
    <?php
    if (!$oc_vache->oc_premium() && function_exists('onecom_premium_theme_admin_notice')) {
        onecom_premium_theme_admin_notice();
    }
    ?>

    <!-- Page Header -->
    <div class="oc-page-header">
        <h1 class="main-heading">
            <?php _e('Performance Tools', OCVCaching::textDomain); ?>
        </h1>

        <div class="page-description">
            <?php
            _e('Tools to help you improve your website’s performance', OCVCaching::textDomain);
            ?>
        </div>
    </div>

    <!-- Main content -->
    <div class='inner-wrap'>
        <div class='oc-row oc-cdn'>
            <div class='oc-column oc-left-column'>
                <div class="oc-flex-center oc-icon-box">
                    <img id="oc-performance-icon" width="48" height="48" src="<?php echo $cdn_icon ?>" alt="one.com" />
                    <h2 class="main-heading"> <?php _e('Content Delivery Network', OCVCaching::textDomain); ?> </h2>
                </div>
                <p>
                    <?php _e('A content delivery network (CDN) is a system of distributed servers that deliver pages and other web content to a user, based on the geographic locations of the user, the origin of the webpage and the content delivery server. This is especially useful if you have a lot of visitors spread across the globe.', OCVCaching::textDomain); ?> <a href="<?php echo wp_nonce_url(add_query_arg($oc_vache->purgeCache, 'cdn'), $oc_vache->plugin); ?>" title="Purge CDN">
                        <?php echo __('Purge CDN', $oc_vache->plugin); ?></a>
                </p>
            </div>
            <div class='oc-column oc-right-column'>
                <div class="pc-settings">

                    <div class="oc-block">
                        <label for="cdn_enable" class="oc-label">
                            <span class="oc_cb_switch">
                                <input type="checkbox" class="" id="cdn_enable" name="show" value=1 <?php echo $cdn_enabled == 'true' ? 'checked' : '' ?> />
                                <span class="oc_cb_slider" data-target="oc-cdn-icon"></span>
                            </span>
                            <?php echo __("Enable CDN", OCVCaching::textDomain); ?>
                        </label><span id="oc_cdn_switch_spinner" class="oc_cb_spinner spinner"></span>
                    </div>


                    <div class="oc-cdn-feature-box oc-block" style="display:<?php echo $cdn_enabled === 'true' ? 'block' : 'none' ?>;">

                        <div class="oc-block">
                            <label for="dev_mode_enable" class="oc-label">
                                <span class="oc_cb_switch">
                                    <input type="checkbox" class="" id="dev_mode_enable" name="show" value=1 <?php echo $dev_mode_checked; ?> />
                                    <span class="oc_cb_slider" data-target="oc-cdn-icon"></span>
                                </span>
                                <?php echo __("Development mode", OCVCaching::textDomain); ?>
                            </label><span id="oc_dev_mode_switch_spinner" class="oc_cb_spinner spinner"></span>

                            <div class="mwp-promo">
                                <?php
                                // If non-mWP & dev mode disabled, show promo
                                if ($oc_vache->oc_premium() === false && $oc_dev_mode_status !== 'true') {
                                    echo $oc_vache->mwp_promo();
                                }
                                ?>
                            </div>
                            <div id="dev_mode_enable_settings" style="display:<?php echo $dev_mode_checked === 'checked' ? 'block' : 'none' ?>;">

                                <form method="post" action="options.php">
                                    <label class="oc_vcache_ttl_label"><?php _e('End development mode (hours)', OCVCaching::textDomain) ?><span class="tooltip"><span class="dashicons dashicons-info"></span><span class="tip-content top"><?php echo __('CDN will not work for logged-in users until development mode is active.', OCVCaching::textDomain) ?><i aria-hidden="true"></i></span></span></label>
                                    <div class="oc-input-wrap">
                                        <input type="text" min="0" max="720" class="" id="dev_mode_duration" value="<?php echo $dev_mode_duration ?>">

                                        <p class="oc_vcache_decription"><?php echo __("Development mode will get disabled after entered duration", OCVCaching::textDomain); ?></p>

                                        <div class="oc-flex-center save-box">

                                            <button type="button" id="oc_dev_duration_save" class="oc-btn oc-btn-primary oc_dev_mode_btn"><?php _e('Save', OCVCaching::textDomain) ?></button>
                                            <span id="oc_dev_duration_spinner" class="oc_cb_spinner spinner"></span>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="">
                            <label for="exclude_cdn_enable" class="oc-label">
                                <span class="oc_cb_switch">
                                    <input type="checkbox" class="" id="exclude_cdn_enable" name="show" value=1 <?php echo $exclude_cdn_checked; ?> />
                                    <span class="oc_cb_slider" data-target="oc-cdn-icon"></span>
                                </span>
                                <?php echo __("Exclude from CDN", OCVCaching::textDomain); ?>
                            </label><span id="oc_exclude_cdn_switch_spinner" class="oc_cb_spinner spinner"></span>
                            
                            <div class="mwp-promo">
                            <?php
                                // If non-mWP & dev mode disabled, show promo
                                if ($oc_vache->oc_premium() === false && $oc_dev_mode_status !== 'true') {
                                    echo $oc_vache->mwp_promo();
                                }
                                ?>
                            </div>
                            <div id="exclude_cdn_enable_settings" style="display:<?php echo $exclude_cdn_checked === 'checked' ? 'block' : 'none' ?>;">

                                <form method="post" action="options.php">
                                    <label class="oc_vcache_ttl_label">
                                        <?php _e('Specify files and folders to be excluded (one per line)', OCVCaching::textDomain) ?><span class="tooltip"><span class="dashicons dashicons-info"></span><span class="tip-content top"><?php
                                                                                                                                            echo __('Specify files and folders you wish to exclude from CDN. For example:', OCVCaching::textDomain) ?>
                                                <br />.css
                                                <br />uploads
                                                <br />uploads/2021/02/sample.png
                                                <br />themes/assets/js/
                                                <i aria-hidden="true"></i></span></span></label>
                                    <div class="oc-input-wrap">
                                        <textarea id="exclude_cdn_data" placeholder=".css
uploads
uploads/2021/02/sample.png
themes/assets/js/
"><?php echo $oc_exclude_cdn_data ?></textarea>
                                    </div>
                                    <div class="oc-flex-center save-box">
                                        <button type="button" id="oc_exclude_cdn_data_save" class="oc-btn oc-btn-primary oc_cdn_exclude_btn"><?php _e('Save', OCVCaching::textDomain) ?></button>
                                        <span id="oc_exclude_cdn_data_spinner" class="oc_cb_spinner spinner"></span>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>
<div class="clear"></div>