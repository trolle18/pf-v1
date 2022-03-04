<?php
$oc_vache = new OCVCaching();
$wp_rocket_link = 'https://wp-rocket.me/one-and-wp-rocket/?utm_campaign=one-benefits&utm_source=one&utm_medium=partners';
if ($oc_vache->oc_premium()) {
    $wrap_premium_class = 'oc-premium';
} else {
    $wrap_premium_class = 'oc-non-premium';
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
        <div class='oc-row oc-wp-rocket'>
            <div class="oc-flex">
                <div class="inner-left-column">
                    <img width="160" height="160" src="<?php echo $oc_vache->OCVCURI . '/assets/images/wp-rocket-dark.svg'; ?>" alt="<?php _e('WP Rocket', OCVCaching::textDomain) ?>">
                </div>
                <div class="inner-right-column">
                    <div class="oc-flex-center">
                        <div class="oc-hide">
                            <img width="160" height="160" src="<?php echo $oc_vache->OCVCURI . '/assets/images/wp-rocket-dark.svg'; ?>" alt="<?php _e('WP Rocket', OCVCaching::textDomain) ?>">
                        </div>
                        <h2 class="">
                            <?php _e('WP Rocket', OCVCaching::textDomain) ?>
                        </h2>
                        <a href="<?php echo $wp_rocket_link; ?>" title="<?php _e('Get WP Rocket with -20% discount', OCVCaching::textDomain) ?>" target="_blank" class="oc-discount-badge">
                        -20% <?php _e('discount', OCVCaching::textDomain) ?>
                        </a>
                    </div>
                    <div class="oc-flex oc-content-column">
                        <div class="oc-left-column">
                            <p><?php _e('WP Rocket is the most powerful caching plugin in the world. Use it to improve the speed of your WordPress site. SEO ranking and conversions. No coding required.', OCVCaching::textDomain) ?></p>
                            <p>
                                <?php _e('WP Rocket is a trusted one.com partner and works seamlessly with one.com plugin and service offering to create a well-rounded package.', OCVCaching::textDomain) ?>
                            </p>
                            <p>
                                <strong><?php _e('High Performance', OCVCaching::textDomain) ?>:</strong> <br />
                                <?php _e('WP Rocket instantly improves your site’s performance and Core Web Vitals scores.', OCVCaching::textDomain) ?>
                            </p>
                            <p>
                                <strong><?php _e('Easy to Use', OCVCaching::textDomain) ?>:</strong> <br />
                                <?php _e('WP Rocket automatically applies the 80% of web performance best practices.', OCVCaching::textDomain) ?>
                            </p>
                        </div>
                        <div class='oc-column oc-right-column'>
                            <div class="wp-rocket-btn">
                                <a href="<?php echo $wp_rocket_link; ?>" title="<?php _e('Get WP Rocket', OCVCaching::textDomain) ?>" target="_blank" class="oc-btn oc-btn-primary">
                                    <?php _e('Get WP Rocket', OCVCaching::textDomain) ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="clear"></div>