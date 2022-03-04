<?php
$oc_vache = new OCVCaching();

$pc_checked = '';
$performance_icon = $oc_vache->OCVCURI . '/assets/images/pcache-icon.svg';
$varnish_caching = get_site_option(OCVCaching::defaultPrefix . 'enable');
$varnish_caching_ttl = get_site_option('varnish_caching_ttl');
$varnish_caching_ttl_unit = get_site_option('varnish_caching_ttl_unit');

if ($oc_vache->oc_premium() === true) {
    $wrap_premium_class = 'oc-premium';
} else {
    $wrap_premium_class = 'oc-non-premium';
}

if ($varnish_caching == "true") {
    $pc_checked = 'checked';
}

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
        <div class='oc-row oc-pcache'>
            <div class='oc-column oc-left-column'>
                <div class="oc-flex-center oc-icon-box">
                    <img id="oc-performance-icon" width="48" height="48" src="<?php echo $performance_icon ?>" alt="one.com" />
                    <h2 class="main-heading"> <?php _e('Performance Cache', OCVCaching::textDomain) ?> </h2>
                </div>
                <p>
                    <?php _e('With One.com Performance Cache enabled your website loads a lot faster. We save a cached copy of your website on a Varnish server, that will then be served to your next visitors. <br/>This is especially useful if you have a lot of visitors. It may also help to improve your SEO ranking. If you would like to learn more, please read our help article: <a href="https://help.one.com/hc/en-us/articles/360000080458" target="_blank">How to use the One.com Performance Cache for WordPress</a>.', OCVCaching::textDomain); ?>
                </p>
                <p>
                    <?php _e('Performance cache is purged automatically when a post or page is published or modified.', OCVCaching::textDomain); ?> <a href="<?php echo wp_nonce_url(add_query_arg($oc_vache->purgeCache, 1), $oc_vache->plugin); ?>" title="Purge Performance Cache">
                        <?php echo __('Purge Performance Cache', $oc_vache->plugin); ?></a>
                </p>
            </div>
            <div class='oc-column oc-right-column'>
                <div class="pc-settings">

                    <div class="oc-block">
                        <label for="pc_enable" class="oc-label">
                            <span class="oc_cb_switch">
                                <input type="checkbox" id="pc_enable" data-target="pc_enable_settings" name="show" value=1 <?php echo $pc_checked; ?> />
                                <span class="oc_cb_slider" data-target="oc-performance-icon" data-target-input="pc_enable"></span>
                            </span>
                            <?php echo __("Enable Performance Cache", OCVCaching::textDomain); ?>
                        </label><span id="oc_pc_switch_spinner" class="oc_cb_spinner spinner"></span>
                    </div>

                    <div id="pc_enable_settings" style="display:<?php echo $pc_checked === 'checked' ? 'block' : 'none' ?>;">
                        <?php
                            if ($varnish_caching_ttl_unit == 'minutes') {
                                $vc_ttl_as_unit = $varnish_caching_ttl / 60;
                            } else if ($varnish_caching_ttl_unit == 'hours') {
                                $vc_ttl_as_unit = $varnish_caching_ttl / 3600;
                            } else if ($varnish_caching_ttl_unit == 'days') {
                                $vc_ttl_as_unit = $varnish_caching_ttl / 86400;
                            } else {
                                $vc_ttl_as_unit = $varnish_caching_ttl;
                            }
                        ?>
                            <form method="post" action="options.php">
                                <div class="oc-flex-fields">
                                    <div>
                                        <label class="oc_vcache_ttl_label"><?php _e('Cache TTL', OCVCaching::textDomain) ?><span class="tooltip"><span class="dashicons dashicons-info"></span><span class="tip-content right"><?php echo __('The time that website data is stored in the Varnish cache. After the TTL expires the data will be updated, 0 means no caching.', OCVCaching::textDomain) ?><i aria-hidden="true"></i></span></span></label><br />
                                        <input type="text" name="oc_vcache_ttl" class="oc_vcache_ttl" id="oc_vcache_ttl" value="<?php echo $vc_ttl_as_unit; ?>" />

                                    </div>
                                    <div>
                                        <label class="oc_vcache_ttl_label"><?php _e('Frequency', OCVCaching::textDomain) ?>: </label><br />
                                        <select class="oc-vcache-ttl-select" name="oc_vcache_ttl_unit" id="oc_vcache_ttl_unit">
                                            <option value="seconds" <?php if ($varnish_caching_ttl_unit == "seconds") {
                                                                        echo "selected";
                                                                    } ?>><?php _e('Seconds', OCVCaching::textDomain) ?></option>
                                            <option value="minutes" <?php if ($varnish_caching_ttl_unit == "minutes") {
                                                                        echo "selected";
                                                                    } ?>><?php _e('Minutes', OCVCaching::textDomain) ?></option>
                                            <option value="hours" <?php if ($varnish_caching_ttl_unit == "hours") {
                                                                        echo "selected";
                                                                    } ?>><?php _e('Hours', OCVCaching::textDomain) ?></option>
                                            <option value="days" <?php if ($varnish_caching_ttl_unit == "days") {
                                                                        echo "selected";
                                                                    } ?>><?php _e('Days', OCVCaching::textDomain) ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="oc-form-footer">

                                    <p class="oc_vcache_decription"><?php _e('Time to live in Varnish cache', OCVCaching::textDomain) ?></p>
                                    <div class="oc-flex-center save-box">
                                        <button type="button" id="oc_ttl_save" class="oc_vcache_btn no-right-margin oc-btn oc-btn-primary"><?php _e('Save', OCVCaching::textDomain) ?></button>
                                        <span id="oc_ttl_spinner" class="oc_cb_spinner spinner"></span>
                                    </div>
                                </div>
                            </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>
<div class="clear"></div>